@extends('layouts.app')

@section('title', 'My Projects')

@section('page-title', 'My Projects')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-project-diagram mr-2"></i>Projects Saya
        </h2>
        <p class="text-gray-600 mt-1">Monitor progress dan komunikasi dengan tim project</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-600"></i>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Status Badge -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                @if($project->status == 'completed')
                                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Completed
                                    </span>
                                @elseif($project->status == 'in_progress')
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-spinner mr-1"></i>In Progress
                                    </span>
                                @elseif($project->status == 'on_hold')
                                    <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-pause-circle mr-1"></i>On Hold
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                @endif
                            </div>
                            <small class="text-gray-500">{{ $project->created_at->diffForHumans() }}</small>
                        </div>

                        <!-- Project Info -->
                        <h5 class="text-lg font-semibold text-gray-900 mb-2">{{ $project->project_name }}</h5>
                        <p class="text-gray-500 text-sm mb-3">{{ $project->project_code }}</p>
                        
                        @if($project->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($project->description, 80) }}</p>
                        @endif

                        <!-- Team Members -->
                        <div class="mb-4">
                            <small class="text-gray-600 flex items-center">
                                <i class="fas fa-users mr-2"></i>Tim: 
                                <span class="font-semibold ml-1">{{ $project->teams->sum(fn($team) => $team->members->count()) }} orang</span>
                            </small>
                        </div>

                        <!-- Dates -->
                        <div class="mb-4 space-y-1">
                            @if($project->start_date)
                                <small class="text-gray-600 flex items-center">
                                    <i class="fas fa-calendar-start mr-2 text-gray-400"></i>
                                    Mulai: {{ $project->start_date->format('d M Y') }}
                                </small>
                            @endif
                            @if($project->end_date)
                                <small class="text-gray-600 flex items-center">
                                    <i class="fas fa-calendar-check mr-2 text-gray-400"></i>
                                    Deadline: {{ $project->end_date->format('d M Y') }}
                                </small>
                            @endif
                        </div>

                        <!-- View Detail Button -->
                        <a href="{{ route('client.projects.show', $project) }}" 
                           class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-eye mr-2"></i>Lihat Detail & Chat
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-12 text-center">
                <i class="fas fa-project-diagram text-gray-300 text-6xl mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-800 mb-2">Belum ada project</h5>
                <p class="text-gray-600 mb-4">Project Anda akan muncul di sini setelah order dikonfirmasi oleh admin</p>
            </div>
        </div>
    @endif
</div>
@endsection
