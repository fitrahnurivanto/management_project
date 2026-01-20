@props(['project', 'stats'])

<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">
                <i class="fas fa-receipt mr-2 text-indigo-600"></i> Project Expenses
            </h2>
            <p class="text-sm text-gray-500 mt-1">Track all expenses for this project</p>
        </div>
        @if(auth()->user()->isAdmin())
        <button onclick="openAddExpenseModal()" 
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm flex items-center gap-2"
                aria-label="Tambah expense baru">
            <i class="fas fa-plus"></i> Add Expense
        </button>
        @endif
    </div>

    @if($project->expenses->isEmpty())
    <div class="text-center py-12">
        <i class="fas fa-wallet text-5xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada expense tercatat</p>
        @if(auth()->user()->isAdmin())
        <button onclick="openAddExpenseModal()" 
                class="mt-4 text-indigo-600 hover:text-indigo-800 font-semibold"
                aria-label="Tambah expense pertama">
            + Tambah Expense Pertama
        </button>
        @endif
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Receipt</th>
                    @if(auth()->user()->isAdmin())
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($project->expenses as $expense)
                @php
                    $badge = $project->presenter->getExpenseCategoryBadge($expense->expense_type);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badge['bg'] }} {{ $badge['text'] }}">
                            {{ $expense->expense_type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $expense->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                        {{ $project->presenter->formatCurrency($expense->amount) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($expense->receipt_file)
                        <a href="{{ Storage::url($expense->receipt_file) }}" 
                           target="_blank" 
                           class="text-indigo-600 hover:text-indigo-800"
                           aria-label="Lihat receipt {{ $expense->expense_type }}">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditExpenseModal({{ $expense->id }}, '{{ $expense->expense_type }}', '{{ $expense->description }}', {{ $expense->amount }}, '{{ $expense->expense_date }}')" 
                                    class="text-blue-600 hover:text-blue-800"
                                    aria-label="Edit expense {{ $expense->expense_type }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.projects.expenses.delete', [$project, $expense]) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus expense ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800"
                                        aria-label="Hapus expense {{ $expense->expense_type }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900">Total Expenses:</td>
                    <td class="px-4 py-3 text-right font-bold text-indigo-600 text-lg">
                        {{ $project->presenter->formatCurrency($stats['total_expenses']) }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
