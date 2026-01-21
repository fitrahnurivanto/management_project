<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter period (default: this month)
        $period = $request->get('period', 'this_month');
        
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        // Revenue from Orders (paid)
        $totalRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('confirmed_at', [$startDate, $endDate])
            ->sum('paid_amount');

        // Pending Revenue (approved but not paid)
        $pendingRevenue = Order::where('payment_status', 'approved')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Project Expenses (approved)
        $totalExpenses = ProjectExpense::where('approval_status', 'approved')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Pending Expenses (need approval)
        $pendingExpenses = ProjectExpense::where('approval_status', 'pending')
            ->count();

        // Payment Requests (approved but not paid)
        $pendingPayments = PaymentRequest::where('status', 'approved')
            ->where('payment_status', 'unpaid')
            ->sum('approved_amount');

        $pendingPaymentCount = PaymentRequest::where('status', 'approved')
            ->where('payment_status', 'unpaid')
            ->count();

        // Net Profit
        $netProfit = $totalRevenue - $totalExpenses;

        // Recent Expenses (pending approval)
        $recentExpenses = ProjectExpense::with(['project', 'createdBy'])
            ->where('approval_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Recent Payment Requests (approved, waiting payment)
        $recentPaymentRequests = PaymentRequest::with(['user', 'project', 'clas'])
            ->where('status', 'approved')
            ->where('payment_status', 'unpaid')
            ->latest()
            ->take(5)
            ->get();

        // Monthly Revenue Chart (last 6 months)
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->where('confirmed_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(confirmed_at) as month, YEAR(confirmed_at) as year, SUM(paid_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Monthly Expenses Chart (last 6 months)
        $monthlyExpenses = ProjectExpense::where('approval_status', 'approved')
            ->where('expense_date', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(expense_date) as month, YEAR(expense_date) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('finance.dashboard', compact(
            'totalRevenue',
            'pendingRevenue',
            'totalExpenses',
            'pendingExpenses',
            'pendingPayments',
            'pendingPaymentCount',
            'netProfit',
            'recentExpenses',
            'recentPaymentRequests',
            'monthlyRevenue',
            'monthlyExpenses',
            'period'
        ));
    }
}
