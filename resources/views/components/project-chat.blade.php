@props(['project', 'chats'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col" style="height: 600px;">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-xl">
        <h5 class="text-lg font-semibold text-white">
            <i class="fas fa-comments mr-2"></i>Project Chat
        </h5>
        <p class="text-xs text-indigo-100 mt-1">Global - Client, Team & Admin</p>
    </div>
    
    <!-- Chat Messages -->
    <div class="flex-1 overflow-y-auto p-4" id="chatMessages" role="log" aria-live="polite" aria-label="Chat messages">
        @foreach($chats as $chat)
            <div class="mb-4 {{ $chat->user_id == auth()->id() ? 'text-right' : '' }}">
                <div class="inline-block max-w-[80%]">
                    <div class="flex items-center mb-1 {{ $chat->user_id == auth()->id() ? 'justify-end' : '' }}">
                        <strong class="{{ $chat->user_id == auth()->id() ? 'text-indigo-600' : 'text-gray-900' }} text-sm">
                            {{ $chat->user->name }}
                        </strong>
                        <small class="text-gray-500 ml-2 text-xs">
                            <time datetime="{{ $chat->created_at->toIso8601String() }}">
                                {{ $chat->created_at->format('H:i') }}
                            </time>
                        </small>
                    </div>
                    <div class="p-3 rounded-lg text-sm {{ $chat->user_id == auth()->id() ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none' }}">
                        {{ $chat->message }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chat Input -->
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
        <form id="chatForm" class="flex gap-2">
            @csrf
            <label for="messageInput" class="sr-only">Ketik pesan</label>
            <input type="text" 
                   id="messageInput" 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" 
                   placeholder="Ketik pesan..." 
                   required
                   aria-label="Ketik pesan chat">
            <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold transition-colors"
                    aria-label="Kirim pesan">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ===== CHAT FUNCTIONALITY =====
const chatMessages = document.getElementById('chatMessages');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');
const projectId = {{ $project->id }};
const refreshInterval = {{ config('project.chat_refresh_interval', 5000) }};

// Scroll to bottom
function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Initial scroll
if (chatMessages) {
    scrollToBottom();
}

// Send message
if (chatForm) {
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch(`/admin/projects/${projectId}/chat`, {
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
            } else {
                console.error('Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });
}

// Append new message
function appendMessage(chat) {
    const isMe = chat.user_id == {{ auth()->id() }};
    const messageHtml = `
        <div class="mb-4 ${isMe ? 'text-right' : ''}">
            <div class="inline-block max-w-[80%]">
                <div class="flex items-center mb-1 ${isMe ? 'justify-end' : ''}">
                    <strong class="${isMe ? 'text-indigo-600' : 'text-gray-900'} text-sm">
                        ${chat.user.name}
                    </strong>
                    <small class="text-gray-500 ml-2 text-xs">${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</small>
                </div>
                <div class="p-3 rounded-lg text-sm ${isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none'}">
                    ${chat.message}
                </div>
            </div>
        </div>
    `;
    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
}

// Refresh chat every X seconds
setInterval(async () => {
    try {
        const response = await fetch(`/admin/projects/${projectId}/chats`);
        if (response.ok) {
            const chats = await response.json();
            refreshChatMessages(chats);
        }
    } catch (error) {
        console.error('Error refreshing chat:', error);
    }
}, refreshInterval);

function refreshChatMessages(chats) {
    const currentScroll = chatMessages.scrollTop;
    const isAtBottom = chatMessages.scrollHeight - chatMessages.scrollTop === chatMessages.clientHeight;
    
    chatMessages.innerHTML = '';
    chats.forEach(chat => {
        const isMe = chat.user_id == {{ auth()->id() }};
        const messageHtml = `
            <div class="mb-4 ${isMe ? 'text-right' : ''}">
                <div class="inline-block max-w-[80%]">
                    <div class="flex items-center mb-1 ${isMe ? 'justify-end' : ''}">
                        <strong class="${isMe ? 'text-indigo-600' : 'text-gray-900'} text-sm">
                            ${chat.user.name}
                        </strong>
                        <small class="text-gray-500 ml-2 text-xs">${new Date(chat.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</small>
                    </div>
                    <div class="p-3 rounded-lg text-sm ${isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none'}">
                        ${chat.message}
                    </div>
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    });
    
    if (isAtBottom) {
        scrollToBottom();
    }
}
// ===== END CHAT FUNCTIONALITY =====
</script>
@endpush
