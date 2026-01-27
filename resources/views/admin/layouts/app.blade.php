<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SportWear Admin')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            /* Palet Warna Elegant Sporty - Sama dengan user */
            --primary: #1A365D;       /* Navy Blue - Professional */
            --secondary: #2D3748;     /* Dark Gray - Elegant */
            --accent: #D69E2E;        /* Gold - Luxurious */
            --success: #38A169;       /* Green - Sporty Fresh */
            --warning: #DD6B20;       /* Orange - Energy */
            --danger: #E53E3E;        /* Red - Alert */
            --light: #F8FAFC;         /* Light Gray */
            --dark: #1A202C;          /* Dark Charcoal */
            --card-bg: #FFFFFF;       /* White */
            --sidebar-bg: #1A365D;    /* Sidebar background */
            --sidebar-text: #E2E8F0;  /* Sidebar text */
            --sidebar-hover: #2C5282; /* Sidebar hover */
            --gradient-primary: linear-gradient(135deg, #1A365D 0%, #2C5282 100%);
            --gradient-accent: linear-gradient(135deg, #D69E2E 0%, #ED8936 100%);
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--light);
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* Admin Layout Container */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styling - Custom untuk theme SportWear */
        #sidebar {
            background: var(--gradient-primary);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            width: 16rem;
            transition: all 0.3s;
            z-index: 40;
            box-shadow: 4px 0 15px rgba(26, 54, 93, 0.1);
        }
        
        /* Sidebar Overlay untuk mobile */
        #sidebar-overlay {
            z-index: 30;
        }
        
        /* Sidebar Links */
        #sidebar a {
            color: var(--sidebar-text);
            transition: all 0.3s ease;
            margin: 4px 8px;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        #sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        #sidebar a.active {
            background: rgba(214, 158, 46, 0.2);
            color: white;
            border-left: 3px solid var(--accent);
        }
        
        #sidebar svg {
            color: var(--accent);
        }
        
        #sidebar a:hover svg {
            color: white;
        }
        
        /* Navbar Styling - Custom untuk theme SportWear */
        .bg-white {
            background: linear-gradient(135deg, #1A365D 0%, #2D3748 100%) !important;
            border-bottom: 3px solid var(--accent);
        }
        
        /* Text dalam navbar */
        .text-gray-600, .text-gray-900, .text-gray-500 {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        
        /* Hover effects di navbar */
        .hover\:text-gray-900:hover, 
        .hover\:bg-gray-100:hover,
        .hover\:text-gray-900:hover svg {
            color: white !important;
            background: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Search input styling */
        #topbar-search, #mobile-search {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border-radius: 8px !important;
        }
        
        #topbar-search::placeholder, 
        #mobile-search::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        #topbar-search:focus, 
        #mobile-search:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(214, 158, 46, 0.1) !important;
        }
        
        /* Main Content Area */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 0;
            transition: margin-left 0.3s;
        }
        
        .admin-content {
            flex: 1;
            padding: 20px;
            margin-top: 64px; /* Height navbar */
            background: var(--light);
            min-height: calc(100vh - 64px);
        }
        
        /* Cards untuk admin */
        .admin-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(26, 54, 93, 0.1);
            border: 1px solid #E2E8F0;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 54, 93, 0.15);
        }
        
        /* Table Styling */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(26, 54, 93, 0.1);
        }
        
        .admin-table th {
            background: var(--gradient-primary);
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            border: none;
        }
        
        .admin-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .admin-table tr:hover {
            background: rgba(26, 54, 93, 0.05);
        }
        
        /* Badges untuk admin */
        .admin-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: rgba(56, 161, 105, 0.1);
            color: var(--success);
            border: 1px solid rgba(56, 161, 105, 0.2);
        }
        
        .badge-warning {
            background: rgba(221, 107, 32, 0.1);
            color: var(--warning);
            border: 1px solid rgba(221, 107, 32, 0.2);
        }
        
        .badge-danger {
            background: rgba(229, 62, 62, 0.1);
            color: var(--danger);
            border: 1px solid rgba(229, 62, 62, 0.2);
        }
        
        .badge-info {
            background: rgba(49, 130, 206, 0.1);
            color: #3182CE;
            border: 1px solid rgba(49, 130, 206, 0.2);
        }
        
        /* Button Admin */
        .btn-admin-primary {
            background: var(--gradient-primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-admin-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 54, 93, 0.2);
        }
        
        .btn-admin-accent {
            background: var(--gradient-accent);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-admin-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(214, 158, 46, 0.2);
        }
        
        /* Responsive Design */
        @media (min-width: 1024px) {
            .admin-main {
                margin-left: 16rem;
            }
            
            #sidebar {
                display: flex !important;
            }
        }
        
        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            #sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
        }
        
        /* Scrollbar untuk sidebar */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        #sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        #sidebar::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 3px;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            padding: 16px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .breadcrumb-item {
            color: var(--primary);
            text-decoration: none;
        }
        
        .breadcrumb-item:hover {
            color: var(--accent);
            text-decoration: underline;
        }
        
        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border-top: 4px solid;
            box-shadow: 0 4px 12px rgba(26, 54, 93, 0.08);
        }
        
        .stat-card-primary {
            border-color: var(--primary);
        }
        
        .stat-card-accent {
            border-color: var(--accent);
        }
        
        .stat-card-success {
            border-color: var(--success);
        }
        
        /* Loading State */
        .admin-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
        
        .loading-spinner {
            border: 3px solid rgba(26, 54, 93, 0.1);
            border-radius: 50%;
            border-top: 3px solid var(--accent);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Admin Layout Container -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside 
            id="sidebar" 
            class="fixed hidden z-40 h-full top-0 left-0 pt-16 flex lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75" 
            aria-label="Sidebar"
        >
            <div class="relative flex-1 flex flex-col min-h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-1 px-3 divide-y space-y-1">
                    <ul class="space-y-2 pb-4">
                        <!-- Mobile Search Form -->
                        <li class="lg:hidden mb-4">
                            <form action="{{ $searchAction ?? '#' }}" method="GET">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="q" 
                                        id="mobile-search-sidebar" 
                                        class="w-full pl-10 pr-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                                        placeholder="Search..."
                                        value="{{ request('q') }}"
                                    >
                                </div>
                            </form>
                        </li>
                        
                        <!-- Navigation Items -->
                        @php
                            $adminNavItems = [
                                [
                                    'url' => route('admin.dashboard'),
                                    'label' => 'Dashboard',
                                    'icon' => '<i class="fas fa-tachometer-alt w-5 h-5"></i>',
                                    'active' => request()->routeIs('admin.dashboard')
                                ],
                                [
                                    'url' => route('admin.produk.index'),
                                    'label' => 'Produk',
                                    'icon' => '<i class="fas fa-box w-5 h-5"></i>',
                                    'active' => request()->routeIs('admin.produk.*')
                                ],
                                [
                                    'url' => route('admin.transaksi.index'),
                                    'label' => 'Transaksi',
                                    'icon' => '<i class="fas fa-shopping-cart w-5 h-5"></i>',
                                    'active' => request()->routeIs('admin.transaksi.*')
                                ],
                                [
                                    'url' => route('admin.sewa.index'),
                                    'label' => 'Penyewaan',
                                    'icon' => '<i class="fas fa-calendar-alt w-5 h-5"></i>',
                                    'active' => request()->routeIs('admin.sewa.*')
                                ],
                                [
                                    'url' => route('admin.laporan.index'),
                                    'label' => 'Laporan',
                                    'icon' => '<i class="fas fa-chart-bar w-5 h-5"></i>',
                                    'active' => request()->routeIs('admin.laporan.*')
                                ],
                            ];
                        @endphp
                        
                        @foreach($adminNavItems as $item)
                            <li>
                                <a 
                                    href="{{ $item['url'] }}" 
                                    class="text-base font-normal rounded-lg flex items-center p-3 hover:bg-white/10 group {{ $item['active'] ? 'bg-white/10 text-white border-l-3 border-accent' : 'text-gray-300' }}"
                                >
                                    {!! $item['icon'] !!}
                                    <span class="ml-3">{{ $item['label'] }}</span>
                                    @if(isset($item['badge']))
                                        <span class="ml-auto inline-flex items-center justify-center px-2 text-xs font-medium bg-accent text-white rounded-full">
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    
                    <!-- Bottom Section -->
                    <div class="pt-4 space-y-2">
                        <a href="{{ route('home') }}" target="_blank" class="text-base font-normal rounded-lg hover:bg-white/10 transition duration-75 flex items-center p-3 text-gray-300">
                            <i class="fas fa-external-link-alt w-5 h-5"></i>
                            <span class="ml-3">View Site</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left text-base font-normal rounded-lg hover:bg-red-500/20 hover:text-red-300 transition duration-75 flex items-center p-3 text-gray-300">
                                <i class="fas fa-sign-out-alt w-5 h-5"></i>
                                <span class="ml-3">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Sidebar Overlay untuk mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 lg:hidden hidden"></div>
        
        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Top Navbar -->
@include('admin.components.navbar', [
    'logoUrl' => route('admin.dashboard'),
    'brandName' => 'SportWear Admin',
    'showSearch' => false,
    'searchAction' => route('admin.search'),
    'showGithub' => false
])
            
            <!-- Main Content -->
            <main class="admin-content">
                <!-- Breadcrumb (optional) -->
                @if(isset($breadcrumbs))
                    <nav class="breadcrumb">
                        <ol class="flex space-x-2">
                            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a></li>
                            @foreach($breadcrumbs as $crumb)
                                <li class="flex items-center">
                                    <span class="mx-2 text-gray-400">/</span>
                                    @if(isset($crumb['url']))
                                        <a href="{{ $crumb['url'] }}" class="breadcrumb-item">{{ $crumb['label'] }}</a>
                                    @else
                                        <span class="text-gray-600">{{ $crumb['label'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif
                
                <!-- Page Title -->
                @hasSection('page-title')
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">@yield('page-title')</h1>
                        @hasSection('page-subtitle')
                            <p class="text-gray-600 mt-2">@yield('page-subtitle')</p>
                        @endif
                    </div>
                @endif
                
                <!-- Content -->
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- JavaScript untuk Admin Layout -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Sidebar Mobile
            const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const toggleSidebarMobileHamburger = document.getElementById('toggleSidebarMobileHamburger');
            const toggleSidebarMobileClose = document.getElementById('toggleSidebarMobileClose');
            
            if (toggleSidebarMobile && sidebar) {
                toggleSidebarMobile.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('hidden');
                    
                    if (toggleSidebarMobileHamburger && toggleSidebarMobileClose) {
                        toggleSidebarMobileHamburger.classList.toggle('hidden');
                        toggleSidebarMobileClose.classList.toggle('hidden');
                    }
                });
                
                // Close sidebar when clicking overlay
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.add('hidden');
                        
                        if (toggleSidebarMobileHamburger && toggleSidebarMobileClose) {
                            toggleSidebarMobileHamburger.classList.remove('hidden');
                            toggleSidebarMobileClose.classList.add('hidden');
                        }
                    });
                }
            }
            
            // Toggle Mobile Search
            const toggleSidebarMobileSearch = document.getElementById('toggleSidebarMobileSearch');
            const mobileSearch = document.getElementById('mobile-search');
            
            if (toggleSidebarMobileSearch && mobileSearch) {
                toggleSidebarMobileSearch.addEventListener('click', function() {
                    mobileSearch.classList.toggle('hidden');
                });
            }
            
            // Active link highlighting
            const currentPath = window.location.pathname;
            document.querySelectorAll('#sidebar a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
            
            // Auto-hide sidebar on mobile when clicking link
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.add('hidden');
                        
                        if (toggleSidebarMobileHamburger && toggleSidebarMobileClose) {
                            toggleSidebarMobileHamburger.classList.remove('hidden');
                            toggleSidebarMobileClose.classList.add('hidden');
                        }
                    }
                });
            });
            
            // Responsive sidebar behavior
            function handleResize() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.add('show');
                    sidebar.classList.remove('hidden');
                    if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('show');
                }
            }
            
            // Initial check
            handleResize();
            
            // Listen for resize
            window.addEventListener('resize', handleResize);
            
            // Notifications
            window.Echo && window.Echo.private(`admin.{{ auth()->id() }}`)
                .notification((notification) => {
                    // Play sound
                    const audio = new Audio('/notification.mp3');
                    audio.play().catch(e => console.log('Audio play failed:', e));
                    
                    // Show toast
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({
                            icon: 'info',
                            title: 'Admin Notification',
                            text: notification.message
                        });
                    }
                });
        });
        
        // Utility function untuk format tanggal
        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }
        
        // Utility function untuk format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    </script>
    
    @stack('scripts')
</body>
</html>