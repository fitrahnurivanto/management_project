@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="min-vh-100" style="background: #f3f4f6;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-briefcase me-2"></i>Management Project
            </a>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Order Baru
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>{{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Orders</p>
                                <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Spent</p>
                                <h4 class="mb-0">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-wallet fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Active Projects</p>
                                <h3 class="mb-0">{{ $stats['active_projects'] }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-tasks fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Completed</p>
                                <h3 class="mb-0">{{ $stats['completed_projects'] }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        @if($orders->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($orders as $order)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $order->order_number }}</strong>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($order->payment_status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                                            <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Belum ada order</p>
                            <div class="text-center">
                                <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Buat Order Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Recent Projects</h5>
                    </div>
                    <div class="card-body">
                        @if($projects->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($projects as $project)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $project->project_name }}</strong>
                                            @if($project->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($project->status == 'in_progress')
                                                <span class="badge bg-primary">In Progress</span>
                                            @elseif($project->status == 'on_hold')
                                                <span class="badge bg-warning">On Hold</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $project->project_code }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Belum ada project</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
