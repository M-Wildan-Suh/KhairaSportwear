@extends('user.layouts.app')

@section('title', 'Sewa Alat Olahraga - SportWear')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="hero-sewa mb-5" data-aos="fade-down">
        <div class="bg-gradient-info rounded-4 p-5 text-white">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-6 mb-3">Sewa Alat Olahraga</h1>
                    <p class="mb-0">Nikmati alat olahraga berkualitas tanpa harus membeli. Fleksibel, praktis, dan hemat!</p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="floating">
                        <i class="fas fa-calendar-alt fa-5x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Benefits -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-md-3">
            <div class="text-center p-3">
                <div class="benefit-icon mb-3">
                    <i class="fas fa-wallet fa-3x text-success"></i>
                </div>
                <h5>Hemat Biaya</h5>
                <p class="text-muted small">Hanya bayar sesuai durasi sewa</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3">
                <div class="benefit-icon mb-3">
                    <i class="fas fa-bolt fa-3x text-warning"></i>
                </div>
                <h5>Proses Cepat</h5>
                <p class="text-muted small">Booking online, ambil di tempat</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3">
                <div class="benefit-icon mb-3">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h5>Terjamin</h5>
                <p class="text-muted small">Alat berkualitas dengan garansi</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3">
                <div class="benefit-icon mb-3">
                    <i class="fas fa-sync-alt fa-3x text-info"></i>
                </div>
                <h5>Fleksibel</h5>
                <p class="text-muted small">Pilihan durasi harian/mingguan/bulanan</p>
            </div>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="row mb-4" data-aos="fade-up">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari alat olahraga untuk disewa...">
                <button class="btn btn-primary" onclick="searchProducts()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="categoryFilter">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="row g-4 mb-5" id="productsGrid">
        @forelse($produks as $produk)
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
            <div class="rental-product-card">
                <div class="product-img-container">
                    <img src="{{ $produk->gambar_url }}" 
                         alt="{{ $produk->nama }}"
                         class="product-img">
                    <div class="product-badge bg-info">Sewa</div>
                </div>
                
                <div class="p-4">
                    <h6 class="font-weight-bold mb-2">{{ $produk->nama }}</h6>
                    <p class="text-muted small mb-3">{{ Str::limit($produk->deskripsi, 60) }}</p>
                    
                    <!-- Rental Prices -->
                    <div class="rental-prices mb-3">
                        <div class="d-flex justify-content-between">
                            <div class="text-center">
                                <div class="text-success font-weight-bold">
                                    Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}
                                </div>
                                <small class="text-muted">Hari</small>
                            </div>
                            <div class="text-center">
                                <div class="text-success font-weight-bold">
                                    Rp {{ number_format($produk->harga_sewa_mingguan, 0, ',', '.') }}
                                </div>
                                <small class="text-muted">Minggu</small>
                            </div>
                            <div class="text-center">
                                <div class="text-success font-weight-bold">
                                    Rp {{ number_format($produk->harga_sewa_bulanan, 0, ',', '.') }}
                                </div>
                                <small class="text-muted">Bulan</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock Info -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-box"></i> {{ $produk->stok_tersedia }} tersedia
                        </span>
                        <span class="badge bg-primary">{{ $produk->kategori->nama }}</span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.produk.show', $produk->slug) }}" 
                           class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-info-circle"></i> Detail
                        </a>
                        @if($produk->stok_tersedia > 0)
                        <button class="btn btn-info btn-sm add-to-cart" 
                                data-product-id="{{ $produk->id }}" 
                                data-type="sewa">
                            <i class="fas fa-cart-plus"></i> Sewa
                        </button>
                        @else
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="fas fa-times"></i> Habis
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">Tidak ada alat tersedia untuk disewa</h4>
            <p class="text-muted">Coba gunakan kata kunci pencarian yang berbeda</p>
        </div>
        @endforelse
    </div>
    
    <!-- How It Works -->
    <div class="row mt-5" data-aos="fade-up">
        <div class="col-12">
            <h3 class="text-center mb-4">Cara Menyewa</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card text-center p-4">
                        <div class="step-number mb-3">1</div>
                        <h5>Pilih Alat</h5>
                        <p class="text-muted">Cari dan pilih alat olahraga yang ingin disewa</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card text-center p-4">
                        <div class="step-number mb-3">2</div>
                        <h5>Tentukan Durasi</h5>
                        <p class="text-muted">Pilih durasi sewa dan tanggal mulai</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card text-center p-4">
                        <div class="step-number mb-3">3</div>
                        <h5>Bayar & Ambil</h5>
                        <p class="text-muted">Lakukan pembayaran dan ambil alat di toko</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ -->
    <div class="row mt-5" data-aos="fade-up">
        <div class="col-12">
            <h3 class="text-center mb-4">FAQ Sewa Alat</h3>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Berapa lama maksimal sewa?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Maksimal sewa adalah 30 hari. Untuk durasi lebih lama, silakan hubungi admin.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Bagaimana jika alat rusak?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Untuk kerusakan ringan dikenakan denda 10% harga alat, kerusakan berat 50%, dan kehilangan 100% harga alat.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rental Modal -->
