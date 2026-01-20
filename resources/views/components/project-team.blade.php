@props(['project'])

<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">Tim Project</h2>
        @if(auth()->user()->isAdmin())
        <button onclick="openAddMemberModal()" 
                class="bg-[#7b2cbf] text-white px-3 py-1 rounded-lg hover:bg-[#6a25a8] transition text-sm"
                aria-label="Tambah anggota tim">
            <i class="fas fa-plus mr-1"></i> Tambah
        </button>
        @endif
    </div>

    @if($project->teams->isEmpty() || $project->teams->sum(fn($t) => $t->members->count()) === 0)
    <div class="text-center py-8">
        <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
        <p class="text-sm text-gray-500">Belum ada tim yang ditugaskan</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($project->teams as $team)
            @foreach($team->members as $member)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#7b2cbf] rounded-full flex items-center justify-center text-white text-sm font-bold"
                         aria-hidden="true">
                        {{ $project->presenter->getUserInitials($member->user->name) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $member->user->name }}</p>
                        <p class="text-xs text-gray-600">{{ ucfirst($member->role) }}</p>
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
                <form action="{{ route('admin.projects.removeTeamMember', [$project, $member]) }}" 
                      method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus {{ $member->user->name ?? 'member ini' }} dari tim? Tindakan ini tidak dapat dibatalkan.')" 
                      class="remove-member-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="text-red-600 hover:text-red-800 text-sm" 
                            data-loading="false"
                            aria-label="Hapus {{ $member->user->name }} dari tim">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        @endforeach
    </div>
    @endif
</div>
