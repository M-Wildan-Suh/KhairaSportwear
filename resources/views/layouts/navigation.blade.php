<nav x-data="{ open: false, cartOpen: false }" class="bg-gradient-to-r from-blue-900 via-blue-800 to-slate-900 text-white shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo and Mobile Menu Button -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="open = !open" 
                        class="lg:hidden p-2 rounded-lg text-gray-300 hover:text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('user.dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-dumbbell text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                            SportWear
                        </span>
                    </a>
                </div>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('user.dashboard') }}" 
                   class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('user.produk.index') }}" 
                   class="nav-link {{ request()->routeIs('user.produk.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart mr-2"></i> Produk
                </a>
                
                <a href="{{ route('user.sewa.index') }}" 
                   class="nav-link {{ request()->routeIs('user.sewa.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt mr-2"></i> Sewa
                </a>
                
                <a href="{{ route('user.keranjang.index') }}" 
                   class="nav-link {{ request()->routeIs('user.keranjang.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag mr-2"></i> Keranjang
                </a>
            </div>

            <!-- Right Side Items -->
            <div class="flex items-center space-x-4">
                <!-- Cart Icon -->
                <div class="relative">
                    <button @click="cartOpen = !cartOpen" 
                            class="p-2 rounded-lg text-gray-300 hover:text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        @if($cartCount = auth()->user()->keranjang()->count() ?? 0)
                        <span class="cart-count absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </button>
                    
                    <!-- Cart Dropdown -->
                    <div x-show="cartOpen" 
                         @click.away="cartOpen = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden"
                         :class="{'hidden': !cartOpen}"
                         style="display: none;">
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Keranjang Anda</h3>
                                <span class="text-sm text-gray-500">{{ $cartCount }} item</span>
                            </div>
                            
                            @if($cartCount > 0)
                                <!-- Cart items would go here -->
                                <div class="space-y-3 max-h-64 overflow-y-auto">
                                    <!-- Sample cart item -->
                                    <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-dumbbell text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-gray-800">Dumbbell Set</h4>
                                            <p class="text-xs text-gray-500">Beli • 1 item</p>
                                            <p class="text-sm font-bold text-blue-600">Rp 450.000</p>
                                        </div>
                                        <button class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between mb-4">
                                        <span class="text-gray-700">Total</span>
                                        <span class="text-lg font-bold text-blue-700">Rp 450.000</span>
                                    </div>
                                    <a href="{{ route('user.keranjang.index') }}" 
                                       class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-2 rounded-lg font-semibold transition-all duration-300">
                                        Lihat Keranjang
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-600">Keranjang masih kosong</p>
                                    <a href="{{ route('user.produk.index') }}" 
                                       class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-semibold">
                                        Belanja Sekarang →
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="relative">
                    <button class="p-2 rounded-lg text-gray-300 hover:text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                            3
                        </span>
                    </button>
                </div>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ userOpen: false }">
                    <button @click="userOpen = !userOpen" 
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="w-9 h-9 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center font-bold text-white shadow-lg">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden lg:block text-left">
                            <div class="font-semibold text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-300">{{ Auth::user()->email }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-300"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userOpen" 
                         @click.away="userOpen = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden"
                         :class="{'hidden': !userOpen}"
                         style="display: none;">
                        <div class="py-2">
                            <a href="{{ route('user.profile') }}" 
                               class="dropdown-link">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i> Profile
                            </a>
                            <a href="{{ route('user.orders') }}" 
                               class="dropdown-link">
                                <i class="fas fa-clipboard-list mr-3 text-gray-400"></i> Pesanan
                            </a>
                            <a href="{{ route('user.rentals') }}" 
                               class="dropdown-link">
                                <i class="fas fa-calendar-check mr-3 text-gray-400"></i> Sewaan
                            </a>
                            <div class="border-t border-gray-200 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="dropdown-link text-red-600 hover:text-red-700">
                                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-gradient-to-b from-blue-800 to-blue-900 text-white">
        <div class="px-4 pt-2 pb-4 space-y-1">
            <a href="{{ route('user.dashboard') }}" 
               class="mobile-nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            
            <a href="{{ route('user.produk.index') }}" 
               class="mobile-nav-link {{ request()->routeIs('user.produk.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart mr-3"></i> Produk
            </a>
            
            <a href="{{ route('user.sewa.index') }}" 
               class="mobile-nav-link {{ request()->routeIs('user.sewa.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt mr-3"></i> Sewa
            </a>
            
            <a href="{{ route('user.keranjang.index') }}" 
               class="mobile-nav-link {{ request()->routeIs('user.keranjang.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag mr-3"></i> Keranjang
                @if($cartCount > 0)
                <span class="bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center ml-auto">
                    {{ $cartCount }}
                </span>
                @endif
            </a>
            
            <div class="border-t border-blue-700 mt-2 pt-2">
                <a href="{{ route('user.profile') }}" 
                   class="mobile-nav-link">
                    <i class="fas fa-user-circle mr-3"></i> Profile
                </a>
                <a href="{{ route('user.orders') }}" 
                   class="mobile-nav-link">
                    <i class="fas fa-clipboard-list mr-3"></i> Pesanan
                </a>
                <a href="{{ route('user.rentals') }}" 
                   class="mobile-nav-link">
                    <i class="fas fa-calendar-check mr-3"></i> Sewaan
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="mobile-nav-link text-red-400 hover:text-red-300 w-full text-left">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
.nav-link {
    @apply px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center;
    color: rgba(255, 255, 255, 0.9);
}

.nav-link:hover {
    @apply bg-blue-800 text-white transform -translate-y-0.5;
    color: white;
}

.nav-link.active {
    @apply bg-gradient-to-r from-yellow-500 to-orange-500 text-white shadow-lg;
}

.mobile-nav-link {
    @apply px-4 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center;
    color: rgba(255, 255, 255, 0.9);
}

.mobile-nav-link:hover {
    @apply bg-blue-700 text-white;
}

.mobile-nav-link.active {
    @apply bg-gradient-to-r from-yellow-500 to-orange-500 text-white;
}

.dropdown-link {
    @apply block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 flex items-center;
}

.cart-count {
    animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
    75%, 100% {
        transform: scale(1.1);
        opacity: 0.7;
    }
}
</style>
@endpush