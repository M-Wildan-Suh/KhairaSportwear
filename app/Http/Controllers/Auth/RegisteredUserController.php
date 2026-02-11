<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'user',
            'is_active' => true // 🔥 default belum aktif
        ]);

        event(new Registered($user));

        // =============================
        // BUAT LINK AKTIVASI YA / TIDAK
        // =============================
        $yesLink = URL::temporarySignedRoute(
            'aktivasi.ya',
            now()->addHours(24),
            ['id' => $user->id]
        );

        $noLink = URL::temporarySignedRoute(
            'aktivasi.tidak',
            now()->addHours(24),
            ['id' => $user->id]
        );

        // =============================
        // KIRIM EMAIL
        // =============================
        Mail::send('emails.aktivasi', compact('user', 'yesLink', 'noLink'), function ($m) use ($user) {
            $m->to($user->email)->subject('Aktifkan Akun SportWear');
        });

        // notif welcome (opsional)
        \App\Models\Notifikasi::createNotifikasi(
            $user->id,
            'Pendaftaran Berhasil',
            'Silakan cek email untuk aktivasi akun Anda.',
            'info'
        );

        return redirect(route('login'))
            ->with('success', 'Registrasi berhasil! Cek email untuk aktivasi akun.');
    }
}
