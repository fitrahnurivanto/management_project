<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentRequest::with(['project', 'clas', 'approver'])
            ->where('user_id', auth()->id());

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15)->appends($request->query());

        return view('employee.payment-requests.index', compact('requests'));
    }

    public function create()
    {
        // Get projects where user is a team member (agency)
        $projects = Project::whereHas('teams.members', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->whereIn('status', ['in_progress', 'on_hold'])
        ->get();

        // Get classes where user might be trainer (academy)
        $classes = \App\Models\Clas::where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('employee.payment-requests.create', compact('projects', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:project,class',
            'project_id' => 'required_if:type,project|nullable|exists:projects,id',
            'class_id' => 'required_if:type,class|nullable|exists:clas,id',
            'requested_amount' => 'required|numeric|min:0',
            'hours_worked' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validated['type'] === 'project') {
            // Verify user is member of the project
            $isMember = TeamMember::where('user_id', auth()->id())
                ->whereHas('team', function($q) use ($validated) {
                    $q->where('project_id', $validated['project_id']);
                })
                ->exists();

            if (!$isMember) {
                return back()->withErrors(['project_id' => 'Anda bukan anggota project ini']);
            }
        }

        PaymentRequest::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['type'] === 'project' ? $validated['project_id'] : null,
            'class_id' => $validated['type'] === 'class' ? $validated['class_id'] : null,
            'requested_amount' => $validated['requested_amount'],
            'hours_worked' => $validated['hours_worked'] ?? null,
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        // TODO: Send notification to admins

        return redirect()->route('employee.payment-requests.index')
            ->with('success', 'Permintaan pembayaran berhasil diajukan! Menunggu persetujuan admin.');
    }

    public function show(PaymentRequest $paymentRequest)
    {
        // Ensure user can only view their own requests
        if ($paymentRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $paymentRequest->load(['project', 'approver']);
        
        return view('employee.payment-requests.show', compact('paymentRequest'));
    }
}
