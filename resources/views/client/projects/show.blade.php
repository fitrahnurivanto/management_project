@extends('layouts.app')

@section('title', 'Project Detail - ' . $project->project_name)

@section('page-title', $project->project_name)

@section('content')
<div class="p-8">
    <!-- Project Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $project->project_name }}</h3>
                    <p class="text-gray-600">{{ $project->project_code }}</p>
                </div>
                <div>
                    @if($project->status == 'completed')
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                            <i class="fas fa-check-circle mr-2"></i>Completed
                        </span>
                    @elseif($project->status == 'in_progress')
                        <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                            <i class="fas fa-spinner mr-2"></i>In Progress
                        </span>
                    @elseif($project->status == 'on_hold')
                        <span class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                            <i class="fas fa-pause-circle mr-2"></i>On Hold
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                            <i class="fas fa-clock mr-2"></i>Pending
                        </span>
                    @endif
                </div>
            </div>

            @if($project->description)
                <p class="text-gray-700 mb-6">{{ $project->description }}</p>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center border border-gray-200 rounded-lg p-4">
                    <i class="fas fa-tasks text-blue-600 text-3xl mb-2"></i>
                    <h4 class="text-2xl font-bold text-gray-900">{{ $stats['total_tasks'] }}</h4>
                    <small class="text-gray-600">Total Tasks</small>
                </div>
                <div class="text-center border border-gray-200 rounded-lg p-4">
                    <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                    <h4 class="text-2xl font-bold text-gray-900">{{ $stats['completed_tasks'] }}</h4>
                    <small class="text-gray-600">Completed</small>
                </div>
                <div class="text-center border border-gray-200 rounded-lg p-4">
                    <i class="fas fa-users text-indigo-600 text-3xl mb-2"></i>
                    <h4 class="text-2xl font-bold text-gray-900">{{ $stats['team_members'] }}</h4>
                    <small class="text-gray-600">Team Members</small>
                </div>
                <div class="text-center border border-gray-200 rounded-lg p-4">
                    <i class="fas fa-chart-line text-yellow-600 text-3xl mb-2"></i>
                    <h4 class="text-2xl font-bold text-gray-900">{{ $stats['progress'] }}%</h4>
                    <small class="text-gray-600">Progress</small>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Project Info & Team -->
        <div class="space-y-6">
            <!-- Project Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar mr-2"></i>Timeline
                    </h5>
                </div>
                <div class="p-6 space-y-4">
                    @if($project->start_date)
                        <div>
                            <small class="text-gray-600 block mb-1">Tanggal Mulai</small>
                            <strong class="text-gray-900">{{ $project->start_date->format('d F Y') }}</strong>
                        </div>
                    @endif
                    @if($project->end_date)
                        <div>
                            <small class="text-gray-600 block mb-1">Deadline</small>
                            <div class="flex items-center gap-2">
                                <strong class="text-gray-900">{{ $project->end_date->format('d F Y') }}</strong>
                                @if($project->end_date->isPast() && $project->status != 'completed')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Overdue</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div>
                        <small class="text-gray-600 block mb-1">Dibuat</small>
                        <strong class="text-gray-900">{{ $project->created_at->format('d F Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-users mr-2"></i>Team Members
                    </h5>
                </div>
                <div class="p-6">
                    @if($project->teams->count() > 0)
                        <div class="space-y-4">
                            @foreach($project->teams as $team)
                                <div>
                                    <h6 class="text-indigo-600 font-semibold mb-2">{{ $team->team_name }}</h6>
                                    @foreach($team->members as $member)
                                        <div class="flex items-center mb-3">
                                            <div class="bg-indigo-100 rounded-full p-2 mr-3">
                                                <i class="fas fa-user text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $member->user->name }}</div>
                                                <small class="text-gray-600">{{ ucfirst($member->role) }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada tim</p>
                    @endif
                </div>
            </div>

            <!-- Services -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-box mr-2"></i>Layanan
                    </h5>
                </div>
                <div class="p-6">
                    @if($project->order->items->count() > 0)
                        <div class="space-y-3">
                            @foreach($project->order->items as $item)
                                <div class="border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                    <div class="font-semibold text-gray-900">{{ $item->service->name }}</div>
                                    <small class="text-gray-600">Qty: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Chat -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col" style="height: 700px;">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-comments mr-2"></i>Project Chat
                        <small class="text-gray-600 font-normal ml-2">(Global - Client, Team & Admin)</small>
                    </h5>
                </div>
                
                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-6" id="chatMessages">
                    @foreach($chats as $chat)
                        <div class="mb-4 {{ $chat->user_id == auth()->id() ? 'text-right' : '' }}">
                            <div class="inline-block max-w-[70%]">
                                <div class="flex items-center mb-1 {{ $chat->user_id == auth()->id() ? 'justify-end' : '' }}">
                                    <strong class="{{ $chat->user_id == auth()->id() ? 'text-indigo-600' : 'text-gray-900' }}">
                                        {{ $chat->user->name }}
                                    </strong>
                                    <small class="text-gray-500 ml-2">{{ $chat->created_at->format('H:i') }}</small>
                                </div>
                                <div class="p-3 rounded-lg {{ $chat->user_id == auth()->id() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                                    {{ $chat->message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Chat Input -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <form id="chatForm" class="flex gap-2">
                        @csrf
                        <input type="text" 
                               id="messageInput" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Ketik pesan..." 
                               required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const projectId = {{ $project->id }};

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Initial scroll
    scrollToBottom();

    // Send message
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch(`/client/projects/${projectId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            if (response.ok) {
                const chat = await response.json();
                appendMessage(chat);
                messageInput.value = '';
                scrollToBottom();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });

    // Append new message
    function appendMessage(chat) {
        const isOwnMessage = chat.user_id === {{ auth()->id() }};
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 ${isOwnMessage ? 'text-right' : ''}`;
        messageDiv.innerHTML = `
            <div class="inline-block max-w-[70%]">
                <div class="flex items-center mb-1 ${isOwnMessage ? 'justify-end' : ''}">
                    <strong class="${isOwnMessage ? 'text-indigo-600' : 'text-gray-900'}">
                        ${chat.user.name}
                    </strong>
                    <small class="text-gray-500 ml-2">${new Date(chat.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</small>
                </div>
                <div class="p-3 rounded-lg ${isOwnMessage ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900'}">
                    ${chat.message}
                </div>
            </div>
        `;
        chatMessages.appendChild(messageDiv);
    }

    // Poll for new messages every 3 seconds
    setInterval(async () => {
        try {
            const response = await fetch(`/client/projects/${projectId}/chat/messages`);
            if (response.ok) {
                const chats = await response.json();
                const currentCount = chatMessages.children.length;
                
                if (chats.length > currentCount) {
                    // New messages available
                    for (let i = currentCount; i < chats.length; i++) {
                        appendMessage(chats[i]);
                    }
                    scrollToBottom();
                }
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }, 3000);
</script>
@endpush
@endsection
