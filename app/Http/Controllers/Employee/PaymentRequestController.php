<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index()
    {
        $requests = PaymentRequest::with(['project', 'approver'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('employee.payment-requests.index', compact('requests'));
    }

    public function create()
    {
        // Get projects where user is a team member
        $projects = Project::whereHas('teams.members', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->whereIn('status', ['in_progress', 'on_hold'])
        ->get();

        return view('employee.payment-requests.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'requested_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify user is member of the project
        $isMember = TeamMember::where('user_id', auth()->id())
            ->whereHas('team', function($q) use ($validated) {
                $q->where('project_id', $validated['project_id']);
            })
            ->exists();

        if (!$isMember) {
            return back()->withErrors(['project_id' => 'Anda bukan anggota project ini']);
        }

        PaymentRequest::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'],
            'requested_amount' => $validated['requested_amount'],
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
