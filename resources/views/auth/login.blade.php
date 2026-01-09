<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - SportWear</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .login-container {
            background: linear-gradient(135deg, #2B6CB0 0%, #38B2AC 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .login-header {
            background: linear-gradient(135deg, #2B6CB0 0%, #38B2AC 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .login-header h2 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
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
        
        .btn-login {
            background: linear-gradient(135deg, #ED8936 0%, #DD6B20 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(237, 137, 54, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #718096;
        }
        
        .register-link a {
            color: #2B6CB0;
            font-weight: 600;
            text-decoration: none;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Selamat Datang Kembali</h2>
                <p>Masuk untuk melanjutkan ke SportWear</p>
            </div>
            
            <div class="login-body">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                        @error('email')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-forgot">
                        <div class="form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label for="remember_me" class="form-check-label">Ingat saya</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-primary">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login">
                        Masuk
                    </button>

                    <div class="register-link">
                        Belum punya akun? 
                        <a href="{{ route('register') }}">
                            Daftar di sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>