<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectExpense::with(['project', 'createdBy', 'approvedBy']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        } else {
            // Default show pending
            $query->where('approval_status', 'pending');
        }
        
        // Filter by project
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('expense_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->latest()->paginate(15)->withQueryString();
        
        // Get all projects for filter
        $projects = \App\Models\Project::orderBy('project_name')->get();
        
        return view('finance.expenses.index', compact('expenses', 'projects'));
    }
    
    public function approve(ProjectExpense $expense)
    {
        if ($expense->approval_status !== 'pending') {
            return back()->with('error', 'Expense ini sudah di' . $expense->approval_status);
        }
        
        $expense->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'Expense berhasil diapprove');
    }
    
    public function reject(Request $request, ProjectExpense $expense)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        if ($expense->approval_status !== 'pending') {
            return back()->with('error', 'Expense ini sudah di' . $expense->approval_status);
        }
        
        $expense->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        
        return back()->with('success', 'Expense berhasil direject');
    }
}
