<!-- Edit Status Notes Modal -->
<div id="notesModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Edit Catatan Status Project</h3>
                <button onclick="closeNotesModal()" 
                        class="text-gray-500 hover:text-gray-700"
                        aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.projects.updateNotes', $project) }}" method="POST" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label for="status_notes" class="block text-sm font-semibold text-gray-700 mb-2">Catatan Status</label>
                <textarea name="status_notes" 
                          id="status_notes"
                          rows="5" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]" 
                          placeholder="Masukkan catatan terkait status project saat ini..."
                          aria-describedby="notes-help">{{ $project->status_notes }}</textarea>
                <p id="notes-help" class="text-xs text-gray-500 mt-1">Contoh: "Menunggu approval client", "Revisi ke-2", "Sedang finalisasi mockup"</p>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="closeNotesModal()" 
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-[#7b2cbf] text-white px-4 py-2 rounded-lg hover:bg-[#6a25a8] transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Team Member Modal -->
<div id="addMemberModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Tambah Anggota Tim</h3>
                <button onclick="closeAddMemberModal()" 
                        class="text-gray-500 hover:text-gray-700"
                        aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.projects.assignTeamMember', $project) }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Employee</label>
                <select name="user_id" 
                        id="user_id"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]"
                        aria-label="Pilih employee untuk ditambahkan ke tim">
                    <option value="">-- Pilih Employee --</option>
                    @if(isset($availableEmployees))
                        @foreach($availableEmployees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->email }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role dalam Tim</label>
                <select name="role" 
                        id="role"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]"
                        aria-label="Pilih role employee">
                    @foreach(config('project.team_roles') as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="closeAddMemberModal()" 
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-[#7b2cbf] text-white px-4 py-2 rounded-lg hover:bg-[#6a25a8] transition">
                    Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Expense Modal -->
<div id="addExpenseModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Tambah Expense Baru</h3>
                <button onclick="closeAddExpenseModal()" 
                        class="text-gray-500 hover:text-gray-700"
                        aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.projects.expenses.store', $project) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="expense_type" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select name="expense_type" 
                            id="expense_type"
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            aria-label="Pilih kategori expense">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(config('project.expense_categories') as $value => $label)
                        <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" 
                              id="description"
                              rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                              placeholder="Contoh: Gaji designer bulan Desember, FB Ads 3 hari, dll"></textarea>
                </div>

                <div>
                    <label for="add_amount" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Rp)</label>
                    <input type="text" 
                           name="amount_display" 
                           id="add_amount" 
                           required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="500.000" 
                           oninput="formatCurrency(this)"
                           aria-label="Masukkan jumlah expense">
                    <input type="hidden" name="amount" id="add_amount_raw">
                </div>

                <div>
                    <label for="expense_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" 
                           name="expense_date" 
                           id="expense_date"
                           required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           aria-label="Pilih tanggal expense">
                </div>

                <div>
                    <label for="receipt_file" class="block text-sm font-semibold text-gray-700 mb-2">Upload Receipt (Optional)</label>
                    <input type="file" 
                           name="receipt_file" 
                           id="receipt_file"
                           accept=".pdf,.jpg,.jpeg,.png" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           aria-describedby="receipt-help">
                    <p id="receipt-help" class="text-xs text-gray-500 mt-1">Max 2MB - PDF, JPG, PNG</p>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" 
                        onclick="closeAddExpenseModal()" 
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Expense Modal -->
<div id="editExpenseModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Edit Expense</h3>
                <button onclick="closeEditExpenseModal()" 
                        class="text-gray-500 hover:text-gray-700"
                        aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form id="editExpenseForm" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label for="edit_expense_type" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select name="expense_type" 
                            id="edit_expense_type" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(config('project.expense_categories') as $value => $label)
                        <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" 
                              id="edit_description" 
                              rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label for="edit_amount" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Rp)</label>
                    <input type="text" 
                           name="amount_display" 
                           id="edit_amount" 
                           required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                           oninput="formatCurrency(this)">
                    <input type="hidden" name="amount" id="edit_amount_raw">
                </div>

                <div>
                    <label for="edit_expense_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" 
                           name="expense_date" 
                           id="edit_expense_date" 
                           required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="edit_receipt_file" class="block text-sm font-semibold text-gray-700 mb-2">Upload Receipt Baru (Optional)</label>
                    <input type="file" 
                           name="receipt_file" 
                           id="edit_receipt_file"
                           accept=".pdf,.jpg,.jpeg,.png" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah</p>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" 
                        onclick="closeEditExpenseModal()" 
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Modal Functions
function openAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
}

function closeAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}

function openNotesModal() {
    document.getElementById('notesModal').classList.remove('hidden');
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.add('hidden');
}

function openAddExpenseModal() {
    document.getElementById('addExpenseModal').classList.remove('hidden');
}

function closeAddExpenseModal() {
    document.getElementById('addExpenseModal').classList.add('hidden');
}

function openEditExpenseModal(expenseId, expenseType, description, amount, expenseDate) {
    const modal = document.getElementById('editExpenseModal');
    const form = document.getElementById('editExpenseForm');
    
    form.action = `/admin/projects/{{ $project->id }}/expenses/${expenseId}`;
    
    document.getElementById('edit_expense_type').value = expenseType;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_amount').value = formatNumber(amount);
    document.getElementById('edit_amount_raw').value = amount;
    document.getElementById('edit_expense_date').value = expenseDate;
    
    modal.classList.remove('hidden');
}

function closeEditExpenseModal() {
    document.getElementById('editExpenseModal').classList.add('hidden');
}

// Format currency with thousand separator
function formatCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        input.value = formatNumber(value);
        const hiddenId = input.id.replace('add_amount', 'add_amount_raw').replace('edit_amount', 'edit_amount_raw');
        document.getElementById(hiddenId).value = value;
    } else {
        input.value = '';
    }
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Close modals when clicking outside
['addMemberModal', 'notesModal', 'addExpenseModal', 'editExpenseModal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush
