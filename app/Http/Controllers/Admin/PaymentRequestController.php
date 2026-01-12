<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = PaymentRequest::with(['user', 'project.order.items.service.category', 'approver']);

        // Filter by division based on admin role
        if ($user->isAgencyAdmin()) {
            // Agency admin: only see payment requests from agency projects
            $query->whereHas('project.order.items.service.category', function($q) {
                $q->where('division', 'agency');
            });
        } elseif ($user->isAcademyAdmin()) {
            // Academy admin: only see payment requests from academy projects
            $query->whereHas('project.order.items.service.category', function($q) {
                $q->where('division', 'academy');
            });
        }
        // Super admin sees all payment requests (no filter)

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(20);

        // Calculate pending count with division filter
        $pendingQuery = PaymentRequest::where('status', 'pending');
        if ($user->isAgencyAdmin()) {
            $pendingQuery->whereHas('project.order.items.service.category', function($q) {
                $q->where('division', 'agency');
            });
        } elseif ($user->isAcademyAdmin()) {
            $pendingQuery->whereHas('project.order.items.service.category', function($q) {
                $q->where('division', 'academy');
            });
        }
        $pendingCount = $pendingQuery->count();

        // Calculate stats with division filter
        $statsQuery = PaymentRequest::query();
        if ($user->isAgencyAdmin()) {
            $statsQuery->whereHas('project.order.items.service.category', function($q) {
                $q->where('division', 'agency');
            });
        } elseif ($user->isAcademyAdmin()) {
            $statsQuery->whereHas('project.order.items.service.category', function($q) {
                $q->where('division', 'academy');
            });
        }

        $stats = [
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
            'total_approved' => (clone $statsQuery)->where('status', 'approved')->sum('approved_amount'),
        ];


        return view('admin.payment-requests.index', compact('requests', 'pendingCount', 'stats'));
    }

    public function show(PaymentRequest $paymentRequest)
    {
        $paymentRequest->load(['user', 'project', 'approver']);
        
        return view('admin.payment-requests.show', compact('paymentRequest'));
    }

    public function update(Request $request, PaymentRequest $paymentRequest)
    {
        if ($paymentRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Permintaan sudah diproses sebelumnya']);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'approved_amount' => 'required_if:action,approve|nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $status = $validated['action'] === 'approve' ? 'approved' : 'rejected';

        $paymentRequest->update([
            'status' => $status,
            'approved_amount' => $validated['action'] === 'approve' ? $validated['approved_amount'] : null,
            'admin_notes' => $validated['admin_notes'] ?? null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // TODO: Send notification to employee

        $message = $status === 'approved' 
            ? 'Permintaan pembayaran berhasil disetujui'
            : 'Permintaan pembayaran ditolak';

        return redirect()->route('admin.payment-requests.index')
            ->with('success', $message);
    }
}
