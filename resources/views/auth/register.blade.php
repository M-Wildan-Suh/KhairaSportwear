<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - SportWear</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .register-container {
            background: linear-gradient(135deg, #2B6CB0 0%, #38B2AC 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .register-header {
            background: linear-gradient(135deg, #2B6CB0 0%, #38B2AC 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .register-header h2 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2D3748;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2B6CB0;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #ED8936 0%, #DD6B20 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(237, 137, 54, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #718096;
        }
        
        .login-link a {
            color: #2B6CB0;
            font-weight: 600;
            text-decoration: none;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h2>Bergabung dengan SportWear</h2>
                <p>Mulai perjalanan olahraga Anda bersama kami</p>
            </div>
            
            <div class="register-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input id="phone" class="form-control" type="text" name="phone" value="{{ old('phone') }}" required autocomplete="tel">
                        @error('phone')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea id="address" class="form-control" name="address" rows="3" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                        @error('password_confirmation')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-register">
                        Daftar Sekarang
                    </button>

                    <div class="login-link">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}">
                            Masuk di sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>