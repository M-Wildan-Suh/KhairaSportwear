<!-- resources/views/user/components/sewa-modal.blade.php -->
<div id="rentalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-4 mx-auto p-4 w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-800 to-gray-950 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Form Penyewaan</h3>
                    </div>
                    <button onclick="closeRentalModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="rentalForm">
                    @csrf
                    <input type="hidden" id="product_id" name="product_id" value="{{ $produk->id }}">

                    <!-- Product Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                                <img id="modalProductImage" src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 id="modalProductName" class="font-bold text-gray-900 mb-1">{{ $produk->nama }}</h4>
                                <p id="modalProductCategory" class="text-sm text-gray-600">{{ $produk->kategori->nama }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="font-semibold text-emerald-600" id="modalDailyPrice">
                                    Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">Harian</div>
                            </div>
                            <div class="border-x border-gray-200">
                                <div class="font-semibold text-emerald-600" id="modalWeeklyPrice">
                                    Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">Mingguan</div>
                            </div>
                            <div>
                                <div class="font-semibold text-emerald-600" id="modalMonthlyPrice">
                                    Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">Bulanan</div>
                            </div>
                        </div>
                        <!-- Quantity -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quantity
                            </label>

                            <div class="flex items-center">
                                <button type="button" onclick="changeRentalQty(-1)" class="w-10 h-10 rounded-l-lg">
                                    <i class="fas fa-minus"></i>
                                </button>

                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                    max="{{ $produk->stok_tersedia }}"
                                    class="w-16 h-10 text-center border-y border-gray-300 focus:outline-none" readonly>

                                <button type="button" onclick="changeRentalQty(1)" class="w-10 h-10 rounded-r-lg">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Form -->
                    <div class="space-y-6">
                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Sewa</label>
                            <div class="grid grid-cols-3 gap-3">
                                @php
                                    $durations = [
                                        'harian' => [
                                            'label' => 'Harian',
                                            'available' => $produk->harga_sewa_harian > 0,
                                        ],
                                        'mingguan' => [
                                            'label' => 'Mingguan',
                                            'available' => $produk->harga_sewa_mingguan > 0,
                                        ],
                                        'bulanan' => [
                                            'label' => 'Bulanan',
                                            'available' => $produk->harga_sewa_bulanan > 0,
                                        ],
                                    ];
                                @endphp
                                @foreach ($durations as $value => $info)
                                    @if ($info['available'])
                                        <label class="duration-option relative">
                                            <input type="radio" name="durasi" value="{{ $value }}"
                                                class="sr-only" required {{ $loop->first ? 'checked' : '' }}>
                                            <div
                                                class="w-full p-4 border-2 border-gray-200 rounded-xl text-center cursor-pointer hover:border-primary transition-colors duration-200">
                                                <div class="font-semibold text-gray-900">{{ $info['label'] }}</div>
                                            </div>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Duration Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Hari</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_hari" id="jumlah_hari" value="1"
                                        min="1" max="30"
                                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                        required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 text-sm">hari</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                    required>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="3"
                                class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                placeholder="Contoh: Butuh alat untuk turnamen tanggal..."></textarea>
                        </div>

                        <!-- Price Summary -->
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Ringkasan Biaya</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Harga per hari:</span>
                                    <span class="font-semibold text-gray-900" id="pricePerDay">
                                        @if ($produk->harga_sewa_harian)
                                            Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}/hari
                                        @else
                                            Rp 0
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jumlah hari:</span>
                                    <span class="font-semibold text-gray-900" id="daysCount">1 hari</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-bold text-gray-900">Total Biaya:</span>
                                        <span class="text-2xl font-bold text-primary" id="totalPrice">
                                            @if ($produk->harga_sewa_harian)
                                                Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                            @else
                                                Rp 0
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" id="submitRentalBtn"
                                class="w-full px-6 py-4 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-950 transition-colors duration-200 flex items-center justify-center gap-3">
                                <i class="fas fa-cart-plus"></i>
                                <span>Tambah ke Keranjang</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add this JavaScript section to your main file -->
