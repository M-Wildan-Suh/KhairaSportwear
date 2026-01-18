{{-- ===================== MODAL LOGIN ===================== --}}
@push('modals')
<div id="loginModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeLoginModal()"></div>
        
        <!-- Modal panel -->
        <div class="inline-block w-full max-w-md overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg">
            <!-- Header -->
            <div class="px-6 pt-6 pb-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary/10">
                        <i class="fas fa-user-lock text-primary"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Login Diperlukan</h3>
                </div>
                <button type="button" onclick="closeLoginModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <div id="loginModalContent" class="p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user-lock text-primary text-2xl"></i>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mb-2">Akses Terbatas</h4>
                <p class="text-gray-600 mb-6">Anda perlu login untuk mengakses fitur ini</p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
                    <button type="button" onclick="closeLoginModal()"
                        class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Nanti Saja
                    </button>
                    <button type="button" onclick="redirectToLogin()"
                        class="w-full px-4 py-3 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-dark">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login Sekarang
                    </button>
                </div>
                <p class="mt-4 text-center text-sm text-gray-500">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-medium text-primary hover:text-primary-dark">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endpush

{{-- ===================== TOMBOL PRODUK ===================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    @if($produk->tipe === 'jual' || $produk->tipe === 'both')
        <button onclick="handleAddToCart('jual', false)" 
                class="flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
            <i class="fas fa-shopping-cart"></i>
            <span>Tambahkan ke Keranjang</span>
        </button>
        <button onclick="handleAddToCart('jual', true)" 
                class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary/5 transition-colors">
            <i class="fas fa-bolt"></i>
            <span>Beli Sekarang</span>
        </button>
    @endif
</div>

{{-- ===================== SCRIPT HALAMAN ===================== --}}
@push('scripts')
<script>
// Cek login global
const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

// ---------------- LOGIN MODAL ----------------
function showLoginModal(config = {}) {
    const modal = document.getElementById('loginModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function redirectToLogin() {
    const currentUrl = encodeURIComponent(window.location.href);
    window.location.href = `{{ route('login') }}?return=${currentUrl}`;
}

// ---------------- REQUIRE LOGIN WRAPPER ----------------
function requireLogin(callback, actionType = 'cart', options = {}) {
    if (!isLoggedIn) {
        showLoginModal();
        return false;
    }
    if (typeof callback === 'function') callback();
    return true;
}

// ---------------- WRAPPER UNTUK TOMBOL ----------------
document.addEventListener('DOMContentLoaded', function() {
    window.handleAddToCart = function(type, checkout = false) {
        requireLogin(() => addToCart(type, document.getElementById('quantity').value, checkout));
    };

    window.handleSewa = function(checkout = false) {
        requireLogin(() => showSewaOptions(checkout));
    };
});

// ---------------- ADD TO CART ----------------
async function addToCart(type, quantity, checkout = false) {
    const data = {
        product_id: {{ $produk->id }},
        type: type,
        quantity: quantity
    };

    try {
        const response = await fetch('/user/keranjang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Produk telah ditambahkan ke keranjang',
                timer: 2000,
                showConfirmButton: false
            });

            if (checkout) {
                window.location.href = '/user/transaksi/create';
            }
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
    }
}
</script>
@endpush
