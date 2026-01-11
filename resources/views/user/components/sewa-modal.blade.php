<!-- Sewa Options Modal -->
<div id="sewaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeSewaModal()"></div>
        <div class="relative bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Pilihan Sewa</h3>
                <button onclick="closeSewaModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="sewaForm" class="space-y-4">
                <input type="hidden" id="sewaType" value="sewa">
                <input type="hidden" id="productId" value="{{ $produk->id }}">
                <input type="hidden" id="isCheckout" value="false">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Sewa</label>
                    <select name="durasi" id="durasiSewa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                        <option value="">Pilih durasi</option>
                        @if($produk->harga_sewa_harian)
                        <option value="harian" data-harga="{{ $produk->harga_sewa_harian }}">Harian</option>
                        @endif
                        @if($produk->harga_sewa_mingguan)
                        <option value="mingguan" data-harga="{{ $produk->harga_sewa_mingguan }}">Mingguan</option>
                        @endif
                        @if($produk->harga_sewa_bulanan)
                        <option value="bulanan" data-harga="{{ $produk->harga_sewa_bulanan }}">Bulanan</option>
                        @endif
                    </select>
                </div>
                
                <div id="harianFields" class="hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Hari</label>
                        <input type="number" name="jumlah_hari" id="jumlahHari" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               min="1" max="30" value="1" required>
                        <p class="mt-1 text-sm text-gray-500">Maksimal 30 hari</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggalMulai" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                </div>
                
                <div id="mingguanFields" class="hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Minggu</label>
                        <input type="number" name="jumlah_minggu" id="jumlahMinggu" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               min="1" max="4" value="1" required>
                        <p class="mt-1 text-sm text-gray-500">Maksimal 4 minggu (28 hari)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai_mingguan" id="tanggalMulaiMingguan" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                </div>
                
                <div id="bulananFields" class="hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bulan</label>
                        <input type="number" name="jumlah_bulan" id="jumlahBulan" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               min="1" max="12" value="1" required>
                        <p class="mt-1 text-sm text-gray-500">Maksimal 12 bulan</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai_bulanan" id="tanggalMulaiBulanan" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Harga per durasi:</span>
                        <span id="hargaPerDurasi" class="font-semibold text-primary">-</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Durasi:</span>
                        <span id="totalDurasi" class="font-semibold">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total:</span>
                        <span id="totalHargaSewa" class="text-xl font-bold text-primary">-</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan" id="catatanSewa" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                </div>
                
                <button type="submit" class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                    <span id="submitText">Tambahkan ke Keranjang</span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables untuk modal sewa
let sewaProductId = {{ $produk->id }};
let sewaProductSlug = '{{ $produk->slug }}';
let sewaHargaHarian = {{ $produk->harga_sewa_harian ?? 0 }};
let sewaHargaMingguan = {{ $produk->harga_sewa_mingguan ?? 0 }};
let sewaHargaBulanan = {{ $produk->harga_sewa_bulanan ?? 0 }};

