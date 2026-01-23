<!-- Sidebar with Mobile Responsive -->
<div x-data="{ sidebarOpen: false }" @keydown.escape="sidebarOpen = false">
    <!-- Mobile Menu Button -->
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-white shadow-lg border border-gray-200 hover:bg-gray-50 transition">
        <i class="fas fa-bars text-xl text-gray-700" x-show="!sidebarOpen"></i>
        <i class="fas fa-times text-xl text-gray-700" x-show="sidebarOpen" x-cloak></i>
    </button>

    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         class="fixed left-0 top-0 bottom-0 w-64 bg-white text-gray-800 overflow-y-auto shadow-xl z-50 border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0">
    <div class="px-5 py-6 border-b border-gray-200">
        <h4 class="text-xl font-bold text-gray-800"><i class="fas fa-project-diagram mr-2 text-indigo-600"></i>Management</h4>
    </div>

    @php
        // Get active division from session or determine from user role
        $activeDivision = session('active_division');
        
        // If no session, set default based on user role
        if (!$activeDivision && auth()->user()->isAdmin()) {
            if (auth()->user()->isAgencyAdmin()) {
                $activeDivision = 'agency';
            } elseif (auth()->user()->isAcademyAdmin()) {
                $activeDivision = 'academy';
            } else {
                // Super admin - default to agency or get from request
                $activeDivision = request('division', 'agency');
            }
        }
    @endphp

    <!-- Division Switcher for Super Admin -->
    @if(auth()->user()->isSuperAdmin())
    <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
        <p class="text-xs font-semibold text-gray-500 mb-2">SWITCH DIVISION</p>
        <div class="grid grid-cols-2 gap-2">
            <form action="{{ route('division.set') }}" method="POST">
                @csrf
                <input type="hidden" name="division" value="agency">
                <button type="submit" class="w-full px-3 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ $activeDivision === 'agency' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' }}">
                    <i class="fas fa-briefcase mr-1"></i>Agency
                </button>
            </form>
            <form action="{{ route('division.set') }}" method="POST">
                @csrf
                <input type="hidden" name="division" value="academy">
                <button type="submit" class="w-full px-3 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ $activeDivision === 'academy' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' }}">
                    <i class="fas fa-graduation-cap mr-1"></i>Academy
                </button>
            </form>
        </div>
    </div>
    @endif
    
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
                @if($activeDivision === 'agency' || auth()->user()->isAgencyAdmin())
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
            @endif
            
            <!-- Menu Academy (Super Admin & Admin Academy) -->
            @if(auth()->user()->canAccessAcademy())
                @if($activeDivision === 'academy' || auth()->user()->isAcademyAdmin())
            <li class="mx-2.5 my-1">
                <a href="{{ route('admin.classes.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-purple-50 hover:text-purple-600 hover:translate-x-1 {{ request()->routeIs('admin.classes.index') || request()->routeIs('admin.classes.create') || request()->routeIs('admin.classes.edit') || request()->routeIs('admin.classes.show') ? 'bg-purple-50 text-purple-600 font-semibold' : '' }}">
                    <i class="fas fa-chalkboard-teacher w-6 text-lg"></i>
                    <span class="ml-2.5">Kelas</span>
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
        @elseif(auth()->user()->role === 'finance')
            <!-- Finance Menu -->
            <li class="mx-2.5 my-1">
                <a href="{{ route('finance.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-green-50 hover:text-green-600 hover:translate-x-1 {{ request()->routeIs('finance.dashboard') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    <i class="fas fa-home w-6 text-lg"></i>
                    <span class="ml-2.5">Dashboard</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('finance.expenses.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-green-50 hover:text-green-600 hover:translate-x-1 {{ request()->routeIs('finance.expenses.*') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    <i class="fas fa-receipt w-6 text-lg"></i>
                    <span class="ml-2.5">Expenses</span>
                </a>
            </li>
            <li class="mx-2.5 my-1">
                <a href="{{ route('finance.payment-requests.index') }}" class="flex items-center px-4 py-3 text-gray-700 no-underline rounded-xl transition-all hover:bg-green-50 hover:text-green-600 hover:translate-x-1 {{ request()->routeIs('finance.payment-requests.*') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    <i class="fas fa-money-bill-wave w-6 text-lg"></i>
                    <span class="ml-2.5">Payment Requests</span>
                </a>
            </li>
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