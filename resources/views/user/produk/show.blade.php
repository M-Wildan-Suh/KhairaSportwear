@extends('user.layouts.app')

@section('title', $produk->nama . ' - SportWear')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.produk.index') }}">Produk</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.produk.kategori', $produk->kategori->slug) }}">{{ $produk->kategori->nama }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama }}</li>
        </ol>
    </nav>
    
    <!-- Product Detail -->
    <div class="row g-4 mb-5">
        <!-- Product Images -->
        <div class="col-lg-6" data-aos="fade-right">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img src="{{ $produk->gambar_url }}" 
                         alt="{{ $produk->nama }}"
                         class="img-fluid rounded-4 shadow"
                         id="mainProductImage"
                         style="height: 400px; object-fit: cover; width: 100%;">
                </div>
                
                <!-- Image Gallery -->
                <div class="image-thumbnails d-flex gap-2">
                    <div class="thumbnail-item active" style="width: 80px; height: 80px;">
                        <img src="{{ $produk->gambar_url }}" 
                             alt="{{ $produk->nama }}"
                             class="img-fluid rounded-2 cursor-pointer"
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onclick="changeMainImage(this.src)">
                    </div>
                    <!-- Additional thumbnails would go here -->
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-6" data-aos="fade-left">
            <div class="product-info">
                <!-- Category & Status -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-primary">{{ $produk->kategori->nama }}</span>
                        @if($produk->tipe === 'jual')
                        <span class="badge bg-success">Dijual</span>
                        @elseif($produk->tipe === 'sewa')
                        <span class="badge bg-info">Disewa</span>
                        @else
                        <span class="badge bg-warning">Dijual/Disewa</span>
                        @endif
                    </div>
                    <div class="stock-status">
                        @if($produk->stok_tersedia > 0)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i> Stok Tersedia ({{ $produk->stok_tersedia }})
                        </span>
                        @else
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i> Stok Habis
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Product Name -->
                <h1 class="h2 font-weight-bold mb-3">{{ $produk->nama }}</h1>
                
                <!-- Ratings -->
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-muted">(4.5) • 128 reviews</span>
                </div>
                
                <!-- Prices -->
                <div class="pricing mb-4">
                    @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                    <div class="mb-3">
                        <h3 class="text-primary font-weight-bold mb-1">
                            Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                        </h3>
                        <small class="text-muted">Harga beli</small>
                    </div>
                    @endif
                    
                    @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                    <div class="rental-prices">
                        <h5 class="font-weight-bold mb-2">Harga Sewa:</h5>
                        <div class="row g-2">
                            @if($produk->harga_sewa_harian)
                            <div class="col-md-4">
                                <div class="price-option border rounded-3 p-3 text-center">
                                    <div class="text-success font-weight-bold h5 mb-1">
                                        Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Per Hari</small>
                                </div>
                            </div>
                            @endif
                            
                            @if($produk->harga_sewa_mingguan)
                            <div class="col-md-4">
                                <div class="price-option border rounded-3 p-3 text-center">
                                    <div class="text-success font-weight-bold h5 mb-1">
                                        Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Per Minggu</small>
                                    <div class="text-success small">Hemat {{ number_format(($produk->harga_sewa_harian * 7 - $produk->harga_sewa_mingguan) / ($produk->harga_sewa_harian * 7) * 100, 0) }}%</div>
                                </div>
                            </div>
                            @endif
                            
                            @if($produk->harga_sewa_bulanan)
                            <div class="col-md-4">
                                <div class="price-option border rounded-3 p-3 text-center">
                                    <div class="text-success font-weight-bold h5 mb-1">
                                        Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Per Bulan</small>
                                    <div class="text-success small">Hemat {{ number_format(($produk->harga_sewa_harian * 30 - $produk->harga_sewa_bulanan) / ($produk->harga_sewa_harian * 30) * 100, 0) }}%</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Product Description -->
                <div class="product-description mb-4">
                    <h5 class="font-weight-bold mb-2">Deskripsi Produk</h5>
                    <p class="text-muted">{{ $produk->deskripsi }}</p>
                </div>
                
                <!-- Specifications -->
                @if($produk->spesifikasi)
                <div class="specifications mb-4">
                    <h5 class="font-weight-bold mb-2">Spesifikasi</h5>
                    <div class="row">
                        @foreach($produk->spesifikasi as $key => $value)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                <span class="font-weight-bold">{{ $value }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    @if($produk->stok_tersedia > 0)
                    <div class="row g-3">
                        <!-- Quantity Selector -->
                        <div class="col-md-3">
                            <div class="quantity-selector">
                                <label class="form-label">Jumlah</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">-</button>
                                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="{{ $produk->stok_tersedia }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add to Cart -->
                        <div class="col-md-9">
                            <div class="d-grid gap-2">
                                @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                                <button class="btn btn-primary btn-lg add-to-cart-action" 
                                        data-type="jual">
                                    <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang (Beli)
                                </button>
                                @endif
                                
                                @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                <button class="btn btn-info btn-lg add-to-cart-action" 
                                        data-type="sewa">
                                    <i class="fas fa-calendar-alt me-2"></i> Tambah ke Keranjang (Sewa)
                                </button>
                                @endif
                                
                                <!-- Buy/Sewa Now -->
                                @if($produk->tipe === 'jual' || $produk->tipe === 'both')
                                <button class="btn btn-sport btn-lg buy-now-action" 
                                        data-type="jual">
                                    <i class="fas fa-bolt me-2"></i> Beli Sekarang
                                </button>
                                @endif
                                
                                @if($produk->tipe === 'sewa' || $produk->tipe === 'both')
                                <button class="btn btn-warning btn-lg buy-now-action" 
                                        data-type="sewa">
                                    <i class="fas fa-bolt me-2"></i> Sewa Sekarang
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Produk ini sedang tidak tersedia. Silakan hubungi admin untuk informasi lebih lanjut.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="product-tabs card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-body">
                    <ul class="nav nav-tabs border-0" id="productTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">
                                <i class="fas fa-file-alt me-2"></i> Deskripsi Lengkap
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button">
                                <i class="fas fa-list-alt me-2"></i> Spesifikasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                                <i class="fas fa-star me-2"></i> Ulasan (128)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button">
                                <i class="fas fa-question-circle me-2"></i> FAQ
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3" id="productTabContent">
                        <div class="tab-pane fade show active" id="description">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="mb-3">Tentang {{ $produk->nama }}</h4>
                                    <p>{{ $produk->deskripsi }}</p>
                                    
                                    <h5 class="mt-4">Fitur Utama:</h5>
                                    <ul>
                                        <li>Material berkualitas tinggi untuk daya tahan maksimal</li>
                                        <li>Desain ergonomis untuk kenyamanan penggunaan</li>
                                        <li>Cocok untuk pemula hingga profesional</li>
                                        <li>Garansi resmi 1 tahun</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light rounded-3 p-4">
                                        <h5 class="mb-3">Info Penting</h5>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-shipping-fast text-primary me-2"></i>
                                                <strong>Gratis Ongkir</strong> untuk order di atas Rp 500.000
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-undo text-primary me-2"></i>
                                                <strong>Garansi 30 Hari</strong> pengembalian
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-shield-alt text-primary me-2"></i>
                                                <strong>100% Original</strong> produk resmi
                                            </li>
                                            <li>
                                                <i class="fas fa-headset text-primary me-2"></i>
                                                <strong>Support 24/7</strong> via chat & telepon
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="specs">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach($produk->spesifikasi ?? [] as $key => $value)
                                        <tr>
                                            <th width="30%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td>{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <th>Kategori</th>
                                            <td>{{ $produk->kategori->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Stok Tersedia</th>
                                            <td>{{ $produk->stok_tersedia }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipe</th>
                                            <td>{{ $produk->tipe === 'jual' ? 'Dijual' : ($produk->tipe === 'sewa' ? 'Disewa' : 'Dijual/Disewa') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="reviews">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center mb-4">
                                        <div class="display-4 text-warning mb-2">4.5</div>
                                        <div class="text-warning mb-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <p class="text-muted">Berdasarkan 128 ulasan</p>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <!-- Sample reviews -->
                                    <div class="review-item mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong>Andi Pratama</strong>
                                                <div class="text-warning">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted">2 minggu yang lalu</small>
                                        </div>
                                        <p class="mb-0">Produk sangat bagus, kualitas sesuai harga. Pengiriman cepat!</p>
                                    </div>
                                    
                                    <div class="review-item mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong>Sari Dewi</strong>
                                                <div class="text-warning">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted">1 bulan yang lalu</small>
                                        </div>
                                        <p class="mb-0">Cocok untuk pemula, mudah digunakan dan hasil maksimal.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="faq">
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            Berapa lama proses pengiriman?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Pengiriman dalam kota 1-2 hari kerja, luar kota 3-5 hari kerja.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            Apakah produk bergaransi?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Ya, semua produk bergaransi resmi 1 tahun.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4" data-aos="fade-up">Produk Terkait</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    @include('components.product-card', ['product' => $related])
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal for Sewa Options -->
<div class="modal fade" id="sewaOptionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilihan Penyewaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Sewa options will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.product-gallery .main-image {
    border: 2px solid #f8f9fa;
    border-radius: 16px;
    overflow: hidden;
}

.image-thumbnails .thumbnail-item {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-thumbnails .thumbnail-item:hover,
.image-thumbnails .thumbnail-item.active {
    border-color: var(--primary);
    transform: scale(1.05);
}

.price-option {
    transition: all 0.3s ease;
    cursor: pointer;
}

.price-option:hover,
.price-option.active {
    border-color: var(--primary) !important;
    background: rgba(43, 108, 176, 0.05);
}

.quantity-selector .input-group button {
    width: 40px;
}

.quantity-selector input {
    border-left: none;
    border-right: none;
}

.product-tabs .nav-tabs {
    border-bottom: 2px solid #f8f9fa;
}

.product-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    border-radius: 0;
}

.product-tabs .nav-link.active {
    color: var(--primary);
    border-bottom: 3px solid var(--primary);
    background: transparent;
}

.review-item {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
}

.accordion-button:not(.collapsed) {
    background: rgba(43, 108, 176, 0.1);
    color: var(--primary);
    font-weight: 600;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(43, 108, 176, 0.25);
}

.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item a {
    color: var(--primary);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
// Quantity control
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const min = parseInt(input.min);
    const current = parseInt(input.value);
    if (current > min) {
        input.value = current - 1;
    }
}

// Change main image
function changeMainImage(src) {
    document.getElementById('mainProductImage').src = src;
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    event.target.closest('.thumbnail-item').classList.add('active');
}

// Add to cart action
document.querySelectorAll('.add-to-cart-action').forEach(button => {
    button.addEventListener('click', function() {
        const type = this.dataset.type;
        const quantity = parseInt(document.getElementById('quantity').value);
        
        if (type === 'sewa') {
            // Show sewa options modal
            showSewaOptions(quantity);
        } else {
            addToCart(type, quantity);
        }
    });
});

// Buy now action
document.querySelectorAll('.buy-now-action').forEach(button => {
    button.addEventListener('click', function() {
        const type = this.dataset.type;
        const quantity = parseInt(document.getElementById('quantity').value);
        
        if (type === 'sewa') {
            // Show sewa options modal and proceed to checkout
            showSewaOptions(quantity, true);
        } else {
            addToCart(type, quantity, true);
        }
    });
});

// Show sewa options
function showSewaOptions(quantity, checkout = false) {
    const modal = document.getElementById('sewaOptionsModal');
    const modalBody = modal.querySelector('.modal-body');
    
    modalBody.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner"></div>
        </div>
    `;
    
    // Load sewa options
    fetch(`/user/produk/{{ $produk->id }}/sewa-options`)
        .then(response => response.json())
        .then(data => {
            modalBody.innerHTML = `
                <form id="sewaForm">
                    <div class="mb-3">
                        <label class="form-label">Durasi Sewa</label>
                        <select class="form-select" name="durasi" required>
                            <option value="">Pilih durasi</option>
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah Hari</label>
                        <input type="number" class="form-control" name="jumlah_hari" value="1" min="1" max="30" required>
                        <div class="form-text">Maksimal 30 hari</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="2"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            ${checkout ? 'Lanjut ke Checkout' : 'Tambahkan ke Keranjang'}
                        </button>
                    </div>
                </form>
            `;
            
            // Set min date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];
            modalBody.querySelector('input[name="tanggal_mulai"]').min = minDate;
            
            // Form submission
            modalBody.querySelector('#sewaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const options = {
                    durasi: formData.get('durasi'),
                    jumlah_hari: formData.get('jumlah_hari'),
                    tanggal_mulai: formData.get('tanggal_mulai'),
                    catatan: formData.get('catatan')
                };
                
                addToCart('sewa', quantity, checkout, options);
                
                // Close modal
                bootstrap.Modal.getInstance(modal).hide();
            });
        })
        .catch(error => {
            modalBody.innerHTML = '<p class="text-danger">Gagal memuat opsi penyewaan.</p>';
        });
    
    // Show modal
    new bootstrap.Modal(modal).show();
}

// Add to cart function
function addToCart(type, quantity, checkout = false, options = null) {
    const productId = {{ $produk->id }};
    const productName = "{{ $produk->nama }}";
    
    const data = {
        product_id: productId,
        type: type,
        quantity: quantity
    };
    
    if (options) {
        data.options = options;
    }
    
    Swal.fire({
        title: checkout ? 'Proses Checkout?' : 'Tambah ke Keranjang?',
        text: checkout 
            ? `Anda akan melanjutkan ke checkout untuk ${type === 'jual' ? 'pembelian' : 'penyewaan'} ${productName}`
            : `Anda akan menambahkan ${productName} ke keranjang untuk ${type === 'jual' ? 'pembelian' : 'penyewaan'}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ED8936',
        cancelButtonColor: '#718096',
        confirmButtonText: checkout ? 'Ya, Checkout' : 'Ya, Tambahkan',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('/user/keranjang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Request failed: ${error}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                // Update cart badge
                window.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: result.value.cart_count }
                }));
                
                if (checkout) {
                    // Redirect to checkout
                    window.location.href = '/user/transaksi/create';
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Produk telah ditambahkan ke keranjang',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
}

// Initialize price option selection
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.price-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.price-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endpush