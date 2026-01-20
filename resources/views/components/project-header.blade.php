@props(['project', 'deadline', 'user'])

<div class="flex justify-between items-center mb-6">
    <div class="flex-1">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route($user->isAdmin() ? 'admin.projects.index' : 'employee.projects.index') }}" 
               class="text-gray-600 hover:text-gray-900"
               aria-label="Kembali ke daftar project">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->project_name }}</h1>
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $project->presenter->getStatusBadgeClass() }}">
                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </span>
            
            @if($project->presenter->isActive() && $deadline)
                @if($deadline['is_overdue'])
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 animate-pulse">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Terlambat {{ abs($deadline['days_left']) }} hari
                    </span>
                @elseif($deadline['is_urgent'])
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 animate-pulse">
                        <i class="fas fa-clock mr-1"></i> URGENT - {{ $deadline['days_left'] }} hari lagi!
                    </span>
                @endif
            @endif
        </div>
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>{{ $project->project_code }}</span>
            @if($project->pks_number)
            <span class="flex items-center"><i class="fas fa-file-contract mr-1"></i> {{ $project->pks_number }}</span>
            @endif
            @if($project->order)
            <span class="flex items-center"><i class="fas fa-receipt mr-1"></i> {{ $project->order->order_number }}</span>
            @endif
        </div>
    </div>
    @if($user->isAdmin())
    <div class="flex gap-3">
        <form action="{{ route('admin.projects.updateStatus', $project) }}" method="POST">
            @csrf
            @method('PATCH')
            <select name="status" 
                    onchange="this.form.submit()" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#7b2cbf] focus:border-[#7b2cbf]"
                    aria-label="Ubah status project">
                <option value="pending" {{ $project->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
    </div>
    @endif
</div>
