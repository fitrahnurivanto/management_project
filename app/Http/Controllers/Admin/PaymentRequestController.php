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
        $query = PaymentRequest::with(['user', 'project.order', 'clas', 'approver']);

        // Filter by division based on admin role
        if ($user->isAgencyAdmin()) {
            // Agency admin: only see payment requests from agency projects (has project_id and order.division = agency)
            $query->whereNotNull('project_id')
                  ->whereHas('project.order', function($q) {
                      $q->where('division', 'agency');
                  });
        } elseif ($user->isAcademyAdmin()) {
            // Academy admin: only see payment requests from academy classes (has class_id)
            $query->whereNotNull('class_id');
        }
        // Super admin sees all payment requests (no filter)

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(20)->appends($request->query());

        // Calculate pending count with division filter
        $pendingQuery = PaymentRequest::where('status', 'pending');
        if ($user->isAgencyAdmin()) {
            $pendingQuery->whereNotNull('project_id')
                        ->whereHas('project.order', function($q) {
                            $q->where('division', 'agency');
                        });
        } elseif ($user->isAcademyAdmin()) {
            $pendingQuery->whereNotNull('class_id');
        }
        $pendingCount = $pendingQuery->count();

        // Calculate stats with division filter
        $statsQuery = PaymentRequest::query();
        if ($user->isAgencyAdmin()) {
            $statsQuery->whereNotNull('project_id')
                      ->whereHas('project.order', function($q) {
                          $q->where('division', 'agency');
                      });
        } elseif ($user->isAcademyAdmin()) {
            $statsQuery->whereNotNull('class_id');
        }

        $stats = [
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'paid' => (clone $statsQuery)->where('status', 'paid')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
            'total_approved' => (clone $statsQuery)->whereIn('status', ['approved', 'processing'])->sum('approved_amount'),
            'total_paid' => (clone $statsQuery)->where('status', 'paid')->sum('approved_amount'),
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

    /**
     * Mark payment request as paid (for Finance role)
     */
    public function markAsPaid(Request $request, PaymentRequest $paymentRequest)
    {
        // Hanya payment request yang approved atau processing yang bisa dipaid
        if (!in_array($paymentRequest->status, ['approved', 'processing'])) {
            return back()->withErrors(['status' => 'Hanya payment request dengan status approved/processing yang bisa dibayar']);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $paymentRequest->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
        ]);

        // TODO: Send notification to employee

        return back()->with('success', 'Pembayaran berhasil dicatat. Employee akan menerima notifikasi.');
    }

    /**
     * Mark as processing (ready for payment)
     */
    public function markAsProcessing(PaymentRequest $paymentRequest)
    {
        if ($paymentRequest->status !== 'approved') {
            return back()->withErrors(['status' => 'Hanya payment request approved yang bisa diubah ke processing']);
        }

        $paymentRequest->update([
            'status' => 'processing',
        ]);

        return back()->with('success', 'Status diubah menjadi Processing - Menunggu pembayaran dari Finance');
    }
}

