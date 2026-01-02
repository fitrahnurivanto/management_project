@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Project</h1>
            <p class="text-gray-600">Kelola project dan tim kerja</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.projects.create') }}" class="bg-[#7b2cbf] text-white px-4 py-2 rounded-lg hover:bg-[#6a25a8] transition">
            <i class="fas fa-plus mr-2"></i> Buat Project Baru
        </a>
        @endif
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.projects.index') }}" class="px-6 py-3 border-b-2 {{ !request('status') ? 'border-[#7b2cbf] text-[#7b2cbf]' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-list mr-2"></i> Semua Project
                </a>
                <a href="{{ route('admin.projects.index', ['status' => 'pending']) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'pending' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-clock mr-2"></i> Pending
                </a>
                <a href="{{ route('admin.projects.index', ['status' => 'in_progress']) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'in_progress' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-spinner mr-2"></i> In Progress
                </a>
                <a href="{{ route('admin.projects.index', ['status' => 'completed']) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'completed' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-check-circle mr-2"></i> Completed
                </a>
                <a href="{{ route('admin.projects.index', ['status' => 'on_hold']) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'on_hold' ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-pause-circle mr-2"></i> On Hold
                </a>
                
                @if($user->isSuperAdmin())
                <!-- Division Filter for Super Admin -->
                <div class="ml-auto flex items-center px-4 gap-3">
                    <div class="flex items-center">
                        <label class="text-xs text-gray-600 mr-2">Divisi:</label>
                        <select onchange="window.location.href='{{ route('admin.projects.index') }}?division=' + this.value + '{{ request('status') ? '&status=' . request('status') : '' }}' + '{{ request('year') ? '&year=' . request('year') : '' }}'" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ request('division') == 'all' || !request('division') ? 'selected' : '' }}>Semua</option>
                            <option value="agency" {{ request('division') == 'agency' ? 'selected' : '' }}>Agency</option>
                            <option value="academy" {{ request('division') == 'academy' ? 'selected' : '' }}>Academy</option>
                        </select>
                    </div>
                </div>
                @endif
                
                <!-- Year Filter for All Admins -->
                <div class="flex items-center {{ $user->isSuperAdmin() ? '' : 'ml-auto' }} px-4">
                    <label class="text-xs text-gray-600 mr-2">Tahun:</label>
                    <select onchange="window.location.href='{{ route('admin.projects.index') }}?year=' + this.value + '{{ request('status') ? '&status=' . request('status') : '' }}' + '{{ request('division') ? '&division=' . request('division') : '' }}'" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </nav>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Projects Grid -->
    @if($projects->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Project</h3>
        <p class="text-gray-500">Project akan muncul setelah order dikonfirmasi dan project dibuat.</p>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($projects as $project)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <!-- Project Header -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-lg text-gray-900">{{ $project->project_name }}</h3>
                            @if($user->isSuperAdmin() && $project->order && $project->order->items->isNotEmpty())
                                @php
                                    $divisions = $project->order->items->map(fn($item) => $item->service->category->division)->unique();
                                @endphp
                                @if($divisions->contains('agency'))
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-briefcase"></i>
                                    </span>
                                @endif
                                @if($divisions->contains('academy'))
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-graduation-cap"></i>
                                    </span>
                                @endif
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">{{ $project->project_code }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($project->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($project->status === 'in_progress') bg-blue-100 text-blue-800
                        @elseif($project->status === 'completed') bg-green-100 text-green-800
                        @elseif($project->status === 'on_hold') bg-orange-100 text-orange-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
                
                <!-- Client Info -->
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-user mr-2"></i>
                    <span>{{ $project->client->user->name ?? $project->client->name }}</span>
                </div>
            </div>

            <!-- Project Stats -->
            <div class="p-6 bg-gray-50 border-b border-gray-100">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Budget</p>
                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($project->budget, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Actual Cost</p>
                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($project->actual_cost, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Start Date</p>
                        <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">End Date</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">Tim Project</h4>
                    <span class="text-xs text-gray-500">{{ $project->teams->sum(fn($t) => $t->members->count()) }} member</span>
                </div>
                @if($project->teams->isEmpty() || $project->teams->sum(fn($t) => $t->members->count()) === 0)
                <p class="text-xs text-gray-500 italic">Belum ada tim yang ditugaskan</p>
                @else
                <div class="flex -space-x-2">
                    @foreach($project->teams->flatMap->members->take(5) as $member)
                    <div class="w-8 h-8 rounded-full bg-[#7b2cbf] text-white flex items-center justify-center text-xs font-semibold border-2 border-white" title="{{ $member->user->name }}">
                        {{ strtoupper(substr($member->user->name, 0, 2)) }}
                    </div>
                    @endforeach
                    @if($project->teams->flatMap->members->count() > 5)
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center text-xs font-semibold border-2 border-white">
                        +{{ $project->teams->flatMap->members->count() - 5 }}
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="p-4 bg-gray-50 flex gap-2">
                <a href="{{ route('admin.projects.show', $project) }}" class="flex-1 bg-white text-[#7b2cbf] border border-[#7b2cbf] px-4 py-2 rounded-lg hover:bg-[#7b2cbf] hover:text-white transition text-center text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>
                @if(auth()->user()->isAdmin())
                <form action="{{ route('admin.projects.updateStatus', $project) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
                        <option value="pending" {{ $project->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
    <div class="mt-6">
        {{ $projects->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
