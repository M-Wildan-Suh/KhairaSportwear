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

function showRentalModal(btn) {
    const modal = document.getElementById('rentalModal');
    const rentalForm = document.getElementById('rentalForm');
    if (!modal || !rentalForm) return;

    // Ambil data dari tombol yg diklik
    const productId = btn?.dataset?.produkId;
    const stok = parseInt(btn?.dataset?.stok) || 0;

    if (!productId) {
        Swal?.fire?.({
            icon: 'error',
            title: 'Gagal',
            text: 'Produk tidak valid.',
            confirmButtonColor: '#2B6CB0'
        });
        return;
    }

    if (stok <= 0) {
        Swal?.fire?.({
            icon: 'warning',
            title: 'Stok Habis',
            text: 'Maaf, produk ini sedang tidak tersedia untuk disewa.',
            confirmButtonColor: '#2B6CB0'
        });
        return;
    }

    // Set hidden product_id di modal (PENTING!)
    const productIdInput = rentalForm.querySelector('#product_id');
    if (productIdInput) productIdInput.value = productId;

    // Set currentProductData (harga) dari dataset tombol
    window.currentProductData = {
        harga_harian: parseInt(btn?.dataset?.harian) || 0,
        harga_mingguan: parseInt(btn?.dataset?.mingguan) || 0,
        harga_bulanan: parseInt(btn?.dataset?.bulanan) || 0
    };

    // Update UI info produk di modal (optional tapi enak)
    const img = document.getElementById('modalProductImage');
    if (img) img.src = btn?.dataset?.gambar || '';

    const name = document.getElementById('modalProductName');
    if (name) name.textContent = btn?.dataset?.nama || '';

    const cat = document.getElementById('modalProductCategory');
    if (cat) cat.textContent = btn?.dataset?.kategori || '';

    // Update harga display header (optional)
    const dailyEl = document.getElementById('modalDailyPrice');
    if (dailyEl) dailyEl.textContent = `Rp ${(window.currentProductData.harga_harian || 0).toLocaleString('id-ID')}`;

    const weeklyEl = document.getElementById('modalWeeklyPrice');
    if (weeklyEl) weeklyEl.textContent = `Rp ${(window.currentProductData.harga_mingguan || 0).toLocaleString('id-ID')}`;

    const monthlyEl = document.getElementById('modalMonthlyPrice');
    if (monthlyEl) monthlyEl.textContent = `Rp ${(window.currentProductData.harga_bulanan || 0).toLocaleString('id-ID')}`;

    // Reset form
    rentalForm.reset();

    // Set qty max sesuai stok + reset qty jadi 1
    const qtyInput = rentalForm.querySelector('#quantity');
    if (qtyInput) {
        qtyInput.max = stok;
        qtyInput.value = 1;
    }

    // Set tanggal minimum (besok)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];

    const tanggalMulaiInput = rentalForm.querySelector('#tanggal_mulai');
    if (tanggalMulaiInput) {
        tanggalMulaiInput.min = tomorrowStr;
        tanggalMulaiInput.value = tomorrowStr;
    }

    // Set jumlah hari default
    const jumlahHariInput = rentalForm.querySelector('#jumlah_hari');
    if (jumlahHariInput) jumlahHariInput.value = 1;

    // Pilih durasi pertama yang tersedia dalam modal
    const durasiOptions = rentalForm.querySelectorAll('input[name="durasi"]');
    let first = null;
    durasiOptions.forEach(opt => {
        if (!first) {
            first = opt;
            opt.checked = true;
        }
    });

    // Update harga awal
    updateRentalPrice(true);

    // Show modal
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    // Trigger update visual durasi terpilih
    updateDurationVisual();
}

function closeRentalModal() {
    const modal = document.getElementById('rentalModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

function updateRentalPrice(resetDays = false) {
    const rentalForm = document.getElementById('rentalForm');
    if (!rentalForm) return;

    const selectedDuration = rentalForm.querySelector('input[name="durasi"]:checked');
    const jumlahHariInput = rentalForm.querySelector('#jumlah_hari');
    const qtyInput = rentalForm.querySelector('#quantity');
    const quantity = parseInt(qtyInput?.value) || 1;

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

    const { harga_harian, harga_mingguan, harga_bulanan } = window.currentProductData;

    let pricePerDay = 0;
    let totalPrice = 0;
    let displayText = "";

    switch (duration) {
        case 'harian':
            pricePerDay = harga_harian;
            totalPrice = pricePerDay * days * quantity;
            displayText = `${days} hari`;
            break;

        case 'mingguan':
            const weeks = days / 7;
            pricePerDay = Math.round(harga_mingguan / 7);
            totalPrice = harga_mingguan * weeks * quantity;
            displayText = `${weeks} minggu (${days} hari)`;
            break;

        case 'bulanan':
            const months = days / 30;
            pricePerDay = Math.round(harga_bulanan / 30);
            totalPrice = harga_bulanan * months * quantity;
            displayText = `${months} bulan (${days} hari)`;
            break;
    }

    const pricePerDayEl = rentalForm.querySelect

