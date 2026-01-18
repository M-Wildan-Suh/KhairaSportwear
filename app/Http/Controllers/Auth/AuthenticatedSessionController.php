<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();

    // Notifikasi login
    if (class_exists(\App\Models\Notifikasi::class)) {
        \App\Models\Notifikasi::createNotifikasi(
            $user->id,
            'Login Berhasil',
            'Anda berhasil login ke akun SportWear.',
            'success'
        );
    }

    $request->session()->flash('login_success', 'Login Berhasil!');

    // 🔑 AMBIL RETURN URL DARI MODAL
    $returnUrl = $request->input('return');

    // 🛡️ KEAMANAN: pastikan URL internal
    if ($returnUrl && str_starts_with($returnUrl, url('/'))) {
        return redirect()->to($returnUrl);
    }

    // Role admin
    if ($user->isAdmin()) {
        return redirect()->intended(route('admin.dashboard', false));
    }

    // Default user
    return redirect()->intended(route('user.dashboard', false));
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Kirim notifikasi logout jika user ada
        if ($user && class_exists(\App\Models\Notifikasi::class)) {
            \App\Models\Notifikasi::createNotifikasi(
                $user->id,
                'Logout Berhasil',
                'Anda telah logout dari sistem SportWear.',
                'info'
            );
        }

        // Tambahkan session flash untuk logout
        $request->session()->flash('logout_success', 'Anda telah berhasil logout.');
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}