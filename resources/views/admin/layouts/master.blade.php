<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - SportWear')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
        }
        
        body {
            background-color: #f8f9fc;
        }
        
        #wrapper {
            display: flex;
        }
        
        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2B6CB0 0%, #1a365d 100%);
            color: white;
            position: fixed;
            height: 100vh;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 20px 15px;
            text-align: center;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h2 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        
        .sidebar-brand span {
            color: #ED8936;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: #ED8936;
        }
        
        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
        }
        
        .nav-link .badge {
            margin-left: auto;
        }
        
        /* Topbar Styles */
        #content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        #topbar {
            height: var(--topbar-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        #sidebar-toggle {
            background: none;
            border: none;
            color: #4a5568;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            color: #4a5568;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .user-dropdown-toggle:hover {
            background: #f7fafc;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #ED8936 0%, #DD6B20 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            min-width: 200px;
            padding: 10px 0;
            display: none;
        }
        
        .user-dropdown:hover .user-dropdown-menu {
            display: block;
        }
        
        .dropdown-item {
            display: block;
            padding: 10px 20px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .dropdown-item:hover {
            background: #f7fafc;
            color: #2B6CB0;
        }
        
        /* Main Content */
        #main-content {
            flex: 1;
            padding: 30px;
            background: #f8f9fc;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        /* Footer */
        #footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 0.9rem;
            border-top: 1px solid #e2e8f0;
            background: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            #content-wrapper {
                margin-left: 0;
            }
            
            #topbar {
                padding: 0 15px;
            }
        }
        
        /* Custom Scrollbar for Sidebar */
        #sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        #sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-brand">
                <h2>Sport<span>Wear</span> <small>Admin</small></h2>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.produk.index') }}" class="nav-link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
                        <i class="fas fa-dumbbell"></i>
                        <span>Produk</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.kategori.index') }}" class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.transaksi.index') }}" class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Transaksi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.sewa.index') }}" class="nav-link {{ request()->routeIs('admin.sewa.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Penyewaan</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.pengembalian.index') }}" class="nav-link {{ request()->routeIs('admin.pengembalian.*') ? 'active' : '' }}">
                        <i class="fas fa-undo"></i>
                        <span>Pengembalian</span>
                        <span class="badge badge-warning">3</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.denda.index') }}" class="nav-link {{ request()->routeIs('admin.denda.*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Denda</span>
                        <span class="badge badge-danger">5</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.laporan.index') }}" class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                
                <li class="nav-item mt-4">
                    <a href="{{ route('logout') }}" class="nav-link text-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        
        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Topbar -->
            <header id="topbar">
                <button id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="user-dropdown">
                    <button class="user-dropdown-toggle">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">Admin</div>
                        </div>
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    
                    <div class="user-dropdown-menu">
                        <a href="{{ route('admin.profile') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="{{ route('admin.settings') }}" class="dropdown-item">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item text-danger"
                           onclick="event.preventDefault(); document.getElementById('logout-form-topbar').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                        <form id="logout-form-topbar" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>
            
            <!-- Main Content -->
            <main id="main-content">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer id="footer">
                <div class="container">
                    <p>&copy; {{ date('Y') }} SportWear - Platform Penjualan & Penyewaan Alat Olahraga</p>
                    <p class="mb-0">v1.0.0 | Made with <i class="fas fa-heart text-danger"></i></p>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Sidebar toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Auto-hide sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
        
        // Mark notifications as read when clicked
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                // AJAX call to mark as read
                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
            });
        });
        
        // Auto-update dashboard every 60 seconds
        setInterval(() => {
            if (window.location.pathname === '/admin/dashboard') {
                // Refresh certain elements or fetch new data
                console.log('Auto-refreshing dashboard data...');
            }
        }, 60000);
    </script>
    
    @stack('scripts')
</body>
</html>