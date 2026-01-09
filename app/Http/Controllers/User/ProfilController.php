<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        
        // Get user statistics
        $statistics = [
            'total_transactions' => $user->transaksis()->count(),
            'total_spent' => $user->transaksis()->completed()->sum('total_bayar'),
            'active_rentals' => $user->sewas()->aktif()->count(),
            'completed_rentals' => $user->sewas()->selesai()->count(),
            'total_fines' => $user->dendas()->sum('jumlah_denda'),
            'member_since' => $user->created_at->format('d F Y'),
        ];
        
        // Get recent activities
        $recentActivities = $user->transaksis()
            ->with('detailTransaksis.produk')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('user.profil.edit', compact('user', 'statistics', 'recentActivities'));
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        // Handle avatar upload
        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $avatarPath = $path;
        }
        
        // Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatarPath,
        ]);
        
        // Create notification
        \App\Models\Notifikasi::createNotifikasi(
            $user->id,
            'Profil Diperbarui',
            'Informasi profil Anda berhasil diperbarui.',
            'success'
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user->fresh()
        ]);
    }
    
    public function security()
    {
        $user = auth()->user();
        
        // Get login history (simulated)
        $loginHistory = [
            [
                'device' => 'Chrome on Windows',
                'location' => 'Jakarta, Indonesia',
                'ip' => '192.168.1.1',
                'time' => now()->subHours(2),
                'current' => true
            ],
            [
                'device' => 'Safari on iPhone',
                'location' => 'Bandung, Indonesia',
                'ip' => '192.168.1.2',
                'time' => now()->subDays(1),
                'current' => false
            ],
            [
                'device' => 'Firefox on Mac',
                'location' => 'Surabaya, Indonesia',
                'ip' => '192.168.1.3',
                'time' => now()->subDays(3),
                'current' => false
            ],
        ];
        
        return view('user.profil.security', compact('user', 'loginHistory'));
    }
    
    public function updateSecurity(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        // Create notification
        \App\Models\Notifikasi::createNotifikasi(
            $user->id,
            'Password Diperbarui',
            'Password akun Anda berhasil diperbarui.',
            'success'
        );
        
        // Logout other devices
        \Illuminate\Support\Facades\Auth::logoutOtherDevices($request->current_password);
        
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ]);
    }
    
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        $user = auth()->user();
        
        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Upload new avatar
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');
        
        $user->avatar = $path;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'avatar_url' => $user->avatar_url
        ]);
    }
    
    public function deleteAvatar()
    {
        $user = auth()->user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->avatar = null;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil dihapus.',
            'avatar_url' => $user->avatar_url
        ]);
    }
    
    public function getActivity(Request $request)
    {
        $user = auth()->user();
        
        $activities = $user->transaksis()
            ->with('detailTransaksis.produk')
            ->latest()
            ->paginate(10);
        
        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }
}