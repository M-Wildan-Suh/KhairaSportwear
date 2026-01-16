<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - SportWear</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #ff5858 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }
        
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            border-color: #6366f1;
        }
        
        .btn-login {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .social-btn {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        /* Popup Notification Styles */
        .notification-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .notification-popup.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .notification-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border-left: 5px solid #10b981;
        }
        
        .notification-progress {
            height: 4px;
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            width: 100%;
            animation: progress 5s linear forwards;
        }
        
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #ff6b6b;
            top: 0;
            opacity: 0;
            z-index: 999;
        }
        
        @keyframes confettiRain {
            0% {
                opacity: 1;
                transform: translateY(-100px) rotate(0deg);
            }
            100% {
                opacity: 0;
                transform: translateY(100vh) rotate(720deg);
            }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Success Notification Popup -->
    @if(session('login_success'))
        <div id="successNotification" class="notification-popup show">
            <div class="notification-content">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center">
                                <i class="fas fa-check text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ session('login_success') }}</h3>
                            <p class="text-gray-600 mt-1">
                                Selamat datang kembali, {{ Auth::user()->name ?? 'Pengguna' }}! 
                                Anda akan diarahkan ke dashboard dalam <span id="countdown">5</span> detik.
                            </p>
                            <div class="mt-4 flex space-x-3">
                                <button onclick="redirectNow()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium">
                                    <i class="fas fa-rocket mr-2"></i>Lanjut Sekarang
                                </button>
                                <button onclick="hideNotification()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                                    <i class="fas fa-times mr-2"></i>Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="notification-progress"></div>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                createConfetti();
                startCountdown();
            });
        </script>
    @endif
    
    <!-- Main Login Card -->
    <div class="relative w-full max-w-6xl flex flex-col md:flex-row rounded-3xl overflow-hidden shadow-2xl">
        <!-- Bagian kiri: Ilustrasi dan brand -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 p-12 flex-col justify-between text-white relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-32 h-32 rounded-full bg-white"></div>
                <div class="absolute bottom-10 right-10 w-40 h-40 rounded-full bg-white"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-24 rounded-full bg-white"></div>
            </div>
            
            <!-- Brand dan konten -->
            <div class="relative z-10">
                <div class="flex items-center space-x-2 mb-8">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-pink-500 to-orange-500 flex items-center justify-center">
                        <i class="fas fa-running text-white"></i>
                    </div>
                    <span class="text-2xl font-bold">SportWear</span>
                </div>
                
                <h1 class="text-4xl font-bold mb-6">Kembali Beraksi <br> dengan SportWear</h1>
                <p class="text-lg opacity-90 mb-10">Temukan koleksi terbaru untuk mendukung aktivitas olahraga dan gaya hidup sehat Anda.</p>
            </div>
            
            <!-- Ilustrasi -->
            <div class="relative z-10 mt-8">
                <div class="flex space-x-6">
                    <div class="bg-white bg-opacity-20 p-4 rounded-2xl backdrop-blur-sm">
                        <i class="fas fa-shipping-fast text-2xl mb-2"></i>
                        <p class="text-sm font-medium">Gratis Ongkir</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-2xl backdrop-blur-sm">
                        <i class="fas fa-award text-2xl mb-2"></i>
                        <p class="text-sm font-medium">Kualitas Terbaik</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-2xl backdrop-blur-sm">
                        <i class="fas fa-headset text-2xl mb-2"></i>
                        <p class="text-sm font-medium">Bantuan 24/7</p>
                    </div>
                </div>
            </div>
            
            <!-- Elemen dekoratif floating -->
            <div class="absolute -bottom-4 -right-4 w-48 h-48 bg-gradient-to-r from-pink-500 to-yellow-500 rounded-full opacity-20 floating-animation"></div>
        </div>
        
        <!-- Bagian kanan: Form login -->
        <div class="w-full md:w-1/2 glass-card p-8 md:p-12">
            <!-- Header form untuk mobile -->
            <div class="md:hidden flex items-center justify-center mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-running text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-800">SportWear</span>
                </div>
            </div>
            
            <!-- Judul form -->
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang Kembali</h2>
                <p class="text-gray-600">Masuk ke akun SportWear Anda</p>
            </div>
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                        <span class="font-medium">Terdapat kesalahan:</span>
                    </div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Form login -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <!-- Email Address -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="email"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                               placeholder="nama@contoh.com">
                    </div>
                    @error('email')
                        <div class="mt-2 text-red-600 text-sm flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-gray-700 font-medium">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                               placeholder="Masukkan password Anda">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <div class="mt-2 text-red-600 text-sm flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               name="remember"
                               class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300">
                        <label for="remember_me" class="ml-2 text-gray-700">Ingat saya</label>
                    </div>
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="btn-login w-full py-3.5 px-4 rounded-xl text-white font-semibold shadow-lg mb-6">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk ke Akun
                </button>
                
                <!-- Pemisah -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Atau masuk dengan</span>
                    </div>
                </div>
                
                <!-- Login sosial -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <button type="button" class="social-btn flex items-center justify-center py-3 rounded-xl bg-white">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        <span class="font-medium text-gray-700">Google</span>
                    </button>
                    <button type="button" class="social-btn flex items-center justify-center py-3 rounded-xl bg-white">
                        <i class="fab fa-facebook text-blue-600 mr-2"></i>
                        <span class="font-medium text-gray-700">Facebook</span>
                    </button>
                </div>
                
                <!-- Link registrasi -->
                <div class="text-center pt-6 border-t border-gray-200">
                    <p class="text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 ml-1">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Footer copyright -->
    <div class="absolute bottom-4 left-0 right-0 text-center text-white text-sm opacity-80">
        &copy; {{ date('Y') }} SportWear. Semua hak dilindungi.
    </div>
    
    <script>
        // Toggle visibility password
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Animasi input focus
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-indigo-200');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-indigo-200');
            });
        });
        
        // Notification Functions
        let countdownInterval;
        let countdownTime = 5;
        
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            
            countdownTime = 5;
            countdownElement.textContent = countdownTime;
            
            countdownInterval = setInterval(() => {
                countdownTime--;
                countdownElement.textContent = countdownTime;
                
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    redirectToDashboard();
                }
            }, 1000);
            
            // Auto-redirect setelah 6 detik
            setTimeout(() => {
                const notification = document.getElementById('successNotification');
                if (notification && notification.classList.contains('show')) {
                    redirectToDashboard();
                }
            }, 6000);
        }
        
            function redirectToDashboard() {
        // Redirect berdasarkan role user
        @auth
            @if(auth()->user()->isAdmin())
                window.location.href = "{{ route('admin.dashboard') }}";
            @else
                window.location.href = "{{ route('user.dashboard') }}";
            @endif
        @endauth
    }
    
        function hideNotification() {
            const notification = document.getElementById('successNotification');
            if (notification) {
                notification.classList.remove('show');
                clearInterval(countdownInterval);
                
                // Hapus dari DOM setelah animasi
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }
        
        function redirectNow() {
            clearInterval(countdownInterval);
            redirectToDashboard();
        }
        
        function redirectToDashboard() {
            // Redirect berdasarkan role user
            @if(Auth::check())
                @if(Auth::user()->isAdmin())
                    window.location.href = "{{ route('admin.dashboard') }}";
                @else
                    window.location.href = "{{ route('user.dashboard') }}";
                @endif
            @endif
        }
        
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                
                // Animasi
                confetti.style.animation = `confettiRain ${Math.random() * 3 + 2}s linear forwards`;
                
                document.body.appendChild(confetti);
                
                // Hapus elemen setelah animasi selesai
                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.remove();
                    }
                }, 5000);
            }
        }
        
        // Form submission animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Tampilkan loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;
            
            // Biarkan form submit normal
        });
    </script>
</body>
</html>