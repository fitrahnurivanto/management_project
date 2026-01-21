<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with omset overview.
     */
    public function adminDashboard(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $user = auth()->user();

        // Filter parameters
        $status = $request->get('status', 'all'); // all, completed, active
        $period = $request->get('period', 'all'); // all, today, this_week, this_month, this_year
        $year = $request->get('year', 'all'); // all, 2026, 2025, 2024, 2023
        $category = $request->get('category', 'all');
        
        // Division filter - for super admin only
        $division = $request->get('division', null);
        
        // Determine active division
        if ($user->isSuperAdmin()) {
            // Super admin can switch between agency/academy or view both
            $activeDivision = $division ?? 'agency'; // default to agency
        } elseif ($user->isAgencyAdmin()) {
            $activeDivision = 'agency';
        } elseif ($user->isAcademyAdmin()) {
            $activeDivision = 'academy';
        } else {
            // Fallback for regular admin (legacy)
            $activeDivision = 'agency';
        }

        // Base queries
        $projectQuery = Project::query();
        $orderQuery = Order::paid();

        // Apply division filter to orders via service categories
        $orderQuery->whereHas('items.service.category', function($q) use ($activeDivision) {
            $q->where('division', $activeDivision);
        });

        // Apply division filter to projects via order items (services relation removed)
        $projectQuery->whereHas('order.items.service.category', function($q) use ($activeDivision) {
            $q->where('division', $activeDivision);
        });

        // Apply period filter
        if ($period !== 'all') {
            $dateFilter = $this->getDateFilter($period);
            $projectQuery->whereBetween('created_at', $dateFilter);
            // Use order_date if available (CSV data), fallback to confirmed_at
            $orderQuery->where(function($q) use ($dateFilter) {
                $q->whereBetween('order_date', $dateFilter)
                  ->orWhereBetween('confirmed_at', $dateFilter);
            });
        }
        
        // Apply year filter
        if ($year !== 'all') {
            // Filter projects by order date, not project created_at
            $projectQuery->whereHas('order', function($q) use ($year) {
                $q->where(function($subQ) use ($year) {
                    $subQ->whereYear('order_date', $year)
                         ->orWhereYear('confirmed_at', $year);
                });
            });
            
            $orderQuery->where(function($q) use ($year) {
                $q->whereYear('order_date', $year)
                  ->orWhereYear('confirmed_at', $year);
            });
        }

        // Apply status filter
        if ($status === 'completed') {
            $projectQuery->where('status', 'completed');
        } elseif ($status === 'active') {
            $projectQuery->whereIn('status', ['in_progress', 'on_hold']);
        }

        // Apply category filter
        if ($category !== 'all') {
            $projectQuery->whereHas('order.items.service.category', function($q) use ($category) {
                $q->where('id', $category);
            });
        }

        // Calculate omset statistics - Revenue = uang yang sudah masuk (paid_amount)
        $totalRevenue = $orderQuery->sum('paid_amount');
        $totalProjects = $projectQuery->count();
        $completedProjects = (clone $projectQuery)->where('status', 'completed')->count();
        $activeProjects = (clone $projectQuery)->whereIn('status', ['in_progress', 'on_hold'])->count();

        // Calculate costs and profit
        $projects = $projectQuery->get();
        $totalCost = $projects->sum('actual_cost');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Calculate growth rates (compare with previous period)
        $previousDateFilter = $this->getPreviousDateFilter($period);
        
        $previousRevenue = Order::paid()
            ->where(function($q) use ($previousDateFilter) {
                $q->whereBetween('order_date', $previousDateFilter)
                  ->orWhereBetween('confirmed_at', $previousDateFilter);
            })
            ->sum('paid_amount');
        
        $previousProjects = Project::whereBetween('created_at', $previousDateFilter)->get();
        $previousCost = $previousProjects->sum('actual_cost');
        $previousProfit = $previousRevenue - $previousCost;
        
        // Calculate percentage changes
        $revenueGrowth = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;
        
        $costGrowth = $previousCost > 0 
            ? (($totalCost - $previousCost) / $previousCost) * 100 
            : 0;
        
        $profitGrowth = $previousProfit != 0 
            ? (($totalProfit - $previousProfit) / abs($previousProfit)) * 100 
            : 0;
        
        $marginChange = $profitMargin - ($previousRevenue > 0 
            ? ($previousProfit / $previousRevenue) * 100 
            : 0);

        // Revenue by service (per layanan, bukan kategori)
        $revenueByService = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->join('projects', 'orders.id', '=', 'projects.order_id')
            ->whereIn('orders.payment_status', ['paid'])
            ->where('service_categories.division', $activeDivision)
            ->select(
                'services.name', 
                DB::raw('SUM(order_items.subtotal) as total'),
                DB::raw('COUNT(DISTINCT projects.id) as project_count')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total')
            ->limit(10) // Top 10 services
            ->get();

        // Top 5 most profitable projects
        $topProjects = Project::select('*')
            ->selectRaw('(budget - actual_cost) as profit')
            ->orderByDesc('profit')
            ->limit(5)
            ->get();

        // Monthly revenue chart data (last 12 months) - Based on actual payments received
        $monthlyRevenue = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->join('projects', 'orders.id', '=', 'projects.order_id')
            ->whereIn('orders.payment_status', ['paid'])
            ->where(function($q) {
                $q->where('orders.order_date', '>=', now()->subMonths(12))
                  ->orWhere('orders.confirmed_at', '>=', now()->subMonths(12));
            })
            ->where('service_categories.division', $activeDivision)
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(orders.order_date, orders.confirmed_at), "%Y-%m") as month'),
                DB::raw('SUM(order_items.subtotal * orders.paid_amount / orders.total_amount) as total'),
                DB::raw('COUNT(DISTINCT projects.id) as project_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Outstanding payments (Belum Lunas) - last 12 months
        $outstandingPayments = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->join('projects', 'orders.id', '=', 'projects.order_id')
            ->where('orders.payment_type', 'installment')
            ->whereIn('orders.payment_status', ['paid'])
            ->where('orders.remaining_amount', '>', 0)
            ->where(function($q) {
                $q->where('orders.order_date', '>=', now()->subMonths(12))
                  ->orWhere('orders.confirmed_at', '>=', now()->subMonths(12));
            })
            ->where('service_categories.division', $activeDivision)
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(orders.order_date, orders.confirmed_at), "%Y-%m") as month'),
                DB::raw('SUM(order_items.subtotal * orders.remaining_amount / orders.total_amount) as total'),
                DB::raw('COUNT(DISTINCT projects.id) as project_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Pending orders (waiting for confirmation)
        $pendingOrders = Order::pending()->count();

        // Recent activities
        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Weekly Target vs Actual (Current Month)
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // Get monthly target for active division
        $monthlyTarget = DB::table('monthly_targets')
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->where('division', $activeDivision)
            ->first();
        
        $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        
        // Get selected month/year for weekly chart (default to current)
        $selectedMonth = request('target_month', $currentMonth);
        $selectedYear = request('target_year', $currentYear);
        
        // Calculate weekly revenue for selected month
        $weeklyData = [];
        $startOfMonth = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Get target for selected month if different
        $selectedTargetAmount = $targetAmount;
        $selectedTargetNotes = '';
        if ($selectedMonth != $currentMonth || $selectedYear != $currentYear) {
            $selectedTarget = DB::table('monthly_targets')
                ->where('year', $selectedYear)
                ->where('month', $selectedMonth)
                ->where('division', $activeDivision)
                ->first();
            $selectedTargetAmount = $selectedTarget ? $selectedTarget->target_amount : 0;
            $selectedTargetNotes = $selectedTarget ? $selectedTarget->notes : '';
        } else {
            // Get notes for current month
            $currentTarget = DB::table('monthly_targets')
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->where('division', $activeDivision)
                ->first();
            $selectedTargetNotes = $currentTarget ? $currentTarget->notes : '';
        }
        
        for ($week = 1; $week <= 4; $week++) {
            // Calculate week date range
            $weekStart = $startOfMonth->copy()->addWeeks($week - 1);
            $weekEnd = $weekStart->copy()->addWeeks(1)->subDay();
            
            // Don't go beyond end of month
            if ($weekEnd->greaterThan($endOfMonth)) {
                $weekEnd = $endOfMonth;
            }
            
            // Get revenue for this week (filtered by division)
            // Include paid orders AND completed projects
            $weekRevenue = Order::where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('payment_status', 'partial');
                })
                ->where(function($q) use ($weekStart, $weekEnd) {
                    // Use DATE() to compare dates only, ignoring time
                    $q->whereRaw('DATE(COALESCE(order_date, confirmed_at)) BETWEEN ? AND ?', [
                        $weekStart->format('Y-m-d'), 
                        $weekEnd->format('Y-m-d')
                    ]);
                })
                ->whereHas('items.service.category', function($q) use ($activeDivision) {
                    $q->where('division', $activeDivision);
                })
                ->sum('paid_amount');
            
            // Calculate percentage
            $percentage = $selectedTargetAmount > 0 ? ($weekRevenue / $selectedTargetAmount) * 100 : 0;
            
            $weeklyData[] = [
                'week' => 'Minggu ' . $week,
                'revenue' => $weekRevenue,
                'percentage' => round($percentage, 1)
            ];
        }

        // Get ongoing classes and revenue data for Academy division
        $ongoingClasses = collect();
        $classRevenue = 0;
        $totalClassIncome = 0;
        $monthlyClassRevenue = [];
        if ($activeDivision === 'academy') {
            // Get ongoing classes
            $ongoingClasses = \App\Models\Clas::where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('start_date', 'desc')
                ->get();
            
            // Build class query with same filters
            $classQuery = \App\Models\Clas::where('status', 'approved');
            
            // Apply period filter
            if ($period !== 'all') {
                $dateFilter = $this->getDateFilter($period);
                $classQuery->whereBetween('start_date', $dateFilter);
            }
            
            // Apply year filter
            if ($year !== 'all') {
                $classQuery->whereYear('start_date', $year);
            }
            
            // Calculate total revenue from classes
            $classRevenue = $classQuery->sum('income');
            $totalClassIncome = \App\Models\Clas::where('status', 'approved')->sum('income');
            
            // Monthly class revenue for chart
            for ($m = 1; $m <= 12; $m++) {
                $monthRevenue = \App\Models\Clas::where('status', 'approved')
                    ->whereYear('start_date', $selectedYear)
                    ->whereMonth('start_date', $m)
                    ->sum('income');
                $monthlyClassRevenue[] = $monthRevenue;
            }
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalProjects',
            'completedProjects',
            'activeProjects',
            'totalCost',
            'totalProfit',
            'profitMargin',
            'revenueGrowth',
            'costGrowth',
            'profitGrowth',
            'marginChange',
            'revenueByService',
            'topProjects',
            'monthlyRevenue',
            'outstandingPayments',
            'pendingOrders',
            'recentActivities',
            'status',
            'period',
            'category',
            'weeklyData',
            'targetAmount',
            'selectedTargetAmount',
            'selectedTargetNotes',
            'selectedMonth',
            'selectedYear',
            'activeDivision',
            'user',
            'ongoingClasses',
            'classRevenue',
            'totalClassIncome',
            'monthlyClassRevenue'
        ));
    }

    /**
     * Get dashboard data for AJAX filter requests (returns JSON).
     */
    public function getFilteredData(Request $request)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $user = auth()->user();

            // Filter parameters
            $status = $request->get('status', 'all');
            $period = $request->get('period', 'all');
            $year = $request->get('year', 'all');
            $category = $request->get('category', 'all');
            
            // Division filter
            $division = $request->get('division', null);
            
            if ($user->isSuperAdmin()) {
                $activeDivision = $division ?? 'agency';
            } elseif ($user->isAgencyAdmin()) {
                $activeDivision = 'agency';
            } elseif ($user->isAcademyAdmin()) {
                $activeDivision = 'academy';
            } else {
                $activeDivision = 'agency';
            }

            // Base queries
            $projectQuery = Project::query();
            $orderQuery = Order::paid();

            // Apply division filter
            $orderQuery->whereHas('items.service.category', function($q) use ($activeDivision) {
                $q->where('division', $activeDivision);
            });

            $projectQuery->whereHas('order.items.service.category', function($q) use ($activeDivision) {
                $q->where('division', $activeDivision);
            });

        // Apply period filter
        if ($period !== 'all') {
            $dateFilter = $this->getDateFilter($period);
            $projectQuery->whereBetween('created_at', $dateFilter);
            $orderQuery->where(function($q) use ($dateFilter) {
                $q->whereBetween('order_date', $dateFilter)
                  ->orWhereBetween('confirmed_at', $dateFilter);
            });
        }
        
        // Apply year filter
        if ($year !== 'all') {
            // Filter projects by order date, not project created_at
            $projectQuery->whereHas('order', function($q) use ($year) {
                $q->where(function($subQ) use ($year) {
                    $subQ->whereYear('order_date', $year)
                         ->orWhereYear('confirmed_at', $year);
                });
            });
            
            $orderQuery->where(function($q) use ($year) {
                $q->whereYear('order_date', $year)
                  ->orWhereYear('confirmed_at', $year);
            });
        }

        // Apply status filter
        if ($status === 'completed') {
            $projectQuery->where('status', 'completed');
        } elseif ($status === 'active') {
            $projectQuery->whereIn('status', ['in_progress', 'on_hold']);
        }

        // Calculate statistics
        $totalRevenue = $orderQuery->sum('paid_amount');
        $totalProjects = $projectQuery->count();
        $completedProjects = (clone $projectQuery)->where('status', 'completed')->count();
        $activeProjects = (clone $projectQuery)->whereIn('status', ['in_progress', 'on_hold'])->count();

        $projects = $projectQuery->get();
        $totalCost = $projects->sum('actual_cost');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Growth rates
        $previousDateFilter = $this->getPreviousDateFilter($period);
        
        $previousRevenue = Order::paid()
            ->where(function($q) use ($previousDateFilter) {
                $q->whereBetween('order_date', $previousDateFilter)
                  ->orWhereBetween('confirmed_at', $previousDateFilter);
            })
            ->sum('paid_amount');
        
        $previousProjects = Project::whereBetween('created_at', $previousDateFilter)->get();
        $previousCost = $previousProjects->sum('actual_cost');
        $previousProfit = $previousRevenue - $previousCost;
        
        $revenueGrowth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $costGrowth = $previousCost > 0 ? (($totalCost - $previousCost) / $previousCost) * 100 : 0;
        $profitGrowth = $previousProfit != 0 ? (($totalProfit - $previousProfit) / abs($previousProfit)) * 100 : 0;
        $marginChange = $profitMargin - ($previousRevenue > 0 ? ($previousProfit / $previousRevenue) * 100 : 0);

        // Revenue by service - Apply same filters as main stats
        $revenueByServiceQuery = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->whereIn('orders.payment_status', ['paid'])
            ->where('service_categories.division', $activeDivision);
        
        // Apply period filter to revenue by service
        if ($period !== 'all') {
            $revenueByServiceQuery->where(function($q) use ($dateFilter) {
                $q->whereBetween('orders.order_date', $dateFilter)
                  ->orWhereBetween('orders.confirmed_at', $dateFilter);
            });
        }
        
        // Apply year filter to revenue by service
        if ($year !== 'all') {
            $revenueByServiceQuery->where(function($q) use ($year) {
                $q->whereYear('orders.order_date', $year)
                  ->orWhereYear('orders.confirmed_at', $year);
            });
        }
        
        $revenueByService = $revenueByServiceQuery
            ->join('projects', 'orders.id', '=', 'projects.order_id')
            ->select(
                'services.name', 
                DB::raw('SUM(order_items.subtotal) as total'),
                DB::raw('COUNT(DISTINCT projects.id) as project_count')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top 5 projects - Use filtered project query
        $topProjects = (clone $projectQuery)
            ->select('*')
            ->selectRaw('(budget - actual_cost) as profit')
            ->orderByDesc('profit')
            ->limit(5)
            ->get();

        // Monthly revenue (last 12 months)
        $monthlyRevenue = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->whereIn('orders.payment_status', ['paid'])
            ->where(function($q) {
                $q->where('orders.order_date', '>=', now()->subMonths(12))
                  ->orWhere('orders.confirmed_at', '>=', now()->subMonths(12));
            })
            ->where('service_categories.division', $activeDivision)
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(orders.order_date, orders.confirmed_at), "%Y-%m") as month'),
                DB::raw('SUM(order_items.subtotal * orders.paid_amount / orders.total_amount) as total'),
                DB::raw('COUNT(DISTINCT projects.id) as project_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Outstanding payments
        $outstandingPayments = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->join('projects', 'orders.id', '=', 'projects.order_id')
            ->where('orders.payment_type', 'installment')
            ->whereIn('orders.payment_status', ['paid'])
            ->where('orders.remaining_amount', '>', 0)
            ->where(function($q) {
                $q->where('orders.order_date', '>=', now()->subMonths(12))
                  ->orWhere('orders.confirmed_at', '>=', now()->subMonths(12));
            })
            ->where('service_categories.division', $activeDivision)
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(orders.order_date, orders.confirmed_at), "%Y-%m") as month'),
                DB::raw('SUM(order_items.subtotal * orders.remaining_amount / orders.total_amount) as total'),
                DB::raw('COUNT(DISTINCT projects.id) as project_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent activities
        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($activity) {
                return [
                    'user_name' => $activity->user ? $activity->user->name : 'System',
                    'description' => $activity->description,
                    'time_ago' => $activity->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'stats' => [
                'totalRevenue' => $totalRevenue,
                'totalCost' => $totalCost,
                'totalProfit' => $totalProfit,
                'profitMargin' => $profitMargin,
                'totalProjects' => $totalProjects,
                'completedProjects' => $completedProjects,
                'activeProjects' => $activeProjects,
                'revenueGrowth' => $revenueGrowth,
                'costGrowth' => $costGrowth,
                'profitGrowth' => $profitGrowth,
                'marginChange' => $marginChange,
            ],
            'charts' => [
                'revenueByService' => $revenueByService,
                'topProjects' => $topProjects,
                'monthlyRevenue' => $monthlyRevenue,
                'outstandingPayments' => $outstandingPayments,
            ],
            'activities' => $recentActivities
        ]);
        
        } catch (\Exception $e) {
            \Log::error('Dashboard Filter Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Display client dashboard.
     */
    public function clientDashboard()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return view('client.dashboard-empty');
        }

        $orders = $client->orders()->latest()->limit(5)->get();
        $projects = $client->projects()->latest()->limit(5)->get();

        $stats = [
            'total_orders' => $client->orders()->count(),
            'total_spent' => $client->orders()->paid()->sum('paid_amount'),
            'active_projects' => $client->projects()->whereIn('status', ['in_progress', 'on_hold'])->count(),
            'completed_projects' => $client->projects()->where('status', 'completed')->count(),
        ];

        return view('client.dashboard', compact('orders', 'projects', 'stats'));
    }

    /**
     * Display employee dashboard.
     */
    public function employeeDashboard()
    {
        $user = auth()->user();

        // Get recent projects (5 latest)
        $projects = Project::whereHas('teams.members', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['client', 'teams.members'])
            ->latest()
            ->limit(5)
            ->get();

        // Get ALL projects history for the employee
        $allProjects = Project::whereHas('teams.members', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['client', 'teams.members'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($project) use ($user) {
                // Get role and hourly rate from team_members pivot
                $teamMember = \App\Models\TeamMember::where('user_id', $user->id)
                    ->whereHas('team', function($q) use ($project) {
                        $q->where('project_id', $project->id);
                    })
                    ->first();
                
                $project->employee_role = $teamMember ? $teamMember->role : 'Member';
                $project->hourly_rate = $teamMember ? $teamMember->hourly_rate : 0;
                
                // Get total hours worked on this project
                $project->total_hours = \App\Models\TimeTracking::where('user_id', $user->id)
                    ->where('project_id', $project->id)
                    ->sum('hours');
                
                return $project;
            });

        // Get assigned tasks
        $tasks = $user->tasks()
            ->with('project')
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Get time tracking summary
        $timeThisMonth = $user->timeTrackings()
            ->whereMonth('work_date', now()->month)
            ->sum('hours');

        // Payment Requests Summary
        $paymentRequests = \App\Models\PaymentRequest::where('user_id', $user->id);
        
        $paymentStats = [
            // Pendapatan RIIL yang sudah cair (status = paid)
            'total_this_month' => (clone $paymentRequests)
                ->where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('approved_amount'),
            
            'total_this_year' => (clone $paymentRequests)
                ->where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->sum('approved_amount'),
            
            'total_all_time' => (clone $paymentRequests)
                ->where('status', 'paid')
                ->sum('approved_amount'),
            
            // Pending approval dari admin
            'pending_amount' => (clone $paymentRequests)
                ->where('status', 'pending')
                ->sum('requested_amount'),
            
            'pending_count' => (clone $paymentRequests)
                ->where('status', 'pending')
                ->count(),
            
            // Approved tapi belum dibayar (awaiting payment from finance)
            'approved_unpaid_amount' => (clone $paymentRequests)
                ->whereIn('status', ['approved', 'processing'])
                ->sum('approved_amount'),
            
            'approved_unpaid_count' => (clone $paymentRequests)
                ->whereIn('status', ['approved', 'processing'])
                ->count(),
        ];

        // Calculate average per month (only months with PAID payments)
        $paidPayments = (clone $paymentRequests)->where('status', 'paid')->get();
        $uniqueMonths = $paidPayments->filter(function($payment) {
            return $payment->paid_at !== null;
        })->map(function($payment) {
            return $payment->paid_at->format('Y-m');
        })->unique()->count();
        
        $monthCount = $uniqueMonths > 0 ? $uniqueMonths : 1;
        $paymentStats['average_per_month'] = $paymentStats['total_all_time'] / $monthCount;

        // Get monthly payment data for chart (last 12 months) - PAID only
        $monthlyPayments = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            
            $total = (clone $paymentRequests)
                ->where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('approved_amount');
            
            $monthlyPayments[] = [
                'month' => $month,
                'total' => $total,
            ];
        }

        $stats = [
            'active_projects' => Project::whereHas('teams.members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereIn('status', ['in_progress', 'on_hold'])->count(),
            'pending_tasks' => $user->tasks()->whereIn('status', ['todo', 'in_progress'])->count(),
            'hours_this_month' => $timeThisMonth,
        ];

        // Get ongoing classes (kelas berjalan)
        $ongoingClasses = \App\Models\Clas::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->get();

        return view('employee.dashboard', compact('projects', 'tasks', 'stats', 'allProjects', 'paymentStats', 'monthlyPayments', 'ongoingClasses'));
    }

    /**
     * Get date filter based on period.
     */
    private function getDateFilter($period)
    {
        switch ($period) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'this_week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'this_month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'this_year':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->subYears(10), now()];
        }
    }

    /**
     * Get previous period date filter for comparison.
     */
    private function getPreviousDateFilter($period)
    {
        switch ($period) {
            case 'today':
                return [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()];
            case 'this_week':
                return [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
            case 'this_month':
                return [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
            case 'this_year':
                return [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()];
            default:
                // For 'all', compare with previous year's same period
                return [now()->subYears(11), now()->subYear()];
        }
    }

    /**
     * Save or update monthly target.
     */
    public function saveTarget(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'division' => 'required|in:agency,academy',
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if target already exists
        $existing = DB::table('monthly_targets')
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('division', $validated['division'])
            ->first();

        if ($existing) {
            // Update existing
            DB::table('monthly_targets')
                ->where('id', $existing->id)
                ->update([
                    'target_amount' => $validated['target_amount'],
                    'notes' => $validated['notes'],
                    'updated_at' => now(),
                ]);

            $message = 'Target berhasil diupdate!';
        } else {
            // Insert new
            DB::table('monthly_targets')->insert([
                'year' => $validated['year'],
                'month' => $validated['month'],
                'division' => $validated['division'],
                'target_amount' => $validated['target_amount'],
                'notes' => $validated['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $message = 'Target berhasil disimpan!';
        }

        // Redirect back to dashboard with division, month, and year parameters
        $redirectParams = [
            'target_month' => $validated['month'],
            'target_year' => $validated['year'],
        ];
        
        if (auth()->user()->isSuperAdmin()) {
            $redirectParams['division'] = $validated['division'];
        }

        // Build URL with hash fragment
        $url = route('admin.dashboard', $redirectParams) . '#target-omset';
        
        return redirect($url)->with('success', $message);
    }

    /**
     * Get calendar events (projects) as JSON for FullCalendar
     */
    public function getCalendarEvents(Request $request)
    {
        $user = auth()->user();
        
        // Determine division filter
        $division = $request->get('division');
        
        if ($user->isSuperAdmin()) {
            $activeDivision = $division ?? 'agency';
        } elseif ($user->isAgencyAdmin()) {
            $activeDivision = 'agency';
        } elseif ($user->isAcademyAdmin()) {
            $activeDivision = 'academy';
        } else {
            $activeDivision = 'agency';
        }

        // Build query
        $query = Project::with(['client', 'order'])
            ->whereHas('order.items.service.category', function($q) use ($activeDivision) {
                $q->where('division', $activeDivision);
            })
            ->whereNotNull('start_date');

        // Filter by status if requested
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $projects = $query->get();

        // Format events for FullCalendar
        $events = $projects->map(function($project) {
            // Determine color based on status
            $colors = [
                'pending' => '#FCD34D',      // Yellow
                'in_progress' => '#3B82F6',  // Blue
                'completed' => '#10B981',    // Green
                'on_hold' => '#F97316',      // Orange
                'cancelled' => '#EF4444',    // Red
            ];

            // Check if overdue
            $isOverdue = false;
            if ($project->end_date && $project->status !== 'completed') {
                $isOverdue = \Carbon\Carbon::parse($project->end_date)->isPast();
            }

            $clientName = $project->client->company_name ?? $project->client->user->name ?? 'N/A';
            
            // Truncate title to max 35 characters
            $fullTitle = "[{$clientName}] {$project->project_name}";
            $title = strlen($fullTitle) > 35 ? substr($fullTitle, 0, 32) . '...' : $fullTitle;
            
            // Use end_date as single event (deadline)
            $eventDate = $project->end_date ?? $project->start_date;
            
            return [
                'id' => $project->id,
                'title' => $title,
                'start' => $eventDate,
                'allDay' => true,
                'backgroundColor' => $isOverdue ? '#DC2626' : ($colors[$project->status] ?? '#6B7280'),
                'borderColor' => $isOverdue ? '#991B1B' : ($colors[$project->status] ?? '#4B5563'),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'project_code' => $project->project_code,
                    'client' => $clientName,
                    'project_name' => $project->project_name,
                    'status' => $project->status,
                    'budget' => $project->budget,
                    'actual_cost' => $project->actual_cost,
                    'is_overdue' => $isOverdue,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'url' => route('admin.projects.show', $project->id),
                ],
            ];
        });

        return response()->json($events);
    }
}