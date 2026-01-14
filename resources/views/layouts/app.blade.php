<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Management Project')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN (Play CDN - Not for Production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    @include('components.sidebar')
    
    <div class="ml-64 min-h-screen">
        <div class="sticky top-0 bg-white px-8 py-4 shadow-sm z-10 flex justify-between items-center">
            <div>
                <h5 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="flex items-center gap-4">
                <!-- Notification Bell -->
                <div x-data="notificationSystem" class="relative">
                    <button @click="toggleNotifications" class="relative text-gray-600 hover:text-gray-900 transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span x-show="unreadCount > 0" x-text="unreadCount" 
                              class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse"></span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div x-show="showNotifications" x-cloak @click.away="showNotifications = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="p-6 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                    <p class="text-sm">No notifications</p>
                                </div>
                            </template>
                            <template x-for="notif in notifications" :key="notif.id">
                                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                                     :class="{'bg-blue-50': !notif.read}"
                                     @click="markAsRead(notif.id)">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                             :class="notif.type === 'warning' ? 'bg-orange-100 text-orange-600' : 
                                                     notif.type === 'success' ? 'bg-green-100 text-green-600' : 
                                                     notif.type === 'danger' ? 'bg-red-100 text-red-600' : 
                                                     'bg-blue-100 text-blue-600'">
                                            <i :class="notif.icon" class="text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900" x-text="notif.title"></p>
                                            <p class="text-xs text-gray-600" x-text="notif.message"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="notif.time"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <span class="text-gray-600">
                    <i class="fas fa-user-circle mr-2"></i>
                    {{ auth()->user()->name }}
                </span>
                <span class="px-3 py-1 bg-indigo-600 text-white text-sm font-medium rounded-lg">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
        
        @yield('content')
    </div>
    
    @stack('scripts')
    
    <!-- Global Alpine.js Components -->
    <script>
        // Notification System
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationSystem', () => ({
                notifications: [],
                showNotifications: false,
                unreadCount: 0,
                
                init() {
                    // Load notifications from localStorage
                    const saved = localStorage.getItem('notifications');
                    if (saved) {
                        this.notifications = JSON.parse(saved);
                        this.updateUnreadCount();
                    }
                    
                    // Check for deadline notifications on page load
                    this.checkDeadlines();
                    
                    // Listen for custom notification events
                    window.addEventListener('notify', (event) => {
                        this.addNotification(event.detail);
                    });
                },
                
                toggleNotifications() {
                    this.showNotifications = !this.showNotifications;
                },
                
                addNotification(data) {
                    const notification = {
                        id: Date.now(),
                        title: data.title,
                        message: data.message,
                        type: data.type || 'info',
                        icon: data.icon || 'fas fa-info-circle',
                        time: 'Just now',
                        read: false,
                        timestamp: new Date().toISOString()
                    };
                    
                    this.notifications.unshift(notification);
                    if (this.notifications.length > 20) {
                        this.notifications = this.notifications.slice(0, 20);
                    }
                    
                    this.updateUnreadCount();
                    this.saveNotifications();
                    this.showToast(notification);
                },
                
                markAsRead(id) {
                    const notif = this.notifications.find(n => n.id === id);
                    if (notif && !notif.read) {
                        notif.read = true;
                        this.updateUnreadCount();
                        this.saveNotifications();
                    }
                },
                
                updateUnreadCount() {
                    this.unreadCount = this.notifications.filter(n => !n.read).length;
                },
                
                saveNotifications() {
                    localStorage.setItem('notifications', JSON.stringify(this.notifications));
                },
                
                showToast(notification) {
                    const toastContainer = document.getElementById('toast-container');
                    const toast = document.createElement('div');
                    toast.className = `mb-3 p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                        notification.type === 'success' ? 'bg-green-500' :
                        notification.type === 'danger' ? 'bg-red-500' :
                        notification.type === 'warning' ? 'bg-orange-500' :
                        'bg-blue-500'
                    } text-white`;
                    toast.innerHTML = `
                        <div class="flex items-start gap-3">
                            <i class="${notification.icon} text-lg mt-1"></i>
                            <div class="flex-1">
                                <p class="font-semibold">${notification.title}</p>
                                <p class="text-sm opacity-90">${notification.message}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    toastContainer.appendChild(toast);
                    
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }, 5000);
                },
                
                checkDeadlines() {
                    // This will be populated by backend data
                    @if(isset($deadlineProjects))
                        @foreach($deadlineProjects as $proj)
                            this.addNotification({
                                title: 'Deadline Alert',
                                message: '{{ $proj->project_name }} deadline in {{ $proj->days_remaining }} days',
                                type: '{{ $proj->days_remaining <= 1 ? "danger" : "warning" }}',
                                icon: 'fas fa-exclamation-triangle'
                            });
                        @endforeach
                    @endif
                }
            }));
        });
        
        // Global Loading State for Forms
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.hasAttribute('data-no-loading')) {
                        submitBtn.disabled = true;
                        const originalHTML = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                        submitBtn.setAttribute('data-original-html', originalHTML);
                        
                        // Reset after 10 seconds as fallback
                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalHTML;
                            }
                        }, 10000);
                    }
                });
            });
        });
    </script>
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 w-96"></div>
</body>
</html>