<script>
    // Inisialisasi data produk untuk modal
    const productData = {
        id: {{ $produk->id }},
        nama: "{{ $produk->nama }}",
        kategori: "{{ $produk->kategori->nama }}",
        gambar: "{{ $produk->gambar_url }}",
        harga_harian: {{ $produk->harga_sewa_harian ?? 0 }},
        harga_mingguan: {{ $produk->harga_sewa_mingguan ?? 0 }},
        harga_bulanan: {{ $produk->harga_sewa_bulanan ?? 0 }}
    };

    // Simpan data produk di window object untuk diakses oleh sewa-modal.js
    window.currentProductData = productData;

    // Fungsi untuk membuka modal
    function showRentalModal() {
        // Cek stok tersedia
        @if ($produk->stok_tersedia <= 0)
            Swal.fire({
                icon: 'warning',
                title: 'Stok Habis',
                text: 'Maaf, produk ini sedang tidak tersedia untuk disewa.',
                confirmButtonColor: '#2B6CB0'
            });
            return;
        @endif

        // Cek apakah produk bisa disewa
        @if (!in_array($produk->tipe, ['sewa', 'both']))
            Swal.fire({
                icon: 'info',
                title: 'Tidak Tersedia',
                text: 'Produk ini hanya tersedia untuk dijual.',
                confirmButtonColor: '#2B6CB0'
            });
            return;
        @endif

        // Set tanggal minimum (besok)
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];

        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        if (tanggalMulaiInput) {
            tanggalMulaiInput.min = tomorrowStr;
            tanggalMulaiInput.value = tomorrowStr;
        }

        // Reset dan pilih durasi default yang tersedia
        const rentalForm = document.getElementById('rentalForm');
        const durasiOptions = document.querySelectorAll('input[name="durasi"]');
        const jumlahHariInput = document.getElementById('jumlah_hari');

        if (rentalForm) rentalForm.reset();
        if (jumlahHariInput) jumlahHariInput.value = 1;

        // Pilih durasi pertama yang tersedia
        let firstAvailableDuration = null;
        durasiOptions.forEach(option => {
            if (!firstAvailableDuration) {
                firstAvailableDuration = option;
                option.checked = true;
            }
        });

        const quantity = parseInt(document.getElementById('quantity')?.value) || 1;

        // Update harga awal
        updateRentalPrice();

        // Tampilkan modal
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    // Fungsi untuk menutup modal
    function closeRentalModal() {
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Fungsi update harga sewa
    function updateRentalPrice(resetDays = false) {
        const selectedDuration = document.querySelector('input[name="durasi"]:checked');
        const jumlahHariInput = document.getElementById('jumlah_hari');
        const quantity = parseInt(document.getElementById('quantity')?.value) || 1;
        

        if (!selectedDuration || !window.currentProductData || !jumlahHariInput) return;

        const duration = selectedDuration.value;
        let days = parseInt(jumlahHariInput.value) || 1;

        if (resetDays) {
            if (duration === 'harian') days = 1;
            if (duration === 'mingguan') days = 7;
            if (duration === 'bulanan') days = 30;
        }

        if (!resetDays) {
            switch (duration) {
                case 'mingguan':
                    if (days < 7) days = 7;
                    days = Math.ceil(days / 7) * 7;
                    break;
                case 'bulanan':
                    if (days < 30) days = 30;
                    days = Math.ceil(days / 30) * 30;
                    break;
                default:
                    if (days < 1) days = 1;
            }
        }

        jumlahHariInput.value = days;

        const {
            harga_harian,
            harga_mingguan,
            harga_bulanan
        } = window.currentProductData;

        let pricePerDay = 0;
        let totalPrice = 0;
        let displayText = "";

        switch (duration) {
            case 'harian':
                pricePerDay = harga_harian;
                totalPrice = pricePerDay * days *quantity;
                displayText = `${days} hari`;
                break;

            case 'mingguan':
                const weeks = days / 7;
                pricePerDay = Math.round(harga_mingguan / 7);
                totalPrice = harga_mingguan * weeks *quantity;
                displayText = `${weeks} minggu (${days} hari)`;
                break;

            case 'bulanan':
                const months = days / 30;
                pricePerDay = Math.round(harga_bulanan / 30);
                totalPrice = harga_bulanan * months *quantity;
                displayText = `${months} bulan (${days} hari)`;
                break;
        }

        document.getElementById('pricePerDay').textContent =
            duration === 'harian' ?
            `Rp ${pricePerDay.toLocaleString('id-ID')}/hari` :
            `~Rp ${pricePerDay.toLocaleString('id-ID')}/hari`;

        document.getElementById('daysCount').textContent = displayText;
        document.getElementById('totalPrice').textContent =
            `Rp ${totalPrice.toLocaleString('id-ID')}`;
    }


    // Event Listeners untuk modal
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="durasi"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateRentalPrice(true);
            });
        });


        // Event: Jumlah hari berubah
        document.getElementById('jumlah_hari')?.addEventListener('input', function() {
            updateRentalPrice(false);
        });


        // Event: Submit form
        const rentalForm = document.getElementById('rentalForm');
        if (rentalForm) {
            rentalForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const durasi = document.querySelector('input[name="durasi"]:checked')?.value;
                const jumlahHari = parseInt(document.getElementById('jumlah_hari').value) || 1;
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const catatan = document.querySelector('textarea[name="catatan"]').value;
                const submitBtn = document.getElementById('submitRentalBtn');

                if (!submitBtn) return;

                const originalContent = submitBtn.innerHTML;

                // Tampilkan loading
                submitBtn.innerHTML = `
                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>Memproses...</span>
            `;
                submitBtn.disabled = true;

                try {
                    const response = await fetch('/user/keranjang', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: {{ $produk->id }},
                            type: 'sewa',
                            quantity: quantity,
                            options: {
                                durasi: durasi,
                                jumlah_hari: jumlahHari,
                                tanggal_mulai: tanggalMulai,
                                catatan: catatan
                            }
                        })
                    });


                    const data = await response.json();

                    if (data.success) {
                        // Tutup modal
                        closeRentalModal();

                        // Update cart badge
                        if (typeof window.updateCartCount === 'function') {
                            window.updateCartCount(data.cart_count);
                        } else {
                            window.dispatchEvent(new CustomEvent('cartUpdated', {
                                detail: {
                                    count: data.cart_count
                                }
                            }));
                        }

                        // Tampilkan pesan sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Produk telah ditambahkan ke keranjang',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message ||
                            'Terjadi kesalahan saat menambahkan ke keranjang',
                        confirmButtonColor: '#2B6CB0'
                    });
                } finally {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.disabled = false;
                }
            });
        }

        // Close modal on background click
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRentalModal();
                }
            });
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('rentalModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeRentalModal();
                }
            }
        });

        // Update visual selection for duration options
        document.querySelectorAll('input[name="durasi"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.duration-option').forEach(option => {
                    const container = option.querySelector('div');
                    if (container) {
                        const isChecked = option.querySelector('input').checked;
                        if (isChecked) {
                            container.classList.add('border-primary', 'bg-primary/5');
                            container.classList.remove('border-gray-200');
                        } else {
                            container.classList.remove('border-primary',
                                'bg-primary/5');
                            container.classList.add('border-gray-200');
                        }
                    }
                });
            });
        });

        // Trigger initial update untuk durasi yang terpilih
        document.querySelectorAll('input[name="durasi"]:checked').forEach(radio => {
            radio.dispatchEvent(new Event('change'));
        });
    });

    function changeRentalQty(delta) {
        const qtyInput = document.getElementById('quantity');
        let qty = parseInt(qtyInput.value) || 1;
        const max = parseInt(qtyInput.max) || 1;

        qty += delta;
        if (qty < 1) qty = 1;
        if (qty > max) qty = max;

        qtyInput.value = qty;
        updateRentalPrice();
    }
</script>
