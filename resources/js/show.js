// public/js/product-show.js
class ProductShow {
    static productData = window.productData || {};
    static selectedSize = null;
    static selectedColor = null;
    
    static init() {
        this.bindEvents();
        this.initQuantityControls();
        this.initTabSystem();
        this.initFAQ();
        this.initRentalModal();
    }
    
    static bindEvents() {
        // Size selection
        document.querySelectorAll('.size-option').forEach(option => {
            option.addEventListener('click', (e) => {
                this.selectSize(e.target.dataset.size);
            });
        });
        
        // Price option selection
        document.querySelectorAll('.price-option').forEach(option => {
            option.addEventListener('click', (e) => {
                this.selectPriceOption(e.currentTarget);
            });
        });
        
        // Close modal on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeRentalModal();
            }
        });
    }
    
    static initQuantityControls() {
        const quantityInput = document.getElementById('productQuantity');
        if (!quantityInput) return;
        
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action;
                let value = parseInt(quantityInput.value);
                const max = parseInt(quantityInput.max);
                const min = parseInt(quantityInput.min);
                
                if (action === 'increase' && value < max) {
                    value++;
                } else if (action === 'decrease' && value > min) {
                    value--;
                }
                
                quantityInput.value = value;
            });
        });
        
        quantityInput.addEventListener('change', () => {
            let value = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.max);
            const min = parseInt(quantityInput.min);
            
            if (value > max) quantityInput.value = max;
            if (value < min) quantityInput.value = min;
        });
    }
    
    static selectSize(size) {
        this.selectedSize = size;
        document.querySelectorAll('.size-option').forEach(option => {
            if (option.dataset.size === size) {
                option.classList.add('border-primary', 'bg-primary/10');
                option.classList.remove('border-gray-300');
            } else {
                option.classList.remove('border-primary', 'bg-primary/10');
                option.classList.add('border-gray-300');
            }
        });
    }
    
    static selectPriceOption(element) {
        document.querySelectorAll('.price-option').forEach(option => {
            option.classList.remove('active');
        });
        element.classList.add('active');
    }
    
    static changeMainImage(src) {
        const mainImage = document.getElementById('mainProductImage');
        if (mainImage) {
            mainImage.src = src;
        }
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');
    }
    
    static async addToCart(type, checkout = false) {
        const quantity = document.getElementById('productQuantity')?.value || 1;
        
        if (quantity < 1 || quantity > this.productData.stok_tersedia) {
            this.showAlert('Error', 'Jumlah tidak valid', 'error');
            return;
        }
        
        const data = {
            product_id: this.productData.id,
            type: type,
            quantity: parseInt(quantity),
            options: {
                size: this.selectedSize,
                warna: this.selectedColor
            }
        };
        
        try {
            const response = await fetch('/user/keranjang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.productData.csrf_token
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update cart badge
                window.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: result.cart_count }
                }));
                
                if (checkout) {
                    window.location.href = '/user/transaksi/create';
                } else {
                    this.showAlert('Berhasil!', 'Produk telah ditambahkan ke keranjang', 'success', false, 2000);
                }
            } else {
                this.showAlert('Error', result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
        }
    }
    
    static initTabSystem() {
        this.openTab('description');
    }
    
    static openTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
            tab.classList.add('hidden');
            tab.setAttribute('aria-hidden', 'true');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
            button.setAttribute('aria-selected', 'false');
        });
        
        // Show selected tab content
        const tabContent = document.getElementById(`${tabName}-content`);
        if (tabContent) {
            tabContent.classList.remove('hidden');
            tabContent.classList.add('active');
            tabContent.setAttribute('aria-hidden', 'false');
        }
        
        // Activate selected tab button
        const tabButton = document.getElementById(`${tabName}-tab`);
        if (tabButton) {
            tabButton.classList.add('active');
            tabButton.setAttribute('aria-selected', 'true');
        }
    }
    
    static initFAQ() {
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', (e) => {
                this.toggleFAQ(e.currentTarget);
            });
        });
    }
    
    static toggleFAQ(button) {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('i');
        
        button.classList.toggle('active');
        answer.classList.toggle('hidden');
        
        if (icon) {
            icon.style.transform = button.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    }
    
    static initRentalModal() {
        // Initialize rental modal events
        this.initRentalForm();
    }
    
    static showRentalModal() {
        // Validation checks
        if (this.productData.stok_tersedia <= 0) {
            this.showAlert('Stok Habis', 'Maaf, produk ini sedang tidak tersedia untuk disewa.', 'warning');
            return;
        }
        
        if (!['sewa', 'both'].includes(this.productData.tipe)) {
            this.showAlert('Tidak Tersedia', 'Produk ini hanya tersedia untuk dijual.', 'info');
            return;
        }
        
        const hasRentalPrices = this.productData.harga_sewa_harian > 0 || 
                              this.productData.harga_sewa_mingguan > 0 || 
                              this.productData.harga_sewa_bulanan > 0;
        
        if (!hasRentalPrices) {
            this.showAlert('Tidak Tersedia', 'Produk ini tidak tersedia untuk disewa.', 'warning');
            return;
        }
        
        // Set minimum date (tomorrow)
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        if (tanggalMulaiInput) {
            tanggalMulaiInput.min = tomorrowStr;
            tanggalMulaiInput.value = tomorrowStr;
        }
        
        // Reset form
        const rentalForm = document.getElementById('rentalForm');
        if (rentalForm) {
            rentalForm.reset();
        }
        
        // Set default duration
        const jumlahHariInput = document.getElementById('jumlah_hari');
        if (jumlahHariInput) {
            jumlahHariInput.value = 1;
            jumlahHariInput.min = 1;
        }
        
        // Select first available duration
        const firstDuration = document.querySelector('input[name="durasi"]:enabled');
        if (firstDuration) {
            firstDuration.checked = true;
            this.updateDurationVisualSelection();
        }
        
        // Update price
        this.updateRentalPrice();
        
        // Show modal
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            modal.setAttribute('aria-hidden', 'false');
            
            // Focus on first input
            setTimeout(() => {
                const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
                if (firstInput) firstInput.focus();
            }, 100);
        }
    }
    
    static closeRentalModal() {
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            modal.setAttribute('aria-hidden', 'true');
        }
    }
    
    static updateDurationVisualSelection() {
        document.querySelectorAll('.duration-option').forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            const container = option.querySelector('div');
            
            if (container && radio) {
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
    
    static updateRentalPrice() {
        const selectedDuration = document.querySelector('input[name="durasi"]:checked');
        const jumlahHariInput = document.getElementById('jumlah_hari');
        
        if (!selectedDuration || !jumlahHariInput) return;
        
        const duration = selectedDuration.value;
        let days = parseInt(jumlahHariInput.value) || 1;
        
        // Adjust days based on duration
        switch(duration) {
            case 'mingguan':
                if (days < 7) days = 7;
                days = Math.ceil(days / 7) * 7;
                break;
            case 'bulanan':
                if (days < 30) days = 30;
                days = Math.ceil(days / 30) * 30;
                break;
            default: // harian
                if (days < 1) days = 1;
                if (days > 365) days = 365;
        }
        
        jumlahHariInput.value = days;
        
        // Calculate price
        let pricePerDay = 0;
        let totalPrice = 0;
        let displayText = "";
        
        switch(duration) {
            case 'harian':
                pricePerDay = this.productData.harga_sewa_harian;
                totalPrice = pricePerDay * days;
                displayText = `${days} hari`;
                break;
                
            case 'mingguan':
                const weeks = days / 7;
                pricePerDay = Math.round(this.productData.harga_sewa_mingguan / 7);
                totalPrice = this.productData.harga_sewa_mingguan * weeks;
                displayText = `${weeks} minggu (${days} hari)`;
                break;
                
            case 'bulanan':
                const months = days / 30;
                pricePerDay = Math.round(this.productData.harga_sewa_bulanan / 30);
                totalPrice = this.productData.harga_sewa_bulanan * months;
                displayText = `${months.toFixed(1)} bulan (${days} hari)`;
                break;
        }
        
        // Update UI elements
        this.updateElementText('pricePerDay', this.formatRupiah(pricePerDay) + (duration === 'harian' ? "/hari" : "~ /hari"));
        this.updateElementText('daysCount', displayText);
        this.updateElementText('totalPrice', this.formatRupiah(totalPrice));
    }
    
    static initRentalForm() {
        // Duration change
        document.querySelectorAll('input[name="durasi"]').forEach(radio => {
            radio.addEventListener('change', () => {
                this.updateDurationVisualSelection();
                this.updateRentalPrice();
            });
        });
        
        // Days input
        const jumlahHariInput = document.getElementById('jumlah_hari');
        if (jumlahHariInput) {
            jumlahHariInput.addEventListener('input', () => this.updateRentalPrice());
            jumlahHariInput.addEventListener('blur', () => this.updateRentalPrice());
        }
        
        // Form submission
        const rentalForm = document.getElementById('rentalForm');
        if (rentalForm) {
            rentalForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitRentalForm();
            });
        }
        
        // Close modal on background click
        const modal = document.getElementById('rentalModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeRentalModal();
                }
            });
        }
    }
    
    static async submitRentalForm() {
        const formData = new FormData(document.getElementById('rentalForm'));
        const submitBtn = document.getElementById('submitRentalBtn');
        
        if (!submitBtn) return;
        
        // Validation
        const tanggalMulai = formData.get('tanggal_mulai');
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);
        
        if (new Date(tanggalMulai) < tomorrow) {
            this.showAlert('Tanggal Tidak Valid', 'Tanggal mulai harus besok atau setelahnya', 'warning');
            return;
        }
        
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
                    'X-CSRF-TOKEN': this.productData.csrf_token,
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
                        catatan: formData.get('catatan') || '',
                        size: this.selectedSize,
                        warna: this.selectedColor
                    }
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.closeRentalModal();
                
                // Update cart
                window.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: data.cart_count }
                }));
                
                this.showAlert('Berhasil!', data.message || 'Produk telah ditambahkan ke keranjang', 'success', true);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Submit error:', error);
            this.showAlert('Gagal', error.message || 'Terjadi kesalahan saat menambahkan ke keranjang', 'error');
        } finally {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }
    }
    
    // Utility methods
    static formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    static updateElementText(id, text) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = text;
        }
    }
    
    static showAlert(title, text, icon, toast = true, timer = null) {
        const options = {
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#2B6CB0'
        };
        
        if (toast) {
            options.toast = true;
            options.position = 'top-end';
            options.showConfirmButton = false;
            if (timer) options.timer = timer;
        }
        
        Swal.fire(options);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    ProductShow.init();
});

// Make available globally
window.ProductShow = ProductShow;