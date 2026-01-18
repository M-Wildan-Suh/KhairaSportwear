// resources/js/sewa-modal.js
console.log('Sewa modal JS loaded - Enhanced Version');

// ================= GLOBAL STATE =================
let currentProductData = null;
let isModalInitialized = false;

// ================= HELPER FUNCTIONS =================
/**
 * Helper: Extract price from string
 */
function extractPrice(priceText) {
    if (!priceText) return 0;
    try {
        const cleaned = priceText
            .replace(/Rp\s*/gi, '')  // Case insensitive
            .replace(/\./g, '')
            .replace(',', '.')  // Handle decimal separators
            .trim();
        return parseInt(cleaned) || 0;
    } catch (error) {
        console.warn('Error extracting price:', error);
        return 0;
    }
}

/**
 * Format number to Rupiah
 */
function formatRupiah(angka) {
    if (angka === null || angka === undefined) return 'Rp 0';
    try {
        return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    } catch (error) {
        console.warn('Error formatting rupiah:', error);
        return 'Rp 0';
    }
}

/**
 * Get tomorrow's date in YYYY-MM-DD format
 */
function getTomorrowDate() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
}

/**
 * Validate and adjust days based on duration
 */
function adjustDaysForDuration(days, duration) {
    let adjustedDays = parseInt(days) || 1;
    
    switch(duration) {
        case 'mingguan':
            if (adjustedDays < 7) adjustedDays = 7;
            adjustedDays = Math.ceil(adjustedDays / 7) * 7;
            break;
        case 'bulanan':
            if (adjustedDays < 30) adjustedDays = 30;
            adjustedDays = Math.ceil(adjustedDays / 30) * 30;
            break;
        default: // harian
            if (adjustedDays < 1) adjustedDays = 1;
            if (adjustedDays > 365) adjustedDays = 365; // Set max limit
    }
    
    return adjustedDays;
}

/**
 * Update visual selection for duration options
 */
function updateDurationVisualSelection() {
    document.querySelectorAll('.duration-option').forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        const container = option.querySelector('div');
        
        if (container) {
            if (radio.checked) {
                container.classList.add('border-primary', 'bg-primary/5');
                container.classList.remove('border-gray-200');
            } else {
                container.classList.remove('border-primary', 'bg-primary/5');
                container.classList.add('border-gray-200');
            }
        }
    });
}

// ================= MODAL FUNCTIONS =================
/**
 * Show modal dengan data produk
 */
async function showSewaModal(productId) {
    try {
        console.log('Opening sewa modal for product:', productId);
        
        if (!productId) {
            throw new Error('Product ID tidak valid');
        }
        
        // Initialize modal jika belum
        if (!isModalInitialized) {
            initModal();
        }
        
        // Load product data - try multiple methods
        const productData = await fetchProductData(productId);
        
        if (!productData) {
            throw new Error('Data produk tidak ditemukan');
        }
        
        currentProductData = productData;
        
        // Update modal UI dengan data produk
        updateModalUI(productData);
        
        // Reset dan setup form
        resetFormDefaults();
        
        // Tampilkan modal
        showModal();
        
        console.log('Modal opened successfully');
        
    } catch (error) {
        console.error('Error opening modal:', error);
        showError(error.message || 'Gagal membuka form penyewaan');
    }
}

/**
 * Fetch product data from DOM or API
 */
async function fetchProductData(productId) {
    // Method 1: Try to find in DOM first
    const productCard = findProductCard(productId);
    if (productCard) {
        return extractProductDataFromCard(productCard, productId);
    }
    
    // Method 2: Fetch from API (if needed)
    console.log('Product not found in DOM, trying API...');
    try {
        return await fetchProductFromAPI(productId);
    } catch (apiError) {
        console.warn('API fetch failed:', apiError);
        throw new Error('Produk tidak tersedia');
    }
}

/**
 * Find product card in DOM
 */
function findProductCard(productId) {
    const productCards = document.querySelectorAll('[data-product-id], .product-card, .group');
    
    for (const card of productCards) {
        // Check data attribute first
        if (card.dataset.productId === productId.toString()) {
            return card;
        }
        
        // Check onclick attribute
        const sewaButton = card.querySelector(`button[onclick*="${productId}"]`);
        if (sewaButton) {
            return card;
        }
    }
    
    return null;
}

/**
 * Extract product data from card element
 */
