<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - SportWear</title>
    
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            border-color: #6366f1;
        }
        
        .btn-register {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
        
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .step-indicator {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .step-line {
            height: 2px;
            flex: 1;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="relative w-full max-w-4xl flex rounded-3xl overflow-hidden shadow-2xl">
        <!-- Bagian kiri: Form registrasi -->
        <div class="w-full glass-card p-8 md:p-10">
            <!-- Header mobile -->
            <div class="md:hidden flex items-center justify-center mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-800">SportWear</span>
                </div>
            </div>
            
            <!-- Judul form -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Bergabung dengan SportWear</h2>
                <p class="text-gray-600">Mulai perjalanan olahraga Anda bersama kami</p>
            </div>
            
            <!-- Indikator langkah (opsional untuk multi-step form) -->
            <div class="flex items-center justify-center mb-10">
                <div class="step-indicator bg-indigo-600 text-white">1</div>
                <div class="step-line bg-indigo-600 mx-2"></div>
                <div class="step-indicator bg-gray-200 text-gray-500">2</div>
                <div class="step-line bg-gray-200 mx-2"></div>
                <div class="step-indicator bg-gray-200 text-gray-500">3</div>
            </div>
            
            <!-- Form registrasi -->
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf
                
                <!-- Grid untuk nama dan email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="name" 
                                   type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="name"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                                   placeholder="Nama lengkap Anda">
                        </div>
                        @error('name')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
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
                </div>
                
                <!-- Grid untuk telepon dan alamat -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-gray-700 font-medium mb-2">Nomor Telepon</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input id="phone" 
                                   type="tel" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   required 
                                   autocomplete="tel"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                                   placeholder="0812 3456 7890">
                        </div>
                        @error('phone')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-gray-700 font-medium mb-2">Alamat Lengkap</label>
                        <div class="relative">
                            <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                            </div>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3" 
                                      required
                                      class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                                      placeholder="Jl. Contoh No. 123, Kota Anda">{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Grid untuk password dan konfirmasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                                   placeholder="Minimal 8 karakter">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Password strength indicator -->
                        <div class="mt-3">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs text-gray-500">Kekuatan password:</span>
                                <span id="passwordStrengthText" class="text-xs font-medium">Lemah</span>
                            </div>
                            <div class="password-strength bg-gray-200 w-full">
                                <div id="passwordStrengthBar" class="progress-bar bg-red-500 w-1/4"></div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Gunakan kombinasi huruf, angka, dan simbol
                            </div>
                        </div>
                        
                        @error('password')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password_confirmation" 
                                   type="password" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-indigo-500 input-focus transition duration-200"
                                   placeholder="Ulangi password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="toggleConfirmPassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div id="passwordMatch" class="mt-2 text-xs hidden">
                            <i class="fas fa-check-circle mr-1 text-green-500"></i>
                            <span class="text-green-600">Password cocok</span>
                        </div>
                        <div id="passwordMismatch" class="mt-2 text-xs hidden">
                            <i class="fas fa-times-circle mr-1 text-red-500"></i>
                            <span class="text-red-600">Password tidak cocok</span>
                        </div>
                        @error('password_confirmation')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="mb-8">
                    <div class="flex items-start">
                        <input id="terms" 
                               type="checkbox" 
                               name="terms"
                               required
                               class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 mt-1">
                        <label for="terms" class="ml-2 text-gray-700 text-sm">
                            Saya setuju dengan 
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">Syarat & Ketentuan</a> 
                            dan 
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">Kebijakan Privasi</a> 
                            SportWear
                        </label>
                    </div>
                    @error('terms')
                        <div class="mt-2 text-red-600 text-sm flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Tombol Daftar -->
                <button type="submit" class="btn-register w-full py-3.5 px-4 rounded-xl text-white font-semibold shadow-lg mb-6">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftar Sekarang
                </button>
                
                <!-- Link login -->
                <div class="text-center pt-6 border-t border-gray-200">
                    <p class="text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 ml-1">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Bagian kanan: Ilustrasi -->
        <div class="hidden md:flex md:w-2/5 bg-gradient-to-br from-blue-600 to-emerald-800 p-8 flex-col justify-between text-white relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 right-10 w-32 h-32 rounded-full bg-white"></div>
                <div class="absolute bottom-10 left-10 w-40 h-40 rounded-full bg-white"></div>
            </div>
            
            <!-- Brand dan konten -->
            <div class="relative z-10">
                <div class="flex items-center space-x-2 mb-8">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center">
                        <i class="fas fa-running text-white"></i>
                    </div>
                    <span class="text-2xl font-bold">SportWear</span>
                </div>
                
                <h1 class="text-3xl font-bold mb-6">Mulai Perjalanan <br> Olahraga Anda</h1>
                <p class="text-lg opacity-90 mb-6">Bergabunglah dengan komunitas olahraga terbesar dan dapatkan akses ke:</p>
                
                <ul class="space-y-3 mb-10">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-300 mr-3"></i>
                        <span>Diskon hingga 50% untuk member baru</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-300 mr-3"></i>
                        <span>Gratis ongkir untuk pembelian pertama</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-300 mr-3"></i>
                        <span>Akses ke produk eksklusif SportWear</span>
                    </li>
                </ul>
            </div>
            
            <!-- Elemen dekoratif floating -->
            <div class="absolute -bottom-4 -left-4 w-48 h-48 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full opacity-20 floating-animation"></div>
        </div>
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
        
        // Toggle visibility confirm password
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 10;
            
            // Check for lowercase letters
            if (/[a-z]/.test(password)) strength += 20;
            
            // Check for uppercase letters
            if (/[A-Z]/.test(password)) strength += 20;
            
            // Check for numbers
            if (/[0-9]/.test(password)) strength += 20;
            
            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength += 15;
            
            // Update strength bar and text
            strengthBar.style.width = strength + '%';
            
            if (strength < 40) {
                strengthBar.className = 'progress-bar bg-red-500';
                strengthText.textContent = 'Lemah';
            } else if (strength < 70) {
                strengthBar.className = 'progress-bar bg-yellow-500';
                strengthText.textContent = 'Cukup';
            } else if (strength < 90) {
                strengthBar.className = 'progress-bar bg-blue-500';
                strengthText.textContent = 'Baik';
            } else {
                strengthBar.className = 'progress-bar bg-green-500';
                strengthText.textContent = 'Sangat Baik';
            }
            
            // Check password match
            checkPasswordMatch();
        });
        
        // Check password confirmation match
        document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchElement = document.getElementById('passwordMatch');
            const mismatchElement = document.getElementById('passwordMismatch');
            
            if (confirmPassword === '') {
                matchElement.classList.add('hidden');
                mismatchElement.classList.add('hidden');
                return;
            }
            
            if (password === confirmPassword) {
                matchElement.classList.remove('hidden');
                mismatchElement.classList.add('hidden');
            } else {
                matchElement.classList.add('hidden');
                mismatchElement.classList.remove('hidden');
            }
        }
        
        // Animasi input focus
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-indigo-200', 'rounded-xl');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-indigo-200');
            });
        });
        
        // Form validation before submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const terms = document.getElementById('terms').checked;
            
            // Check password match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                document.getElementById('password').focus();
                return;
            }
            
            // Check terms agreement
            if (!terms) {
                e.preventDefault();
                alert('Anda harus menyetujui Syarat & Ketentuan dan Kebijakan Privasi!');
                document.getElementById('terms').focus();
                return;
            }
        });
    </script>
</body>
</html>