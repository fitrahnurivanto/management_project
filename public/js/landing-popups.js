const servicesData = window.servicesData || [];
let selectedPackages = {};

// Success Toast
function showToast(message) {
    const toast = document.getElementById('successToast');
    document.getElementById('toastMessage').textContent = message;
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        closeToast();
    }, 5000);
}

function closeToast() {
    document.getElementById('successToast').classList.add('hidden');
}

// Modal functions
function closePackageModal() {
    document.getElementById('packageModal').classList.add('hidden');
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
}

function closeMagangModal() {
    document.getElementById('magangModal').classList.add('hidden');
}

function closeSertifikasiModal() {
    document.getElementById('sertifikasiModal').classList.add('hidden');
}

// Show packages modal
function showPackages(serviceId) {
    const service = servicesData.find(s => s.id === serviceId);
    if (!service || !service.packages || service.packages.length === 0) return;

    document.getElementById('modalTitle').textContent = `Pilih Paket - ${service.name}`;
    
    let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
    
    service.packages.forEach(pkg => {
        const isSelected = selectedPackages[serviceId] === pkg.id;
        const popularBadge = pkg.is_popular ? '<span class="absolute -top-3 right-4 bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">PALING LARIS</span>' : '';
        
        html += `
            <div class="relative border-2 ${isSelected ? 'border-purple-600 bg-purple-50' : 'border-gray-200'} rounded-xl p-6 hover:shadow-lg transition cursor-pointer" onclick="selectPackage(${serviceId}, ${pkg.id}, '${pkg.name.replace(/'/g, "\\'")}', ${pkg.price})">
                ${popularBadge}
                <div class="text-center mb-4">
                    <h4 class="text-xl font-bold text-gray-900 mb-2">${pkg.name}</h4>
                    <div class="text-3xl font-bold text-purple-700 mb-1">Rp ${new Intl.NumberFormat('id-ID').format(pkg.price)}</div>
                    ${pkg.duration ? `<p class="text-sm text-gray-600">${pkg.duration} hari</p>` : ''}
                </div>
                <div class="space-y-2 mb-4">
                    ${pkg.features.map(f => `
                        <div class="flex items-start text-sm">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>${f}</span>
                        </div>
                    `).join('')}
                </div>
                <button class="w-full py-2 rounded-lg font-semibold ${isSelected ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-purple-100'}">
                    ${isSelected ? '<i class="fas fa-check mr-2"></i>Dipilih' : 'Pilih Paket'}
                </button>
            </div>
        `;
    });
    
    html += '</div>';
    html += `
        <div class="mt-6 flex justify-end gap-3">
            <button onclick="closePackageModal()" class="px-6 py-3 rounded-lg font-semibold border-2 border-gray-300 hover:bg-gray-50 transition">
                Batal
            </button>
            <button onclick="confirmPackageSelection()" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition">
                <i class="fas fa-check mr-2"></i> Lanjut ke Form Order
            </button>
        </div>
    `;
    
    document.getElementById('packageContent').innerHTML = html;
    document.getElementById('packageModal').classList.remove('hidden');
}

function selectPackage(serviceId, packageId, packageName, price) {
    selectedPackages[serviceId] = packageId;
    showPackages(serviceId); // Refresh modal
}

function confirmPackageSelection() {
    closePackageModal();
    showOrderForm();
}