function extractProductDataFromCard(card, productId) {
    // Nama produk
    const productName = card.querySelector('h3, .product-name, [data-name]')?.textContent || 
                       card.dataset.name || 
                       'Produk';
    
    // Kategori
    const productCategory = card.querySelector('.category, [data-category], .inline-flex span')?.textContent || 
                          card.dataset.category || 
                          'Kategori';
    
    // Gambar
    const productImage = card.querySelector('img')?.src || 
                        card.dataset.image || 
                        '/images/default-product.jpg';
    
    // Harga - cari semua elemen dengan class harga atau data attributes
    const hargaElements = card.querySelectorAll('.price, [data-price], .text-emerald-600');
    let harga_harian = 0, harga_mingguan = 0, harga_bulanan = 0;
    
    if (hargaElements.length >= 3) {
        // Asumsi: harian, mingguan, bulanan
        harga_harian = extractPrice(hargaElements[0]?.textContent);
        harga_mingguan = extractPrice(hargaElements[1]?.textContent);
        harga_bulanan = extractPrice(hargaElements[2]?.textContent);
    } else if (hargaElements.length === 1) {
        // Hanya satu harga, gunakan untuk semua
        const singlePrice = extractPrice(hargaElements[0]?.textContent);
        harga_harian = singlePrice;
        harga_mingguan = singlePrice * 7 * 0.9; // 10% discount for weekly
        harga_bulanan = singlePrice * 30 * 0.8; // 20% discount for monthly
    }
    
    // Check data attributes
    if (card.dataset.dailyPrice) harga_harian = extractPrice(card.dataset.dailyPrice);
    if (card.dataset.weeklyPrice) harga_mingguan = extractPrice(card.dataset.weeklyPrice);
    if (card.dataset.monthlyPrice) harga_bulanan = extractPrice(card.dataset.monthlyPrice);
    
    return {
        id: productId,
        nama: productName.trim(),
        kategori: productCategory.trim(),
        gambar: productImage,
        harga_harian: Math.max(0, harga_harian),
        harga_mingguan: Math.max(0, harga_mingguan),
        harga_bulanan: Math.max(0, harga_bulanan)
    };
}

/**
 * Fetch product from API
 */
