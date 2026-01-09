<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- AOS Animation -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Content -->
        <main class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
            @isset($header)
                <header class="bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold mb-2">
                                    {{ $header }}
                                </h1>
                                <p class="text-blue-200 opacity-90">
                                    @isset($subheader)
                                        {{ $subheader }}
                                    @else
                                        SportWear - Platform Alat Olahraga Terbaik
                                    @endisset
                                </p>
                            </div>
                            @isset($headerActions)
                                <div class="mt-4 md:mt-0">
                                    {{ $headerActions }}
                                </div>
                            @endisset
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-gray-800 to-gray-900 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4 text-yellow-400">SportWear</h3>
                        <p class="text-gray-400">
                            Platform penjualan dan penyewaan alat olahraga terlengkap dengan kualitas terbaik.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Navigasi</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('user.dashboard') }}" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('user.produk.index') }}" class="text-gray-400 hover:text-white transition-colors">Produk</a></li>
                            <li><a href="{{ route('user.sewa.index') }}" class="text-gray-400 hover:text-white transition-colors">Sewa</a></li>
                            <li><a href="{{ route('user.keranjang.index') }}" class="text-gray-400 hover:text-white transition-colors">Keranjang</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i>
                                <span>Jl. Olahraga No. 123, Jakarta</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-phone mr-2 text-yellow-400"></i>
                                <span>(021) 1234-5678</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-envelope mr-2 text-yellow-400"></i>
                                <span>info@sportwear.com</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-blue-400 rounded-full flex items-center justify-center transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-700 hover:bg-red-600 rounded-full flex items-center justify-center transition-colors">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} SportWear. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                once: true,
                offset: 100
            });
        </script>
        
        @stack('scripts')
    </body>
</html>