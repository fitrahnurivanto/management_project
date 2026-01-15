<div class="fixed left-0 top-0 bottom-0 w-64 bg-white text-gray-800 overflow-y-auto shadow-xl z-50 border-r border-gray-200">
    <div class="px-5 py-6 border-b border-gray-200">
        <h4 class="text-xl font-bold text-gray-800"><i class="fas fa-project-diagram mr-2 text-indigo-600"></i>Management</h4>
    </div>
    
    <ul class="py-5 px-0 list-none">
        @if(auth()->user()->role === 'admin')
            <!-- Dashboard - All Admins -->
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-home w-6 text-lg"></i>
                    <span class="ml-2.5">Dashboard</span>
                </a>
            </li>

            <!-- Menu Agency (Super Admin & Admin Agency) -->
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAgencyAdmin())
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-shopping-cart w-6 text-lg"></i>
                    <span class="ml-2.5">Orders</span>
                    @php
                        $pendingCount = \App\Models\Order::where('payment_status', 'pending_review')->count();
                    @endphp
                    @if($pendingCount > 0)
                    <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.clients.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.clients.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-users w-6 text-lg"></i>
                    <span class="ml-2.5">Klien</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.projects.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.projects.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-tasks w-6 text-lg"></i>
                    <span class="ml-2.5">Projects</span>
                </a>
            </li>
            @endif
            
            <!-- Menu Academy (Super Admin & Admin Academy) -->
            @if(auth()->user()->canAccessAcademy())
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.classes.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-purple-50 hover:text-purple-600 hover:translate-x-1 {{ request()->routeIs('admin.classes.index') || request()->routeIs('admin.classes.create') || request()->routeIs('admin.classes.edit') || request()->routeIs('admin.classes.show') ? 'bg-purple-50 text-purple-600 font-semibold' : '' }}">
                    <i class="fas fa-chalkboard-teacher w-6 text-lg"></i>
                    <span class="ml-2.5">Kelas</span>
                </a>
            </li>
             <li class="mx-2.5 my-1">
                <a href="{{ route('admin.trainer.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-purple-50 hover:text-purple-600 hover:translate-x-1 {{ request()->routeIs('admin.trainer.*') ? 'bg-purple-50 text-purple-600 font-semibold' : '' }}">
                    <i class="fas fa-user-graduate w-6 text-lg"></i>
                    <span class="ml-2.5">Trainer</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.classes.showclas') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-green-50 hover:text-green-600 hover:translate-x-1 {{ request()->routeIs('admin.classes.showclas') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    <i class="fas fa-play-circle w-6 text-lg"></i>
                    <span class="ml-2.5">Kelas Berjalan</span>
                    @php
                        $activeClassCount = \App\Models\Clas::where('status', 'approved')->count();
                    @endphp
                    @if($activeClassCount > 0)
                    <span class="ml-auto bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $activeClassCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Karyawan & Teams (Super Admin & Admin Agency) -->
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAgencyAdmin())
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.karyawan.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.karyawan.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-user-tie w-6 text-lg"></i>
                    <span class="ml-2.5">Karyawan</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1">
                    <i class="fas fa-users-cog w-6 text-lg"></i>
                    <span class="ml-2.5">Teams</span>
                </a>
            </li>
            @endif
            
            <!-- Laporan & Payment Requests - All Admins -->
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.laporan.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.laporan.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-chart-line w-6 text-lg"></i>
                    <span class="ml-2.5">Laporan</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.payment-requests.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.payment-requests.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-money-bill-wave w-6 text-lg"></i>
                    <span class="ml-2.5">Payment Requests</span>
                    @php
                        $pendingPaymentsQuery = \App\Models\PaymentRequest::where('status', 'pending');
                        if (auth()->user()->isAgencyAdmin()) {
                            $pendingPaymentsQuery->whereNotNull('project_id')
                                                ->whereHas('project.order', function($q) {
                                                    $q->where('division', 'agency');
                                                });
                        } elseif (auth()->user()->isAcademyAdmin()) {
                            $pendingPaymentsQuery->whereNotNull('class_id');
                        }
                        $pendingPayments = $pendingPaymentsQuery->count();
                    @endphp
                    @if($pendingPayments > 0)
                    <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $pendingPayments }}</span>
                    @endif
                </a>
            </li>

            <!-- Settings - Super Admin Only -->
            @if(auth()->user()->isSuperAdmin())
            <li class="mx-2.5 my-1 pt-2 border-t border-gray-200">
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-cog w-6 text-lg"></i>
                    <span class="ml-2.5">Pengaturan</span>
                </a>
            </li>
            @endif
        @elseif(auth()->user()->role === 'employee')
            <!-- Employee Menu -->
            <li class="mx-2.5 my-1">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-home w-6 text-lg"></i>
                    <span class="ml-2.5">Dashboard</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('employee.projects.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('employee.projects.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-tasks w-6 text-lg"></i>
                    <span class="ml-2.5">My Projects</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('employee.payment-requests.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('employee.payment-requests.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-money-bill-wave w-6 text-lg"></i>
                    <span class="ml-2.5">Payment Requests</span>
                </a>
            </li>
        @elseif(auth()->user()->role === 'client')
            <!-- Client Menu -->
            <li class="mx-2.5 my-1">
                <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('client.dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-home w-6 text-lg"></i>
                    <span class="ml-2.5">Dashboard</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('client.projects.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('client.projects.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-project-diagram w-6 text-lg"></i>
                    <span class="ml-2.5">My Projects</span>
                </a>
            </li>
        @else
            <!-- Employee Menu (Legacy) -->
            <li class="mx-2.5 my-1">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-home w-6 text-lg"></i>
                    <span class="ml-2.5">Dashboard</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('employee.projects.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1 {{ request()->routeIs('employee.projects.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : '' }}">
                    <i class="fas fa-tasks w-6 text-lg"></i>
                    <span class="ml-2.5">My Projects</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1">
                    <i class="fas fa-clock w-6 text-lg"></i>
                    <span class="ml-2.5">Time Tracking</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-indigo-50 hover:text-indigo-600 hover:translate-x-1">
                    <i class="fas fa-users-cog w-6 text-lg"></i>
                    <span class="ml-2.5">My Teams</span>
                </a>
            </li>
        @endif
        
        
        <li class="mx-2.5 my-1">
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-red-50 hover:text-red-600 hover:translate-x-1">
                    <i class="fas fa-sign-out-alt w-6 text-lg"></i>
                    <span class="ml-2.5">Logout</span>
                </a>
            </form>
        </li>
    </ul>
</div>

<style>
/* Custom scrollbar untuk sidebar */
.fixed::-webkit-scrollbar {
    width: 6px;
}
.fixed::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
}
.fixed::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 10px;
}
.fixed::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
}
</style>