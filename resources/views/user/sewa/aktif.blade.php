@extends('user.layouts.app')

@section('title', 'Sewa Aktif - SportWear')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 lg:px-8">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-gray-800 transition-colors">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <a href="{{ route('user.sewa.index') }}" class="hover:text-gray-800 transition-colors">Sewa</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <span class="text-gray-800 font-medium">Sewa Aktif</span>
        </nav>
    </div>

    <div class="container mx-auto px-4 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Sewa Aktif</h1>
                <p class="text-gray-600">Kelola penyewaan alat olahraga Anda</p>
            </div>
            
            <a href="{{ route('user.sewa.index') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-800-dark transition-colors">
                <i class="fas fa-plus"></i>
                <span>Sewa Baru</span>
            </a>
        </div>

        @if($sewas->count() > 0)
        <!-- Active Rentals Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            @foreach($sewas as $sewa)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300" 
                 data-aos="fade-up" 
                 data-aos-delay="{{ $loop->index * 100 }}">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ $sewa->produk->nama }}</h3>
                            <p class="text-sm text-gray-600">{{ $sewa->kode_sewa }}</p>
                        </div>
                        <div>
                            @include('components.status-badge', ['status' => $sewa->status])
                        </div>
                    </div>
                    
                    <!-- Product & Info -->
                    <div class="flex gap-4 mb-6">
                        <img src="{{ $sewa->produk->gambar_url }}" 
                             alt="{{ $sewa->produk->nama }}"
                             class="w-20 h-20 object-cover rounded-lg">
                        <div class="flex-1">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gray-800/10 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-gray-800 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Durasi</p>
                                        <p class="text-sm font-medium">{{ $sewa->durasi }} ({{ $sewa->jumlah_hari }} hari)</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-play-circle text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Mulai</p>
                                        <p class="text-sm font-medium">{{ $sewa->tanggal_mulai->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-flag-checkered text-red-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Kembali</p>
                                        <p class="text-sm font-medium">{{ $sewa->tanggal_kembali_rencana->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Total</p>
                                        <p class="text-sm font-medium">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Progress Sewa</span>
                            <span class="text-sm font-medium {{ $sewa->sisa_hari < 3 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $sewa->sisa_hari }} hari tersisa
                            </span>
                        </div>
                        <div class="relative">
                            @php
                                $totalDays = $sewa->jumlah_hari;
                                $remainingDays = $sewa->sisa_hari;
                                $elapsedDays = $totalDays - $remainingDays;
                                $percentage = min(100, ($elapsedDays / $totalDays) * 100);
                            @endphp
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-gray-800 to-green-500 rounded-full transition-all duration-500"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="absolute inset-0 flex justify-between items-center px-1">
                                @for($i = 0; $i <= $totalDays; $i++)
                                <div class="w-px h-3 {{ $i <= $elapsedDays ? 'bg-gray-800' : 'bg-gray-300' }}"></div>
                                @endfor
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Mulai</span>
                            <span>Kembali</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('user.sewa.struk', ['struk' => $sewa->id]) }}" target="_blank" {{$sewa->status === 'aktif' ? '' : 'disable'}}
                           class="flex-1 min-w-[120px] px-4 py-2 border {{$sewa->status === 'aktif' ? '' : 'opacity-50 cursor-not-allowed'}} border-gray-800 text-gray-800 font-medium rounded-lg hover:bg-gray-800/5 transition-colors text-center">
                            <i class="fas fa-eye mr-2"></i> Struk
                        </a>
                        
                        <button onclick="showExtendModal('{{ $sewa->id }}')" 
                                class="flex-1 min-w-[120px] px-4 py-2 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors {{ $sewa->sisa_hari < 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $sewa->sisa_hari < 1 ? 'disabled' : '' }}>
                            <i class="fas fa-plus mr-2"></i> Perpanjang
                        </button>
                        
                        <button onclick="showReturnModal('{{ $sewa->id }}')" 
                                class="flex-1 min-w-[120px] px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-undo mr-2"></i> Kembalikan
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($sewas->hasPages())
        <div class="mt-8">
            {{ $sewas->onEachSide(1)->links('vendor.pagination.custom') }}
        </div>
        @endif
        
        @else
        <!-- Empty State -->
        <div class="text-center py-12" data-aos="fade-up">
            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-calendar-check text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Sewa Aktif</h3>
            <p class="text-gray-600 mb-6">Mulai sewa alat olahraga favorit Anda</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('user.sewa.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-gray-800-dark transition-colors">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Sewa Sekarang</span>
                </a>
                <a href="{{ route('produk.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-800 text-gray-800 font-medium rounded-lg hover:bg-gray-800/5 transition-colors">
                    <i class="fas fa-store"></i>
                    <span>Lihat Katalog</span>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeReturnModal()"></div>
        <div class="relative bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Pengembalian Alat</h3>
                    <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="returnForm" class="space-y-6">
                    @csrf
                    <input type="hidden" id="return_sewa_id" name="sewa_id">
                    
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Kembali <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="tanggal_kembali" 
                                       id="return_tanggal_kembali"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                                       required>
                                <p class="mt-2 text-sm text-gray-500">Tanggal saat Anda mengembalikan alat</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kondisi Alat <span class="text-red-500">*</span>
                                </label>
                                <select name="kondisi_alat" 
                                        id="return_kondisi_alat"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                                        required>
                                    <option value="">Pilih kondisi</option>
                                    <option value="baik">Baik (Tidak ada kerusakan)</option>
                                    <option value="rusak_ringan">Rusak Ringan (Perlu perbaikan kecil)</option>
                                    <option value="rusak_berat">Rusak Berat (Perlu perbaikan besar)</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Kondisi</label>
                                <textarea name="catatan_kondisi" 
                                          rows="3"
                                          placeholder="Deskripsi kondisi alat..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800"></textarea>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Perhitungan Denda</h4>
                                <div id="fineCalculation" class="space-y-3">
                                    <div class="text-center py-8">
                                        <p class="text-gray-600">Pilih tanggal kembali dan kondisi alat untuk menghitung denda</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <p class="font-medium text-blue-800 mb-1">Perhatian:</p>
                                <p class="text-sm text-blue-700">Pengembalian akan diverifikasi oleh admin. Denda akan dikenakan jika terdapat keterlambatan atau kerusakan.</p>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full py-3 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-800-dark transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Ajukan Pengembalian</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Extend Modal -->
<div id="extendModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeExtendModal()"></div>
        <div class="relative bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Perpanjang Sewa</h3>
                <button onclick="closeExtendModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="extendForm" class="space-y-6">
                @csrf
                <input type="hidden" id="extend_sewa_id" name="sewa_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tambahan Hari <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="tambahan_hari" 
                           min="1" 
                           max="30" 
                           value="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                           required>
                    <p class="mt-2 text-sm text-gray-500">Maksimal 30 hari per sewa</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Perpanjangan</label>
                    <textarea name="alasan" 
                              rows="2"
                              placeholder="Alasan perpanjangan sewa..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800"></textarea>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-yellow-800 mb-1">Perhatian:</p>
                            <p class="text-sm text-yellow-700">Perpanjangan sewa akan menambah biaya sesuai durasi tambahan.</p>
                        </div>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-800-dark transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>Ajukan Perpanjangan</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Progress bar styling */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Modal animations */
#returnModal, #extendModal {
    transition: opacity 0.3s ease;
}

/* Card hover effects */
.hover\:shadow-md:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
// Modal functions
function showReturnModal(sewaId) {
    document.getElementById('return_sewa_id').value = sewaId;
    
    // Set default return date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('return_tanggal_kembali').value = today;
    
    // Reset form
    document.getElementById('return_kondisi_alat').value = '';
    document.getElementById('fineCalculation').innerHTML = `
        <div class="text-center py-8">
            <p class="text-gray-600">Pilih tanggal kembali dan kondisi alat untuk menghitung denda</p>
        </div>
    `;
    
    document.getElementById('returnModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReturnModal() {
    document.getElementById('returnModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showExtendModal(sewaId) {
    document.getElementById('extend_sewa_id').value = sewaId;
    document.getElementById('extendModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeExtendModal() {
    document.getElementById('extendModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Hitung denda saat input berubah
function attachCalculationEvents() {
    const tanggalInput = document.getElementById('return_tanggal_kembali');
    const kondisiSelect = document.getElementById('return_kondisi_alat');
    
    const calculateHandler = () => {
        const sewaId = document.getElementById('return_sewa_id').value;
        const tanggalKembali = tanggalInput.value;
        const kondisiAlat = kondisiSelect.value;
        
        if (sewaId && tanggalKembali && kondisiAlat) {
            calculateFines(sewaId, tanggalKembali, kondisiAlat);
        }
    };
    
    tanggalInput?.addEventListener('change', calculateHandler);
    kondisiSelect?.addEventListener('change', calculateHandler);
}

// Fungsi hitung denda
async function calculateFines(sewaId, tanggalKembali, kondisiAlat) {
    const fineCalculationDiv = document.getElementById('fineCalculation');
    
    // Tampilkan loading
    fineCalculationDiv.innerHTML = `
        <div class="text-center py-8">
            <div class="w-8 h-8 border-4 border-gray-800 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600">Menghitung denda...</p>
        </div>
    `;
    
    try {
        // PERBAIKAN: Gunakan URL langsung atau route dengan nama yang benar
        const response = await fetch("{{ route('user.sewa.calculate-denda') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                sewa_id: sewaId,
                tanggal_kembali: tanggalKembali,
                kondisi_alat: kondisiAlat
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const fines = data.data;
            let fineHtml = '';
            
            if (fines.total_denda > 0) {
                fineHtml = `
                    <div class="space-y-3">
                        ${fines.keterlambatan_hari > 0 ? `
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Keterlambatan:</span>
                            <span class="font-medium">${fines.keterlambatan_hari} hari × ${fines.formatted.tarif_denda_per_hari}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Denda Keterlambatan:</span>
                            <span class="text-red-600 font-medium">${fines.formatted.denda_keterlambatan}</span>
                        </div>
                        ` : ''}
                        
                        ${fines.denda_kerusakan > 0 ? `
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Denda Kerusakan:</span>
                            <span class="text-red-600 font-medium">${fines.formatted.denda_kerusakan}</span>
                        </div>
                        ` : ''}
                        
                        <div class="pt-3 border-t border-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900">Total Denda:</span>
                                <span class="text-xl font-bold text-red-600">${fines.formatted.total_denda}</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                fineHtml = `
                    <div class="text-center py-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <p class="text-green-600 font-medium">Tidak ada denda</p>
                    </div>
                `;
            }
            
            fineCalculationDiv.innerHTML = fineHtml;
        } else {
            fineCalculationDiv.innerHTML = `
                <div class="text-center py-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                    </div>
                    <p class="text-red-600 font-medium">${data.message || 'Gagal menghitung denda'}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        fineCalculationDiv.innerHTML = `
            <div class="text-center py-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
                <p class="text-red-600 font-medium">Terjadi kesalahan jaringan</p>
            </div>
        `;
    }
}

// Form submission untuk pengembalian
document.addEventListener('DOMContentLoaded', function() {
    attachCalculationEvents();
    
    document.getElementById('returnForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const sewaId = document.getElementById('return_sewa_id').value;
        const tanggalKembali = document.getElementById('return_tanggal_kembali').value;
        const kondisiAlat = document.getElementById('return_kondisi_alat').value;
        const catatanKondisi = document.querySelector('textarea[name="catatan_kondisi"]').value;
        
        if (!confirm('Apakah Anda yakin ingin mengajukan pengembalian?')) return;
        
        const submitButton = form.querySelector('button[type="submit"]');
        const originalContent = submitButton.innerHTML;
        
        // Tampilkan loading
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengajukan...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch(`/user/sewa/${sewaId}/pengembalian`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    tanggal_kembali: tanggalKembali,
                    kondisi_alat: kondisiAlat,
                    catatan_kondisi: catatanKondisi
                })
            });
            
            const data = await response.json();
            
            // Kembalikan tombol ke keadaan semula
            submitButton.innerHTML = originalContent;
            submitButton.disabled = false;
            
            if (data.success) {
                showToast('success', data.message);
                closeReturnModal();
                
                // Redirect setelah 1.5 detik
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                }, 1500);
            } else {
                showToast('error', data.message);
            }
        } catch (error) {
            // Kembalikan tombol ke keadaan semula
            submitButton.innerHTML = originalContent;
            submitButton.disabled = false;
            
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan jaringan');
        }
    });
    
    // Form submission untuk perpanjangan
    document.getElementById('extendForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const sewaId = document.getElementById('extend_sewa_id').value;
        const tambahanHari = document.querySelector('input[name="tambahan_hari"]').value;
        const alasan = document.querySelector('textarea[name="alasan"]').value;
        
        if (!confirm('Perpanjang sewa ini?')) return;
        
        const submitButton = form.querySelector('button[type="submit"]');
        const originalContent = submitButton.innerHTML;
        
        // Show loading
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengajukan...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch(`/user/sewa/${sewaId}/extend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    tambahan_hari: tambahanHari,
                    alasan: alasan
                })
            });
            
            const data = await response.json();
            
            // Restore button
            submitButton.innerHTML = originalContent;
            submitButton.disabled = false;
            
            if (data.success) {
                closeExtendModal();
                showToast('success', data.message);
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                }, 1500);
            } else {
                showToast('error', data.message);
            }
        } catch (error) {
            // Restore button
            submitButton.innerHTML = originalContent;
            submitButton.disabled = false;
            
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan jaringan');
        }
    });
});

// Toast notification function
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in-right ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // AOS initialization
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    }
    
    // Close modals on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReturnModal();
            closeExtendModal();
        }
    });
});
</script>
@endpush