<div class="modal fade" id="rentalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penyewaan Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rentalForm">
                    @csrf
                    <input type="hidden" id="product_id" name="product_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Durasi Sewa</label>
                        <select class="form-select" name="durasi" id="durasi" required>
                            <option value="">Pilih durasi</option>
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah Hari</label>
                        <input type="number" class="form-control" name="jumlah_hari" id="jumlah_hari" value="1" min="1" max="30" required>
                        <div class="form-text">Maksimal 30 hari</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="2"></textarea>
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="price-summary border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga per hari:</span>
                            <span id="pricePerDay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah hari:</span>
                            <span id="daysCount">0 hari</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="totalPrice" class="text-success">Rp 0</strong>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hero-sewa {
    background: linear-gradient(135deg, #38B2AC, #2B6CB0);
}

.benefit-icon {
    transition: all 0.3s ease;
}

.benefit-icon:hover {
    transform: scale(1.1) rotate(5deg);
}

.rental-product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.rental-product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.product-img-container {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.rental-product-card:hover .product-img {
    transform: scale(1.1);
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 1;
}

.rental-prices {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
}

.step-card {
    border: 2px solid transparent;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.step-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
}

.step-number {
    width: 40px;
    height: 40px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.price-summary {
    background: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
// Search functionality
function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value;
    const categoryId = document.getElementById('categoryFilter').value;
    
    // Implement search logic here
    console.log('Search:', searchTerm, 'Category:', categoryId);
}

// Initialize rental modal
let currentProduct = null;

document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        currentProduct = productId;
        
        // Load product info and show modal
        loadProductInfo(productId);
    });
});

function loadProductInfo(productId) {
    fetch(`/api/products/${productId}/rental-info`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('product_id').value = productId;
            
            // Set date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('tanggal_mulai').min = tomorrow.toISOString().split('T')[0];
            document.getElementById('tanggal_mulai').value = tomorrow.toISOString().split('T')[0];
            
            // Initialize price calculation
            updatePrice();
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('rentalModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat informasi produk', 'error');
        });
}

// Update price based on selections
function updatePrice() {
    const durasi = document.getElementById('durasi').value;
    const jumlahHari = parseInt(document.getElementById('jumlah_hari').value);
    
    if (!durasi || !jumlahHari) return;
    
    // This would normally come from API
    const prices = {
        harian: 50000,
        mingguan: 300000,
        bulanan: 1000000
    };
    
    const pricePerDay = prices[durasi] || 0;
    let totalPrice = 0;
    
    if (durasi === 'harian') {
        totalPrice = pricePerDay * jumlahHari;
    } else if (durasi === 'mingguan') {
        totalPrice = pricePerDay * Math.ceil(jumlahHari / 7);
    } else if (durasi === 'bulanan') {
        totalPrice = pricePerDay * Math.ceil(jumlahHari / 30);
    }
    
    // Update display
    document.getElementById('pricePerDay').textContent = `Rp ${pricePerDay.toLocaleString()}`;
    document.getElementById('daysCount').textContent = `${jumlahHari} hari`;
    document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString()}`;
}

// Event listeners for price updates
document.getElementById('durasi').addEventListener('change', updatePrice);
document.getElementById('jumlah_hari').addEventListener('input', updatePrice);

// Rental form submission
document.getElementById('rentalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const productId = formData.get('product_id');
    
    Swal.fire({
        title: 'Tambah ke Keranjang?',
        text: 'Alat akan ditambahkan ke keranjang untuk penyewaan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ED8936',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Ya, Tambahkan',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('/user/keranjang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    type: 'sewa',
                    quantity: 1,
                    options: {
                        durasi: formData.get('durasi'),
                        jumlah_hari: formData.get('jumlah_hari'),
                        tanggal_mulai: formData.get('tanggal_mulai'),
                        catatan: formData.get('catatan')
                    }
                })
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
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('rentalModal')).hide();
                
                // Update cart badge
                window.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: result.value.cart_count }
                }));
                
                // Show success message
                Toast.fire({
                    icon: 'success',
                    title: 'Alat berhasil ditambahkan ke keranjang!'
                });
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
});

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        once: true
    });
});
</script>
@endpush