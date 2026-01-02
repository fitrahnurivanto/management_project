@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="min-vh-100" style="background: #f3f4f6;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-user-tie me-2"></i>Employee Portal
            </a>
            
            <div class="ms-auto">
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

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Active Projects</p>
                                <h3 class="mb-0">{{ $stats['active_projects'] }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-project-diagram fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Pending Tasks</p>
                                <h3 class="mb-0">{{ $stats['pending_tasks'] }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-tasks fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Hours This Month</p>
                                <h3 class="mb-0">{{ number_format($stats['hours_this_month'], 1) }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Assigned Projects -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Assigned Projects</h5>
                    </div>
                    <div class="card-body">
                        @if($projects->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($projects as $project)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $project->project_name }}</strong>
                                            @if($project->status == 'in_progress')
                                                <span class="badge bg-primary">In Progress</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($project->status) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <small class="text-muted">{{ $project->project_code }}</small>
                                            <br>
                                            <small class="text-muted">Client: {{ $project->client->user->name }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Belum ada project yang di-assign</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Pending Tasks</h5>
                    </div>
                    <div class="card-body">
                        @if($tasks->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($tasks as $task)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $task->title }}</strong>
                                            @if($task->priority == 'urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                            @elseif($task->priority == 'high')
                                                <span class="badge bg-warning">High</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->priority) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-project-diagram me-1"></i>{{ $task->project->project_name }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>Due: {{ $task->due_date ? $task->due_date->format('d M Y') : 'No deadline' }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Tidak ada task pending</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