async function fetchProductFromAPI(productId) {
    try {
        const response = await fetch(`/api/products/${productId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`API responded with ${response.status}`);
        }
        
        const data = await response.json();
        return {
            id: data.id || productId,
            nama: data.name || data.nama || 'Produk',
            kategori: data.category || data.kategori || 'Kategori',
            gambar: data.image || data.gambar || '/images/default-product.jpg',
            harga_harian: data.daily_price || data.harga_harian || 0,
            harga_mingguan: data.weekly_price || data.harga_mingguan || 0,
            harga_bulanan: data.monthly_price || data.harga_bulanan || 0
        };
    } catch (error) {
        throw new Error('Gagal mengambil data produk');
    }
}

/**
 * Update modal UI with product data
 */
function updateModalUI(productData) {
    // Update basic info
    safeSetValue('product_id', productData.id);
    safeSetText('modalProductName', productData.nama);
    safeSetText('modalProductCategory', productData.kategori);
    
    const modalProductImage = document.getElementById('modalProductImage');
    if (modalProductImage) {
        modalProductImage.src = productData.gambar;
        modalProductImage.alt = productData.nama;
        modalProductImage.onerror = function() {
            this.src = '/images/default-product.jpg';
        };
    }
    
    // Update prices
    safeSetText('modalDailyPrice', formatRupiah(productData.harga_harian));
    safeSetText('modalWeeklyPrice', formatRupiah(productData.harga_mingguan));
    safeSetText('modalMonthlyPrice', formatRupiah(productData.harga_bulanan));
    
    // Set tanggal mulai (besok)
    const tanggalMulaiInput = document.getElementById('tanggal_mulai');
    if (tanggalMulaiInput) {
        tanggalMulaiInput.min = getTomorrowDate();
        tanggalMulaiInput.value = getTomorrowDate();
    }
}

/**
 * Safe element value setter
 */
function safeSetValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) element.value = value;
}

/**
 * Safe element text setter
 */
function safeSetText(elementId, text) {
    const element = document.getElementById(elementId);
    if (element) element.textContent = text;
}

/**
 * Reset form to defaults
 */
function resetFormDefaults() {
    const rentalForm = document.getElementById('rentalForm');
    if (rentalForm) {
        rentalForm.reset();
    }
    
    // Set default duration to harian
    const durasiHarian = document.querySelector('input[name="durasi"][value="harian"]');
    if (durasiHarian) {
        durasiHarian.checked = true;
    }
    
    // Reset jumlah hari
    const jumlahHariInput = document.getElementById('jumlah_hari');
    if (jumlahHariInput) {
        jumlahHariInput.value = 1;
        jumlahHariInput.min = 1;
        jumlahHariInput.max = 365; // Set reasonable max
    }
    
    // Update UI
    updateDurationVisualSelection();
    updatePrice();
}

/**
 * Show modal
 */
function showModal() {
    const modal = document.getElementById('rentalModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Focus on first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input, textarea, select');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

/**
 * Close modal
 */
function closeRentalModal() {
    const modal = document.getElementById('rentalModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    currentProductData = null;
}

/**
 * Update price calculation
 */
function updatePrice() {
    try {
        const selectedDuration = document.querySelector('input[name="durasi"]:checked');
        const jumlahHariInput = document.getElementById('jumlah_hari');
        
        if (!selectedDuration || !currentProductData || !jumlahHariInput) {
            console.warn('Missing data for price calculation');
            return;
        }
        
        const duration = selectedDuration.value;
        let days = parseInt(jumlahHariInput.value) || 1;
        
        // Validate and adjust days
        days = adjustDaysForDuration(days, duration);
        jumlahHariInput.value = days;
        
        // Get base prices
        const { harga_harian, harga_mingguan, harga_bulanan } = currentProductData;
        
        // Calculate prices
        let pricePerDay = 0;
        let totalPrice = 0;
        let displayText = "";
        
        switch(duration) {
            case 'harian':
                pricePerDay = harga_harian;
                totalPrice = pricePerDay * days;
                displayText = `${days} hari`;
                break;
                
            case 'mingguan':
                const weeks = days / 7;
                pricePerDay = Math.round(harga_mingguan / 7);
                totalPrice = harga_mingguan * weeks;
                displayText = `${weeks} minggu (${days} hari)`;
                break;
                
            case 'bulanan':
                const months = days / 30;
                pricePerDay = Math.round(harga_bulanan / 30);
                totalPrice = harga_bulanan * months;
                displayText = `${months.toFixed(1)} bulan (${days} hari)`;
                break;
        }
        
        // Update UI
        const pricePerDayEl = document.getElementById('pricePerDay');
        const daysCountEl = document.getElementById('daysCount');
        const totalPriceEl = document.getElementById('totalPrice');
        
        if (pricePerDayEl) {
            pricePerDayEl.textContent = `${formatRupiah(pricePerDay)}${duration === 'harian' ? '/hari' : ' (rata-rata/hari)'}`;
        }
        
        if (daysCountEl) {
            daysCountEl.textContent = displayText;
        }
        
        if (totalPriceEl) {
            totalPriceEl.textContent = formatRupiah(totalPrice);
            totalPriceEl.setAttribute('data-total', totalPrice); // Store raw value
        }
        
    } catch (error) {
        console.error('Error updating price:', error);
    }
}

// ================= FORM HANDLING =================
/**
 * Handle form submission
 */
async function handleRentalFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('submitRentalBtn');
    
    if (!submitBtn || !form.checkValidity()) {
        if (!form.checkValidity()) {
            form.reportValidity();
        }
        return;
    }
    
    // Show loading state
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = `
        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Memproses...</span>
    `;
    submitBtn.disabled = true;
    
    try {
        // Prepare form data
        const formData = new FormData(form);
        const data = {
            product_id: formData.get('product_id'),
            type: 'sewa',
            quantity: 1,
            options: {
                durasi: formData.get('durasi'),
                jumlah_hari: formData.get('jumlah_hari'),
                tanggal_mulai: formData.get('tanggal_mulai'),
                catatan: formData.get('catatan') || ''
            }
        };
        
        // Validate data
        if (!validateRentalData(data)) {
            throw new Error('Data penyewaan tidak valid');
        }
        
        // Submit to server
        await submitRentalData(data);
        
        // Success
        closeRentalModal();
        showSuccess('Produk berhasil ditambahkan ke keranjang');
        
    } catch (error) {
        console.error('Submission error:', error);
        showError(error.message || 'Gagal menambahkan ke keranjang');
    } finally {
        // Restore button
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
}

/**
 * Validate rental data before submission
 */
function validateRentalData(data) {
    if (!data.product_id) {
        throw new Error('Produk tidak dipilih');
    }
    
    if (!data.options.durasi) {
        throw new Error('Durasi penyewaan harus dipilih');
    }
    
    if (!data.options.jumlah_hari || data.options.jumlah_hari < 1) {
        throw new Error('Jumlah hari tidak valid');
    }
    
    if (!data.options.tanggal_mulai) {
        throw new Error('Tanggal mulai harus dipilih');
    }
    
    // Check if start date is at least tomorrow
    const startDate = new Date(data.options.tanggal_mulai);
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);
    
    if (startDate < tomorrow) {
        throw new Error('Tanggal mulai harus besok atau setelahnya');
    }
    
    return true;
}

/**
 * Submit rental data to server
 */
async function submitRentalData(data) {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        throw new Error('CSRF token tidak ditemukan');
    }
    
    const response = await fetch('/user/keranjang', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (!response.ok) {
        throw new Error(result.message || `Server error: ${response.status}`);
    }
    
    if (!result.success) {
        throw new Error(result.message || 'Gagal menambahkan ke keranjang');
    }
    
    // Update cart count
    if (typeof window.updateCartCount === 'function') {
        window.updateCartCount(result.cart_count);
    } else if (result.cart_count !== undefined) {
        window.dispatchEvent(new CustomEvent('cartUpdated', {
            detail: { count: result.cart_count }
        }));
    }
    
    return result;
}

/**
 * Get CSRF token
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ||
           document.querySelector('input[name="_token"]')?.value;
}

// ================= UI FEEDBACK =================
/**
 * Show success message
 */
function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    } else if (typeof Toastify !== 'undefined') {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#10B981",
            stopOnFocus: true
        }).showToast();
    } else {
        alert(message);
    }
}

/**
 * Show error message
 */
function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: message,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK'
        });
    } else {
        alert('Error: ' + message);
    }
}

// ================= INITIALIZATION =================
/**
 * Initialize modal functionality
 */
function initModal() {
    if (isModalInitialized) {
        console.log('Modal already initialized');
        return;
    }
    
    try {
        console.log('Initializing modal functionality...');
        
        // Event: Duration change
        document.querySelectorAll('input[name="durasi"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateDurationVisualSelection();
                
                const jumlahHariInput = document.getElementById('jumlah_hari');
                if (!jumlahHariInput) return;
                
                // Reset jumlah hari untuk durasi baru
                const minDays = this.value === 'mingguan' ? 7 : 
                              this.value === 'bulanan' ? 30 : 1;
                
                if (parseInt(jumlahHariInput.value) < minDays) {
                    jumlahHariInput.value = minDays;
                }
                
                updatePrice();
            });
        });
        
        // Event: Jumlah hari input
        const jumlahHariInput = document.getElementById('jumlah_hari');
        if (jumlahHariInput) {
            jumlahHariInput.addEventListener('input', debounce(function() {
                updatePrice();
            }, 300));
            
            jumlahHariInput.addEventListener('blur', function() {
                updatePrice();
            });
        }
        
        // Event: Tanggal mulai change
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        if (tanggalMulaiInput) {
            tanggalMulaiInput.addEventListener('change', function() {
                // Basic validation
                const selectedDate = new Date(this.value);
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                tomorrow.setHours(0, 0, 0, 0);
                
                if (selectedDate < tomorrow) {
                    this.value = getTomorrowDate();
                    showError('Tanggal mulai harus besok atau setelahnya');
                }
            });
        }
        
        // Event: Form submission
        const rentalForm = document.getElementById('rentalForm');
        if (rentalForm) {
            rentalForm.addEventListener('submit', handleRentalFormSubmit);
        }
        
        // Event: Close modal on background click
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRentalModal();
                }
            });
        }
        
        // Event: Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeRentalModal();
            }
        });
        
        // Initialize visual selection
        updateDurationVisualSelection();
        
        isModalInitialized = true;
        console.log('Modal functions initialized successfully');
        
    } catch (error) {
        console.error('Failed to initialize modal:', error);
    }
}

/**
 * Debounce function for performance
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ================= GLOBAL EXPORTS =================
window.showSewaModal = showSewaModal;
window.closeRentalModal = closeRentalModal;
window.initModal = initModal;
window.updatePrice = updatePrice;

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('rentalModal')) {
            initModal();
        }
    });
} else {
    if (document.getElementById('rentalModal')) {
        initModal();
    }
}

// Global error handler for modal
window.addEventListener('error', function(e) {
    if (e.message.includes('modal') || e.filename?.includes('sewa-modal')) {
        console.error('Modal error caught:', e);
        showError('Terjadi kesalahan pada sistem penyewaan');
    }
});