// Show sewa modal
function showSewaOptions(checkout = false) {
    const modal = document.getElementById('sewaModal');
    const isCheckout = document.getElementById('isCheckout');
    const submitText = document.getElementById('submitText');
    
    isCheckout.value = checkout ? 'true' : 'false';
    submitText.textContent = checkout ? 'Lanjut ke Checkout' : 'Tambahkan ke Keranjang';
    
    // Reset form
    document.getElementById('sewaForm').reset();
    document.querySelectorAll('[id$="Fields"]').forEach(field => {
        field.classList.add('hidden');
    });
    
    // Reset harga display
    document.getElementById('hargaPerDurasi').textContent = '-';
    document.getElementById('totalDurasi').textContent = '-';
    document.getElementById('totalHargaSewa').textContent = '-';
    
    // Set default dates
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    document.getElementById('tanggalMulai').value = tomorrowStr;
    document.getElementById('tanggalMulaiMingguan').value = tomorrowStr;
    document.getElementById('tanggalMulaiBulanan').value = tomorrowStr;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close modal
function closeSewaModal() {
    const modal = document.getElementById('sewaModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Handle durasi change
document.addEventListener('DOMContentLoaded', function() {
    const durasiSelect = document.getElementById('durasiSewa');
    if (durasiSelect) {
        durasiSelect.addEventListener('change', function() {
            const value = this.value;
            const selectedOption = this.options[this.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            
            // Hide all fields
            document.querySelectorAll('[id$="Fields"]').forEach(field => {
                field.classList.add('hidden');
            });
            
            // Show relevant fields
            if (value === 'harian') {
                document.getElementById('harianFields').classList.remove('hidden');
                updateHargaDisplay('harian', harga, document.getElementById('jumlahHari').value);
            } else if (value === 'mingguan') {
                document.getElementById('mingguanFields').classList.remove('hidden');
                updateHargaDisplay('mingguan', harga, document.getElementById('jumlahMinggu').value);
            } else if (value === 'bulanan') {
                document.getElementById('bulananFields').classList.remove('hidden');
                updateHargaDisplay('bulanan', harga, document.getElementById('jumlahBulan').value);
            }
        });
    }
    
    // Handle jumlah changes
    document.getElementById('jumlahHari')?.addEventListener('input', function() {
        const durasi = document.getElementById('durasiSewa').value;
        const harga = document.getElementById('durasiSewa').options[document.getElementById('durasiSewa').selectedIndex].getAttribute('data-harga');
        updateHargaDisplay('harian', harga, this.value);
    });
    
    document.getElementById('jumlahMinggu')?.addEventListener('input', function() {
        const durasi = document.getElementById('durasiSewa').value;
        const harga = document.getElementById('durasiSewa').options[document.getElementById('durasiSewa').selectedIndex].getAttribute('data-harga');
        updateHargaDisplay('mingguan', harga, this.value);
    });
    
    document.getElementById('jumlahBulan')?.addEventListener('input', function() {
        const durasi = document.getElementById('durasiSewa').value;
        const harga = document.getElementById('durasiSewa').options[document.getElementById('durasiSewa').selectedIndex].getAttribute('data-harga');
        updateHargaDisplay('bulanan', harga, this.value);
    });
    
    // Handle form submission
    document.getElementById('sewaForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const isCheckout = document.getElementById('isCheckout').value === 'true';
        submitSewaForm(isCheckout);
    });
});

// Update harga display
function updateHargaDisplay(durasi, hargaPerUnit, jumlah) {
    const harga = parseInt(hargaPerUnit) || 0;
    const total = harga * jumlah;
    
    document.getElementById('hargaPerDurasi').textContent = formatRupiah(harga);
    
    if (durasi === 'harian') {
        document.getElementById('totalDurasi').textContent = `${jumlah} hari`;
    } else if (durasi === 'mingguan') {
        document.getElementById('totalDurasi').textContent = `${jumlah} minggu`;
    } else if (durasi === 'bulanan') {
        document.getElementById('totalDurasi').textContent = `${jumlah} bulan`;
    }
    
    document.getElementById('totalHargaSewa').textContent = formatRupiah(total);
}

// Format rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Submit sewa form
async function submitSewaForm(checkout = false) {
    const form = document.getElementById('sewaForm');
    const formData = new FormData(form);
    const durasi = document.getElementById('durasiSewa').value;
    const quantity = document.getElementById('quantity').value;
    
    // Prepare data berdasarkan durasi
    let options = {
        durasi: durasi
    };
    
    if (durasi === 'harian') {
        options.jumlah_hari = formData.get('jumlah_hari');
        options.tanggal_mulai = formData.get('tanggal_mulai');
    } else if (durasi === 'mingguan') {
        options.jumlah_minggu = formData.get('jumlah_minggu');
        options.tanggal_mulai = formData.get('tanggal_mulai_mingguan');
    } else if (durasi === 'bulanan') {
        options.jumlah_bulan = formData.get('jumlah_bulan');
        options.tanggal_mulai = formData.get('tanggal_mulai_bulanan');
    }
    
    options.catatan = formData.get('catatan');
    
    // Calculate total price
    let totalHarga = 0;
    const selectedOption = document.getElementById('durasiSewa').options[document.getElementById('durasiSewa').selectedIndex];
    const hargaPerUnit = parseInt(selectedOption.getAttribute('data-harga'));
    
    if (durasi === 'harian') {
        totalHarga = hargaPerUnit * parseInt(options.jumlah_hari);
    } else if (durasi === 'mingguan') {
        totalHarga = hargaPerUnit * parseInt(options.jumlah_minggu);
    } else if (durasi === 'bulanan') {
        totalHarga = hargaPerUnit * parseInt(options.jumlah_bulan);
    }
    
    options.total_harga = totalHarga;
    
    // Panggil fungsi addToCart
    await addToCart('sewa', quantity, checkout, options);
    closeSewaModal();
}
</script>
@endpush