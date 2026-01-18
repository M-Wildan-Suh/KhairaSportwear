// public/js/sewa-modal.js

// ================= MODAL FUNCTIONS =================
let currentProductData = null;

// Show modal dengan data produk
function showSewaModal(productId) {
    console.log('Opening sewa modal for product:', productId);
    
    // Cari produk di DOM berdasarkan ID
    const productCards = document.querySelectorAll('.group');
    let productCard = null;
    
    // Cari produk dengan ID yang sesuai
    for (const card of productCards) {
        const sewaButton = card.querySelector('button[onclick*="showSewaModal"]');
        if (sewaButton && sewaButton.getAttribute('onclick').includes(productId)) {
            productCard = card;
            break;
        }
    }
    
    if (!productCard) {
        console.error('Product not found in DOM for ID:', productId);
        alert('Produk tidak ditemukan');
        return;
    }
    
    // Ambil data dari card
    const productName = productCard.querySelector('h3')?.textContent || 'Produk';
    const productCategory = productCard.querySelector('.text-xs')?.textContent || 'Kategori';
    const productImage = productCard.querySelector('img')?.src || '/images/default.jpg';
    
    // Ambil harga dari card (ada 3 kolom harga)
    const priceElements = productCard.querySelectorAll('.text-emerald-600');
    const harga_harian = extractPrice(priceElements[0]?.textContent);
    const harga_mingguan = extractPrice(priceElements[1]?.textContent);
    const harga_bulanan = extractPrice(priceElements[2]?.textContent);
    
    // Simpan data produk
    currentProductData = {
        id: productId,
        nama: productName,
        kategori: productCategory,
        gambar: productImage,
        harga_harian: harga_harian,
        harga_mingguan: harga_mingguan,
        harga_bulanan: harga_bulanan
    };
    
    // Update modal dengan data produk
    document.getElementById('product_id').value = productId;
    document.getElementById('modalProductName').textContent = currentProductData.nama;
    document.getElementById('modalProductCategory').textContent = currentProductData.kategori;
    document.getElementById('modalProductImage').src = currentProductData.gambar;
    document.getElementById('modalProductImage').alt = currentProductData.nama;
    
    // Update harga di modal
    document.getElementById('modalDailyPrice').textContent = formatRupiah(currentProductData.harga_harian);
    document.getElementById('modalWeeklyPrice').textContent = formatRupiah(currentProductData.harga_mingguan);
    document.getElementById('modalMonthlyPrice').textContent = formatRupiah(currentProductData.harga_bulanan);
    
    // Set tanggal minimum (besok)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').min = tomorrowStr;
    document.getElementById('tanggal_mulai').value = tomorrowStr;
    
    // Reset form dan pilih default
    document.getElementById('rentalForm').reset();
    document.querySelector('input[name="durasi"][value="harian"]').checked = true;
    document.getElementById('jumlah_hari').value = 1;
    
    // Update harga
    updatePrice();
    
    // Tampilkan modal
    const modal = document.getElementById('rentalModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    console.log('Modal should be visible now');
}

// Close modal
function closeRentalModal() {
    document.getElementById('rentalModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Helper: extract price from string
function extractPrice(priceText) {
    if (!priceText) return 0;
    return parseInt(priceText.replace('Rp ', '').replace(/\./g, '')) || 0;
}

// Format rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Price Calculation
function updatePrice() {
    const selectedDuration = document.querySelector('input[name="durasi"]:checked');
    const days = parseInt(document.getElementById('jumlah_hari').value) || 1;
    
    if (!selectedDuration) return;
    
    const duration = selectedDuration.value;
    let pricePerDay = 0;
    let totalPrice = 0;
    
    // Ambil harga dari data produk
    const harga_harian = currentProductData?.harga_harian || 0;
    const harga_mingguan = currentProductData?.harga_mingguan || 0;
    const harga_bulanan = currentProductData?.harga_bulanan || 0;
    
    switch(duration) {
        case 'harian':
            pricePerDay = harga_harian;
            totalPrice = harga_harian * days;
            break;
        case 'mingguan':
            pricePerDay = Math.round(harga_mingguan / 7);
            totalPrice = harga_mingguan * Math.ceil(days / 7);
            break;
        case 'bulanan':
            pricePerDay = Math.round(harga_bulanan / 30);
            totalPrice = harga_bulanan * Math.ceil(days / 30);
            break;
    }
    
    // Update display
    document.getElementById('pricePerDay').textContent = formatRupiah(pricePerDay);
    document.getElementById('daysCount').textContent = `${days} hari`;
    document.getElementById('totalPrice').textContent = formatRupiah(totalPrice);
}

// Initialize modal functionality
function initModal() {
    // Event listeners for price updates
    document.querySelectorAll('input[name="durasi"]').forEach(radio => {
        radio.addEventListener('change', updatePrice);
    });
    
    document.getElementById('jumlah_hari').addEventListener('input', updatePrice);
    
    // Rental Form Submission
    document.getElementById('rentalForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitRentalBtn');
        const originalContent = submitBtn.innerHTML;
        
        // Show loading
        submitBtn.innerHTML = `
            <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <span>Memproses...</span>
        `;
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('/user/keranjang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: formData.get('product_id'),
                    type: 'sewa',
                    quantity: 1,
                    options: {
                        durasi: formData.get('durasi'),
                        jumlah_hari: formData.get('jumlah_hari'),
                        tanggal_mulai: formData.get('tanggal_mulai'),
                        catatan: formData.get('catatan')
                    }
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Close modal
                closeRentalModal();
                
                // Update cart badge
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount(data.cart_count);
                } else {
                    // Fallback: dispatch event
                    window.dispatchEvent(new CustomEvent('cartUpdated', {
                        detail: { count: data.cart_count }
                    }));
                }
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Produk telah ditambahkan ke keranjang',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert('Produk telah ditambahkan ke keranjang');
                }
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Submit error:', error);
            
            if (typeof Swal !== 'undefined') {
                await Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Terjadi kesalahan saat menambahkan ke keranjang',
                    confirmButtonColor: '#2B6CB0'
                });
            } else {
                alert('Error: ' + error.message);
            }
        } finally {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRentalModal();
        }
    });
    
    // Close modal on background click
    const modal = document.getElementById('rentalModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRentalModal();
            }
        });
    }
    
    console.log('Modal functions initialized');
}

// Export functions for global use
window.showSewaModal = showSewaModal;
window.closeRentalModal = closeRentalModal;
window.initModal = initModal;

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('rentalModal')) {
        initModal();
    }
});