@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h2>
        <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Projects</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['active_projects'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-project-diagram text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Tasks</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['pending_tasks'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hours This Month</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['hours_this_month'], 1) }}</h3>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assigned Projects -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-project-diagram mr-2"></i>Assigned Projects</h5>
            </div>
            <div class="p-6">
                @if($projects->count() > 0)
                    <div class="space-y-4">
                        @foreach($projects as $project)
                            <a href="{{ route('employee.projects.show', $project) }}" class="block p-4 rounded-lg border border-gray-200 hover:border-blue-400 hover:shadow-md transition">
                                <div class="flex justify-between items-center mb-2">
                                    <strong class="text-gray-900">{{ $project->project_name }}</strong>
                                    @if($project->status == 'in_progress')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">In Progress</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">{{ ucfirst($project->status) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <small class="text-gray-600">{{ $project->project_code }}</small>
                                    <br>
                                    <small class="text-gray-600">Client: {{ $project->client->user->name ?? $project->client->company_name ?? 'N/A' }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Belum ada project yang di-assign</p>
                @endif
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-tasks mr-2"></i>Pending Tasks</h5>
            </div>
            <div class="p-6">
                @if($tasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($tasks as $task)
                            <div class="p-4 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <strong class="text-gray-900 block">{{ $task->title }}</strong>
                                        <small class="text-gray-600">{{ $task->project->project_name }}</small>
                                    </div>
                                    @if($task->priority == 'urgent')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Urgent</span>
                                    @elseif($task->priority == 'high')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">High</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">{{ ucfirst($task->priority) }}</span>
                                    @endif
                                </div>
                                @if($task->due_date)
                                    <small class="text-gray-600">
                                        <i class="far fa-calendar mr-1"></i>Due: {{ $task->due_date->format('d M Y') }}
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Tidak ada task pending</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection