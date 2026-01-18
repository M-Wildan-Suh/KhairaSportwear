<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SportWear - Platform Alat Olahraga')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Styles -->
<style>
    :root {
        /* Palet Warna Elegant Sporty */
        --primary: #1A365D;       /* Navy Blue - Professional */
        --secondary: #2D3748;     /* Dark Gray - Elegant */
        --accent: #D69E2E;        /* Gold - Luxurious */
        --success: #38A169;       /* Green - Sporty Fresh */
        --warning: #DD6B20;       /* Orange - Energy */
        --light: #F8FAFC;         /* Light Gray */
        --dark: #1A202C;          /* Dark Charcoal */
        --card-bg: #FFFFFF;       /* White */
        --gradient-primary: linear-gradient(135deg, #1A365D 0%, #2C5282 100%);
        --gradient-accent: linear-gradient(135deg, #D69E2E 0%, #ED8936 100%);
    }
    
    body {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        background: var(--light);
        color: var(--dark);
        overflow-x: hidden;
    }
    
    /* Navbar */
    .navbar-sport {
        background: linear-gradient(135deg, #1A365D 0%, #2D3748 100%);
        box-shadow: 0 4px 20px rgba(26, 54, 93, 0.15);
        padding: 15px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
    }
    
    .navbar-brand-sport {
        font-weight: 800;
        font-size: 1.8rem;
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .navbar-brand-sport i {
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .nav-link-sport {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .nav-link-sport:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .nav-link-sport.active {
        color: white;
        background: rgba(214, 158, 46, 0.2);
        border-left: 3px solid var(--accent);
    }
    
    .nav-link-sport.active::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        background: var(--accent);
        border-radius: 50%;
    }
    
    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--gradient-accent);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(214, 158, 46, 0.3);
    }
    
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: linear-gradient(135deg, #F56565 0%, #ED64A6 100%);
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(245, 101, 101, 0.3);
    }
    
    /* User Dropdown */
    .user-dropdown-sport {
        position: relative;
    }
    
    .user-avatar-sport {
        width: 40px;
        height: 40px;
        background: var(--gradient-accent);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .user-avatar-sport:hover {
        transform: scale(1.05);
        border-color: var(--accent);
    }
    
    .dropdown-menu-sport {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        box-shadow: 0 10px 40px rgba(26, 54, 93, 0.2);
        border-radius: 12px;
        min-width: 220px;
        padding: 10px 0;
        margin-top: 10px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        z-index: 1001;
        border: 1px solid #E2E8F0;
    }
    
    .user-dropdown-sport:hover .dropdown-menu-sport {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .dropdown-item-sport {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    
    .dropdown-item-sport:hover {
        background: linear-gradient(90deg, rgba(214, 158, 46, 0.1) 0%, rgba(214, 158, 46, 0.05) 100%);
        color: var(--primary);
        padding-left: 25px;
        border-left-color: var(--accent);
    }
    
    /* Mobile Menu */
    .mobile-menu {
        background: linear-gradient(135deg, #2D3748 0%, #1A365D 100%);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        border-radius: 0 0 12px 12px;
        margin-top: 10px;
    }
    
    .mobile-menu.open {
        max-height: 500px;
    }
    
    .mobile-nav-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        border-radius: 8px;
        margin: 4px 8px;
        transition: all 0.3s ease;
    }
    
    .mobile-nav-link:hover {
        background: rgba(214, 158, 46, 0.2);
        color: white;
        transform: translateX(5px);
    }
    
    .mobile-nav-link i {
        width: 24px;
        text-align: center;
        margin-right: 12px;
        color: var(--accent);
    }
    
    /* Footer */
    .footer-sport {
        background: linear-gradient(135deg, var(--dark) 0%, #1A365D 100%);
        color: white;
        padding: 60px 0 20px;
        border-top: 3px solid var(--accent);
    }
    
    .footer-title {
        color: white;
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }
    
    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--gradient-accent);
        border-radius: 2px;
    }
    
    /* Button Styles */
    .btn-primary-sport {
        background: var(--gradient-primary);
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary-sport:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(26, 54, 93, 0.2);
    }
    
    .btn-accent-sport {
        background: var(--gradient-accent);
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-accent-sport:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(214, 158, 46, 0.2);
    }
    
    /* Card Styles */
    .card-sport {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(26, 54, 93, 0.1);
        border: 1px solid #E2E8F0;
        transition: all 0.3s ease;
    }
    
    .card-sport:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(26, 54, 93, 0.15);
    }
    
    .card-sport-accent {
        border-top: 4px solid var(--accent);
    }
    
    .card-sport-primary {
        border-top: 4px solid var(--primary);
    }
    
    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .floating {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    /* Loading Skeleton */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 10px;
    }
    
    ::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--gradient-primary);
        border-radius: 10px;
        transition: background 0.3s ease;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2C5282 0%, #1A365D 100%);
    }
    
    /* Badge Styles */
    .badge-sport {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-primary {
        background: rgba(26, 54, 93, 0.1);
        color: var(--primary);
        border: 1px solid rgba(26, 54, 93, 0.2);
    }
    
    .badge-accent {
        background: rgba(214, 158, 46, 0.1);
        color: var(--accent);
        border: 1px solid rgba(214, 158, 46, 0.2);
    }
    
    .badge-success {
        background: rgba(56, 161, 105, 0.1);
        color: var(--success);
        border: 1px solid rgba(56, 161, 105, 0.2);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .dropdown-menu-sport {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 20px 20px 0 0;
            transform: translateY(100%);
        }
        
        .user-dropdown-sport:hover .dropdown-menu-sport {
            transform: translateY(0);
        }
        
        .navbar-brand-sport {
            font-size: 1.5rem;
        }
    }
    
    /* Login/Register Buttons */
    .btn-login {
        padding: 8px 24px;
        border: 2px solid var(--accent);
        color: var(--accent);
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
    }
    
    .btn-login:hover {
        background: var(--accent);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-register {
        padding: 8px 24px;
        background: var(--gradient-accent);
        color: white;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(214, 158, 46, 0.2);
    }
    
    /* Social Icons */
    .social-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .social-icon:hover {
        background: var(--accent);
        transform: translateY(-3px);
        border-color: var(--accent);
    }
</style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-sport">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <!-- Brand -->
                <a href="{{ route('home') }}" class="navbar-brand-sport">
                    <i class="fas fa-dumbbell"></i>
                    Sport<span>Wear</span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('home') }}" class="nav-link-sport {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home mr-2"></i> Home
                    </a>
                    <a href="{{ route('produk.index') }}" class="nav-link-sport {{ request()->routeIs('user.produk.*') ? 'active' : '' }}">
                        <i class="fas fa-store mr-2"></i> Produk
                    </a>
                    <a href="{{ route('sewa.index') }}" class="nav-link-sport {{ request()->routeIs('user.sewa.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt mr-2"></i> Sewa
                    </a>
                    <a href="{{ route('user.transaksi.index') }}" class="nav-link-sport {{ request()->routeIs('user.transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-history mr-2"></i> Histori
                    </a>
                    
                    <!-- Cart -->
                    <a href="{{ route('user.keranjang.index') }}" class="nav-link-sport relative">
                        <i class="fas fa-shopping-cart"></i>
                        @if(auth()->check() && auth()->user()->getCartCount() > 0)
                        <span class="cart-badge">{{ auth()->user()->getCartCount() }}</span>
                        @endif
                    </a>
                    
                    <!-- Notifications -->
                    <a href="{{ route('user.notifikasi.index') }}" class="nav-link-sport relative">
                        <i class="fas fa-bell"></i>
                        @if(auth()->check() && auth()->user()->getUnreadNotificationsCount() > 0)
                        <span class="notification-badge">{{ auth()->user()->getUnreadNotificationsCount() }}</span>
                        @endif
                    </a>
                    
                    <!-- User Dropdown -->
                    @auth
                    <div class="user-dropdown-sport">
                        <div class="user-avatar-sport">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="dropdown-menu-sport">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <div class="font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                                <small class="text-gray-500 text-sm">{{ auth()->user()->email }}</small>
                            </div>
                            <a href="{{ route('user.dashboard') }}" class="dropdown-item-sport">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                            <a href="{{ route('user.profil.edit') }}" class="dropdown-item-sport">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="{{ route('user.transaksi.index') }}" class="dropdown-item-sport">
                                <i class="fas fa-receipt"></i> Transaksi
                            </a>
                            <a href="{{ route('user.sewa.aktif') }}" class="dropdown-item-sport">
                                <i class="fas fa-running"></i> Sewa Aktif
                            </a>
                            <div class="border-t border-gray-200 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-item-sport">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 text-red-600 hover:text-red-700">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('login') }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">Daftar</a>
                    </div>
                    @endauth
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" id="mobileMenuToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div class="mobile-menu lg:hidden mt-3" id="mobileMenu">
                <div class="space-y-1">
                    <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="{{ route('produk.index') }}" class="mobile-nav-link {{ request()->routeIs('user.produk.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-store"></i> Produk
                    </a>
                    <a href="{{ route('sewa.index') }}" class="mobile-nav-link {{ request()->routeIs('user.sewa.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-calendar-alt"></i> Sewa
                    </a>
                    <a href="{{ route('user.transaksi.index') }}" class="mobile-nav-link {{ request()->routeIs('user.transaksi.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-history"></i> Histori
                    </a>
                    <a href="{{ route('user.keranjang.index') }}" class="mobile-nav-link {{ request()->routeIs('user.keranjang.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                        @if(auth()->check() && auth()->user()->getCartCount() > 0)
                        <span class="ml-auto bg-gradient-to-r from-orange-500 to-yellow-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                            {{ auth()->user()->getCartCount() }}
                        </span>
                        @endif
                    </a>
                    
                    @auth
                    <hr class="my-2 border-gray-200">
                    <a href="{{ route('user.dashboard') }}" class="mobile-nav-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('user.profil.edit') }}" class="mobile-nav-link">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="{{ route('user.notifikasi.index') }}" class="mobile-nav-link">
                        <i class="fas fa-bell"></i> Notifikasi
                        @if(auth()->check() && auth()->user()->getUnreadNotificationsCount() > 0)
                        <span class="ml-auto bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                            {{ auth()->user()->getUnreadNotificationsCount() }}
                        </span>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full mobile-nav-link text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                    @else
                    <hr class="my-2 border-gray-200">
                    <div class="grid grid-cols-2 gap-2 p-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors text-center">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all text-center">
                            Daftar
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="footer-sport">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h4 class="footer-title">SportWear</h4>
                    <p class="text-gray-300 mt-3">Platform penjualan dan penyewaan alat olahraga terlengkap dengan kualitas terbaik dan harga terjangkau.</p>
                    <div class="flex gap-3 mt-4">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="footer-title">Menu</h4>
                    <ul class="space-y-2 mt-3">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('produk.index') }}" class="text-gray-300 hover:text-white transition-colors">Produk</a></li>
                        <li><a href="{{ route('sewa.index') }}" class="text-gray-300 hover:text-white transition-colors">Sewa</a></li>
                        <li><a href="{{ route('user.transaksi.index') }}" class="text-gray-300 hover:text-white transition-colors">Transaksi</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-title">Kontak</h4>
                    <ul class="space-y-3 mt-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400"></i>
                            <span class="text-gray-300">Jl. Olahraga No. 123, Jakarta</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>
                            <span class="text-gray-300">(021) 1234-5678</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>
                            <span class="text-gray-300">info@sportwear.com</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-title">Jam Operasional</h4>
                    <ul class="space-y-2 mt-3">
                        <li class="text-gray-300">Senin - Jumat: 08:00 - 21:00</li>
                        <li class="text-gray-300">Sabtu: 09:00 - 18:00</li>
                        <li class="text-gray-300">Minggu: 10:00 - 16:00</li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-gray-700 my-8">
            
            <div class="text-center pt-3">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} SportWear. All rights reserved.
                    <span class="hidden md:inline"> | </span>
                    <br class="md:hidden">
                    Made with <i class="fas fa-heart text-red-400 mx-1"></i> for sports lovers
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIcon = mobileMenuToggle.querySelector('i');
        
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('open');
            
            if (mobileMenu.classList.contains('open')) {
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-times');
            } else {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                mobileMenu.classList.remove('open');
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });
        
        // Close mobile menu when clicking a link
        mobileMenu.querySelectorAll('a, button').forEach(element => {
            element.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            });
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add active class to current page in navigation
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link-sport').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
        
        // AOS initialization
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                once: true,
                offset: 100
            });
        }
        
        // Cart update notification
        window.addEventListener('cartUpdated', function(e) {
            const cartBadges = document.querySelectorAll('.cart-badge, .cart-count-badge');
            cartBadges.forEach(badge => {
                badge.textContent = e.detail.count;
                badge.classList.add('animate-pulse');
                setTimeout(() => {
                    badge.classList.remove('animate-pulse');
                }, 1000);
            });
            
            // Show success message
            if (typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'success',
                    title: 'Keranjang berhasil diperbarui!'
                });
            }
        });
        
        // Real-time notifications check
        if (window.Echo) {
            window.Echo.private(`user.{{ auth()->id() }}`)
                .notification((notification) => {
                    // Update notification badges
                    const badges = document.querySelectorAll('.notification-badge');
                    badges.forEach(badge => {
                        const currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + 1;
                        badge.classList.add('animate-pulse');
                        setTimeout(() => {
                            badge.classList.remove('animate-pulse');
                        }, 1000);
                    });
                    
                    // Show toast notification
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({
                            icon: 'info',
                            title: notification.title,
                            text: notification.message
                        });
                    }
                });
        }
    </script>
    
    @stack('scripts')
</body>
</html>