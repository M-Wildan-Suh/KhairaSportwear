<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SportWear - Platform Alat Olahraga')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- Simple User Layout for Now -->
    <div class="container py-4">
        <div class="text-center mb-4">
            <h2 class="text-primary">User Dashboard</h2>
            <p>Layout user akan dibuat lengkap di fase berikutnya</p>
            
            <div class="d-flex justify-content-center gap-2 mb-4">
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Dashboard</a>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">Home</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
        
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>