// Show order form in popup
function showOrderForm() {
    if (Object.keys(selectedPackages).length === 0) {
        alert('Silakan pilih paket terlebih dahulu');
        return;
    }

    // Build selected services display
    let servicesHtml = '';
    let totalPrice = 0;

    Object.entries(selectedPackages).forEach(([serviceId, packageId]) => {
        const service = servicesData.find(s => s.id == serviceId);
        
        if (service && packageId) {
            const pkg = service.packages.find(p => p.id == packageId);
            if (pkg) {
                totalPrice += parseFloat(pkg.price);
                servicesHtml += `
                    <div class="flex items-center justify-between p-3 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${service.name}</p>
                            <p class="text-sm text-gray-600">Paket: ${pkg.name}</p>
                        </div>
                        <p class="font-bold text-purple-700">Rp ${new Intl.NumberFormat('id-ID').format(pkg.price)}</p>
                    </div>
                `;
            }
        }
    });

    servicesHtml += `
        <div class="p-4 bg-purple-100 border-2 border-purple-600 rounded-lg mt-3">
            <div class="flex justify-between items-center">
                <span class="font-bold text-gray-900">Total Estimasi:</span>
                <span class="text-2xl font-bold text-purple-700">Rp ${new Intl.NumberFormat('id-ID').format(totalPrice)}</span>
            </div>
        </div>
    `;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formHtml = `
        <form action="/submit-order" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="_token" value="${csrfToken}">
            
            <!-- Selected Services -->
            <div>
                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-box-open text-purple-600 mr-2"></i>
                    Layanan Terpilih
                </h4>
                ${servicesHtml}
                <input type="hidden" name="package_selections" value='${JSON.stringify(Object.entries(selectedPackages).map(([sid, pid]) => ({service_id: sid, package_id: pid})))}'>
            </div>

            <!-- Personal Info -->
            <div>
                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-user text-purple-600 mr-2"></i>
                    Informasi Kontak
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                        <input type="text" name="company_name" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div>
                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                    Pembayaran
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                        <select name="payment_method" required class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Pilih Metode</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran *</label>
                        <select name="payment_type" required class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="full">Pembayaran Penuh</option>
                            <option value="installment">Cicilan (DP 50%)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Pembayaran (Opsional)</label>
                    <input type="file" name="payment_proof" accept="image/*" class="w-full px-4 py-2.5 border rounded-lg">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Informasi tambahan..."></textarea>
            </div>

            <!-- Submit -->
            <div class="flex gap-3">
                <button type="button" onclick="closeOrderModal()" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Pesanan
                </button>
            </div>
        </form>
    `;

    document.getElementById('orderFormContent').innerHTML = formHtml;
    document.getElementById('orderModal').classList.remove('hidden');
}

// Select service without package (Academy registrations)
function selectService(serviceId) {
    const service = servicesData.find(s => s.id === serviceId);
    if (!service) return;

    // Check service name to determine registration type
    const serviceName = service.name.toLowerCase();
    if (serviceName.includes('magang')) {
        openMagangModal();
        return;
    } else if (serviceName.includes('sertifikasi') || serviceName.includes('bnsp')) {
        openSertifikasiModal();
        return;
    }

    // Regular service - add to selection and show order form
    selectedPackages[serviceId] = null;
    showOrderForm();
}

// Open registration modals
function openMagangModal() {
    document.getElementById('magangModal').classList.remove('hidden');
}

function openSertifikasiModal() {
    document.getElementById('sertifikasiModal').classList.remove('hidden');
}

// Initialize forms on page load
document.addEventListener('DOMContentLoaded', function() {
    // Handle magang form submit
    const magangForm = document.getElementById('magangForm');
    if (magangForm) {
        magangForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/daftar/magang', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeMagangModal();
                    this.reset();
                    showToast(data.message);
                } else {
                    alert(data.message || 'Terjadi kesalahan, silakan coba lagi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan, silakan coba lagi');
            });
        });
    }

    // Handle sertifikasi form submit
    const sertifikasiForm = document.getElementById('sertifikasiForm');
    if (sertifikasiForm) {
        sertifikasiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/daftar/sertifikasi', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeSertifikasiModal();
                    this.reset();
                    showToast(data.message);
                } else {
                    alert(data.message || 'Terjadi kesalahan, silakan coba lagi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan, silakan coba lagi');
            });
        });
    }

    // Close modals when clicking outside
    ['packageModal', 'orderModal', 'magangModal', 'sertifikasiModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    });
});
