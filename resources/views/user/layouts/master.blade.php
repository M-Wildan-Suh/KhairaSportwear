<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - SportWear')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Styles untuk Admin -->
    <style>
        :root {
            --primary: #1A365D;
            --secondary: #2D3748;
            --accent: #D69E2E;
            --success: #38A169;
            --warning: #DD6B20;
            --danger: #E53E3E;
            --light: #F8FAFC;
            --dark: #1A202C;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Sidebar */
        #sidebar {
            background: linear-gradient(180deg, #1A365D 0%, #2D3748 100%);
            color: white;
            transition: all 0.3s;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar-item {
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar-item.active {
            background: rgba(214, 158, 46, 0.2);
            color: white;
            border-left-color: var(--accent);
        }
        
        .sidebar-item i {
            width: 20px;
            text-align: center;
        }
        
        .sidebar-subitem {
            padding: 10px 20px 10px 52px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            font-size: 14px;
            border-radius: 6px;
            margin: 2px 8px;
            transition: all 0.3s;
        }
        
        .sidebar-subitem:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 16rem;
            transition: margin-left 0.3s;
        }
        
        @media (max-width: 1024px) {
            #sidebar {
                margin-left: -16rem;
            }
            
            #sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        /* Table */
        .table-admin {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .table-admin th {
            background: #f8fafc;
            font-weight: 600;
            color: #1A365D;
            padding: 16px 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table-admin td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .table-admin tr:hover {
            background: #f8fafc;
        }
        
        /* Badges */
        .badge-admin {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 z-40 h-screen w-64 pt-16 overflow-y-auto">
        <div class="px-4 py-6">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-4 mb-8">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center">
                    <i class="fas fa-crown text-white"></i>
                </div>
                <div>
                    <h2 class="font-bold text-white">Admin Panel</h2>
                    <p class="text-xs text-gray-300">SportWear</p>
                </div>
            </div>
            
            <!-- Menu -->
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Produk -->
                <div class="mb-2">
                    <a href="{{ route('admin.produk.index') }}" 
                       class="sidebar-item {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>Produk</span>
                    </a>
                    @if(request()->routeIs('admin.produk.*'))
                    <div class="mt-1">
                        <a href="{{ route('admin.produk.index') }}" 
                           class="sidebar-subitem {{ request()->routeIs('admin.produk.index') ? 'text-yellow-400' : '' }}">
                            • Daftar Produk
                        </a>
                        <a href="{{ route('admin.produk.create') }}" 
                           class="sidebar-subitem {{ request()->routeIs('admin.produk.create') ? 'text-yellow-400' : '' }}">
                            • Tambah Produk
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Kategori -->
                <a href="{{ route('admin.kategori.index') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Kategori</span>
                </a>
                
                <!-- Transaksi -->
                <div class="mb-2">
                    <a href="{{ route('admin.transaksi.index') }}" 
                       class="sidebar-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Transaksi</span>
                    </a>
                </div>
                
                <!-- Sewa -->
                <div class="mb-2">
                    <a href="{{ route('admin.sewa.index') }}" 
                       class="sidebar-item {{ request()->routeIs('admin.sewa.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Penyewaan</span>
                    </a>
                </div>
                
                <!-- Pengguna -->
                <a href="{{ route('admin.pengguna.index') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
                
                <!-- Laporan -->
                <div class="mb-2">
                    <a href="{{ route('admin.laporan.index') }}" 
                       class="sidebar-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                    @if(request()->routeIs('admin.laporan.*'))
                    <div class="mt-1">
                        <a href="{{ route('admin.laporan.penjualan') }}" 
                           class="sidebar-subitem {{ request()->routeIs('admin.laporan.penjualan') ? 'text-yellow-400' : '' }}">
                            • Laporan Penjualan
                        </a>
                        <a href="{{ route('admin.laporan.penyewaan') }}" 
                           class="sidebar-subitem {{ request()->routeIs('admin.laporan.penyewaan') ? 'text-yellow-400' : '' }}">
                            • Laporan Penyewaan
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Settings -->
                <a href="{{ route('admin.setting.index') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.setting.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
            
            <!-- Divider -->
            <div class="my-8 border-t border-gray-700"></div>
            
            <!-- User Info -->
            <div class="px-4 py-3 bg-white/5 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center">
                        <span class="font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-300 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-30">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Left: Toggle & Breadcrumb -->
                <div class="flex items-center space-x-4">
                    <!-- Sidebar Toggle -->
                    <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    
                    <!-- Breadcrumb -->
                    <div class="hidden lg:flex items-center space-x-2 text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-home"></i>
                        </a>
                        <span class="text-gray-400">/</span>
                        @yield('breadcrumb', '<span class="text-gray-600">Dashboard</span>')
                    </div>
                </div>
                
                <!-- Right: User & Notifications -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block relative">
                        <input type="text" 
                               placeholder="Cari..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="p-2 rounded-lg hover:bg-gray-100 relative">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.laporan.penjualan') }}" 
                           class="px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-800 transition-all">
                            <i class="fas fa-chart-line mr-1"></i> Laporan
                        </a>
                        
                        <!-- Logout -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-3 py-2 border border-red-600 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content pt-16">
        @yield('content')
    </main>
    
    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>
    
    <!-- Scripts -->
    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });
        
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
        
        // Close sidebar on click outside (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) && 
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
        
        // Auto-hide sidebar on mobile when clicking a link
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
        
        // Update active sidebar item based on current URL
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar-item').forEach(item => {
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('active');
                }
            });
        });
        
        // Chart initialization helper
        window.initChart = function(canvasId, config) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            return new Chart(ctx, config);
        };
    </script>
    
    @stack('scripts')
</body>
</html>