<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Order;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects with search and filter.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Project::with(['client', 'order.items.service.category', 'teams.members']);

        if (!$user->isAdmin()) {
            // Employee hanya lihat project yang mereka ikuti
            $query->whereHas('teams.members', function($q) {
                $q->where('user_id', auth()->id());
            });
        } else {
            // Apply division filter for admin
            if ($user->isAgencyAdmin()) {
                // Agency admin: only see projects from agency orders
                $query->whereHas('order', function($q) {
                    $q->where('division', 'agency');
                });
            } elseif ($user->isAcademyAdmin()) {
                // Academy admin: only see projects from academy orders
                $query->whereHas('order', function($q) {
                    $q->where('division', 'academy');
                });
            }
            // Super admin sees all projects (no filter)
            
            // Filter by division for super admin (dropdown filter)
            if ($user->isSuperAdmin() && $request->has('division') && $request->division !== 'all') {
                $query->whereHas('order', function($q) use ($request) {
                    $q->where('division', $request->division);
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('project_name', 'like', '%' . $request->search . '%')
                  ->orWhere('project_code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'in_progress')
                      ->whereDate('end_date', '>=', now());
            } elseif ($request->status === 'overdue') {
                $query->where('status', 'in_progress')
                      ->whereDate('end_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by year (based on start_date or created_at)
        $selectedYear = $request->input('year', date('Y'));
        $availableYears = Project::select(DB::raw('YEAR(COALESCE(start_date, created_at)) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        if ($selectedYear !== 'all') {
            $query->whereYear(DB::raw('COALESCE(start_date, created_at)'), $selectedYear);
        }

        // Sort
        if ($request->sort === 'deadline_asc') {
            $query->orderBy('end_date', 'asc');
        } elseif ($request->sort === 'deadline_desc') {
            $query->orderBy('end_date', 'desc');
        } else {
            $query->latest();
        }

        $projects = $query->paginate(12)->appends($request->query());

        return view('admin.projects.index', compact('projects', 'user', 'selectedYear', 'availableYears'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $user = auth()->user();
        
        // Get orders yang sudah dibayar tapi belum ada projectnya
        $ordersQuery = Order::where('payment_status', 'paid')
            ->doesntHave('project')
            ->with(['client', 'items.service.category']);
        
        // Filter by division for agency/academy admin
        if ($user->isAgencyAdmin()) {
            $ordersQuery->whereHas('items.service.category', function($q) {
                $q->where('division', 'agency');
            });
        } elseif ($user->isAcademyAdmin()) {
            $ordersQuery->whereHas('items.service.category', function($q) {
                $q->where('division', 'academy');
            });
        }
        
        $orders = $ordersQuery->get();

        return view('admin.projects.create', compact('orders'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'service_budgets' => 'required|array',
            'service_budgets.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($validated['order_id']);

            // Create project
            $project = Project::create([
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'project_name' => $validated['project_name'],
                'project_code' => Project::generateProjectCode(),
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'budget' => $order->total_amount,
                'actual_cost' => 0,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
            ]);

            // Attach services with budget allocation
            // NOTE: Disabled - project_services table removed
            // foreach ($validated['services'] as $index => $serviceId) {
            //     $project->services()->attach($serviceId, [
            //         'allocated_budget' => $validated['service_budgets'][$index] ?? 0,
            //     ]);
            // }

            // Log activity
            \App\Models\ActivityLog::createLog(
                'create_project',
                'Project',
                $project->id,
                auth()->user()->name . ' membuat project baru: ' . $project->project_name
            );

            DB::commit();

            return redirect()->route('admin.projects.show', $project)
                ->with('success', 'Project berhasil dibuat. Silakan buat tim project.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Check authorization
        if (!auth()->user()->isAdmin()) {
            $isMember = $project->teams()->whereHas('members', function($q) {
                $q->where('user_id', auth()->id());
            })->exists();

            if (!$isMember) {
                abort(403);
            }
        }

        // Eager load all relationships to avoid N+1 queries
        $project->load([
            'client.user',
            'order.items.service',
            'order.items.servicePackage',
            'teams.members.user',
            'tasks' => function($query) {
                $query->with('assignee')->take(5);
            },
            'milestones',
            'expenses' => function($query) {
                $query->orderBy('expense_date', 'desc');
            },
            'chats' => function($query) {
                $query->with('user')->latest()->take(50);
            },
        ]);

        // Attach presenter for helper methods
        $project->presenter = new \App\Presenters\ProjectPresenter($project);

        // Calculate project statistics
        $stats = [
            'total_tasks' => $project->tasks->count(),
            'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
            'total_expenses' => $project->expenses->sum('amount'),
            'budget_used_percentage' => $project->budget > 0 ? ($project->actual_cost / $project->budget) * 100 : 0,
        ];

        // Calculate deadline information
        $deadline = null;
        if ($project->end_date) {
            $endDate = \Carbon\Carbon::parse($project->end_date);
            $daysLeft = (int) floor(\Carbon\Carbon::now()->diffInDays($endDate, false));
            
            $deadline = [
                'end_date' => $endDate,
                'days_left' => $daysLeft,
                'is_overdue' => $daysLeft < 0,
                'is_urgent' => $daysLeft >= 0 && $daysLeft <= 7,
                'is_today' => $daysLeft === 0,
                'formatted_date' => $endDate->format('d F Y'),
            ];
        }

        // Find PIC from team members
        $picMember = null;
        foreach($project->teams as $team) {
            foreach($team->members as $member) {
                if(strtolower($member->role) === 'pic') {
                    $picMember = $member;
                    break 2;
                }
            }
        }

        // Get recent activities
        $activities = \App\Models\ActivityLog::where('model', 'Project')
            ->where('model_id', $project->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get available employees for team assignment
        $availableEmployees = \App\Models\User::where('role', 'employee')->get();

        // Chats already loaded via eager loading, just reverse order
        $chats = $project->chats->reverse()->values();

        return view('admin.projects.show', compact('project', 'stats', 'deadline', 'picMember', 'activities', 'availableEmployees', 'chats'));
    }

    /**
     * Update project status.
     */
    public function updateStatus(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
        ]);

        $project->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        // Update actual cost if completed
        if ($validated['status'] === 'completed') {
            $project->updateActualCost();
        }

        // Log activity
        \App\Models\ActivityLog::createLog(
            'update_status',
            'Project',
            $project->id,
            auth()->user()->name . ' mengubah status project ' . $project->project_name . ' menjadi ' . $validated['status']
        );

        return redirect()->back()->with('success', 'Status project berhasil diupdate.');
    }

    /**
     * Update project status notes.
     */
    public function updateNotes(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status_notes' => 'nullable|string|max:500',
        ]);

        $project->update(['status_notes' => $validated['status_notes']]);

        // Log activity
        \App\Models\ActivityLog::createLog(
            'update_notes',
            'Project',
            $project->id,
            auth()->user()->name . ' mengupdate catatan status project ' . $project->project_name
        );

        return redirect()->back()->with('success', 'Catatan status berhasil diupdate.');
    }

    /**
     * Mark project as completed.
     */
    public function markCompleted(Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($project->status === 'completed') {
            return redirect()->back()->with('error', 'Project sudah dalam status completed.');
        }

        $project->update([
            'status' => 'completed',
            'completed_at' => now(),
            'status_notes' => 'Project telah diselesaikan pada ' . now()->format('d F Y H:i')
        ]);

        // Log activity
        \App\Models\ActivityLog::createLog(
            'mark_completed',
            'Project',
            $project->id,
            auth()->user()->name . ' menandai project ' . $project->project_name . ' sebagai selesai'
        );

        return redirect()->back()->with('success', 'Project berhasil ditandai sebagai completed! 🎉');
    }

    /**
     * Assign a team member to the project.
     */
    public function assignTeamMember(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:100',
        ]);

        // Create team if not exists
        $team = $project->teams()->first();
        if (!$team) {
            $team = Team::create([
                'project_id' => $project->id,
                'team_name' => 'Team ' . $project->project_name,
            ]);
        }

        // Check if already a member
        $exists = $team->members()->where('user_id', $validated['user_id'])->exists();
        if ($exists) {
            return back()->with('error', 'User sudah menjadi anggota team.');
        }

        // Add member
        $team->members()->create([
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
        ]);

        // Log activity
        $user = User::find($validated['user_id']);
        \App\Models\ActivityLog::createLog(
            'add_team_member',
            'Project',
            $project->id,
            auth()->user()->name . ' menambahkan ' . $user->name . ' ke team project ' . $project->project_name
        );

        return back()->with('success', 'Anggota team berhasil ditambahkan.');
    }

    /**
     * Remove a team member from the project.
     */
    public function removeTeamMember(Project $project, $memberId)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $teamMember = \App\Models\TeamMember::findOrFail($memberId);
        $userName = $teamMember->user->name;
        
        $teamMember->delete();

        // Log activity
        \App\Models\ActivityLog::createLog(
            'remove_team_member',
            'Project',
            $project->id,
            auth()->user()->name . ' menghapus ' . $userName . ' dari team project ' . $project->project_name
        );

        return back()->with('success', 'Anggota team berhasil dihapus.');
    }

    /**
     * Store a new expense for the project.
     */
    public function storeExpense(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = auth()->id();

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/expenses/' . $project->id, $filename);
            $validated['receipt_file'] = 'expenses/' . $project->id . '/' . $filename;
        }

        $expense = \App\Models\ProjectExpense::create($validated);

        // Update actual cost
        $project->updateActualCost();

        // Log activity
        \App\Models\ActivityLog::createLog(
            'add_expense',
            'Project',
            $project->id,
            auth()->user()->name . ' menambahkan expense ' . $validated['expense_type'] . ' - Rp ' . number_format($validated['amount'], 0, ',', '.')
        );

        return back()->with('success', 'Expense berhasil ditambahkan!');
    }

    /**
     * Update an existing expense.
     */
    public function updateExpense(Request $request, Project $project, $expenseId)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $expense = \App\Models\ProjectExpense::findOrFail($expenseId);

        $validated = $request->validate([
            'expense_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            // Delete old file
            if ($expense->receipt_file) {
                \Storage::delete('public/' . $expense->receipt_file);
            }

            $file = $request->file('receipt_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/expenses/' . $project->id, $filename);
            $validated['receipt_file'] = 'expenses/' . $project->id . '/' . $filename;
        }

        $expense->update($validated);

        // Update actual cost
        $project->updateActualCost();

        // Log activity
        \App\Models\ActivityLog::createLog(
            'update_expense',
            'Project',
            $project->id,
            auth()->user()->name . ' mengupdate expense ' . $validated['expense_type']
        );

        return back()->with('success', 'Expense berhasil diupdate!');
    }

    /**
     * Delete an expense.
     */
    public function deleteExpense(Project $project, $expenseId)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $expense = \App\Models\ProjectExpense::findOrFail($expenseId);
        $expenseType = $expense->expense_type;

        // Delete receipt file
        if ($expense->receipt_file) {
            \Storage::delete('public/' . $expense->receipt_file);
        }

        $expense->delete();

        // Update actual cost
        $project->updateActualCost();

        // Log activity
        \App\Models\ActivityLog::createLog(
            'delete_expense',
            'Project',
            $project->id,
            auth()->user()->name . ' menghapus expense ' . $expenseType
        );

        return back()->with('success', 'Expense berhasil dihapus!');
    }

    /**
     * Store a new chat message for admin.
     */
    public function storeChat(Request $request, Project $project)
    {
        // Admin can always chat
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $chat = \App\Models\ProjectChat::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        $chat->load('user');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($chat);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Pesan terkirim');
    }

    /**
     * Get chat messages via AJAX for admin.
     */
    public function getChats(Project $project)
    {
        // Admin can always view chats
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $chats = $project->chats()
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($chats);
    }
}
