<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Sewa;
use App\Models\Keranjang;
use App\Models\Notifikasi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // User statistics
        $totalTransactions = $user->transaksis()->count();
        $totalSpent = $user->transaksis()->selesai()->sum('total_bayar');
        $activeRentals = $user->sewas()->aktif()->count();
        $cartCount = $user->keranjangs()->count();
        
        // Recent transactions
        $recentTransactions = $user->transaksis()
            ->with('detailTransaksis.produk')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Active rentals
        $activeRentalsList = $user->sewas()
            ->with('produk')
            ->aktif()
            ->orderBy('tanggal_kembali_rencana')
            ->take(5)
            ->get();
            
        // Upcoming rental returns
        $upcomingReturns = $user->sewas()
            ->with('produk')
            ->aktif()
            ->whereDate('tanggal_kembali_rencana', '>=', Carbon::today())
            ->orderBy('tanggal_kembali_rencana')
            ->take(5)
            ->get();
            
        // Recent notifications
        $notifications = $user->notifikasis()
            ->terbaru()
            ->take(10)
            ->get();
            
        // Cart items
        $cartItems = $user->keranjangs()
            ->with('produk')
            ->get();
        
        return view('user.dashboard.index', compact(
            'user',
            'totalTransactions',
            'totalSpent',
            'activeRentals',
            'cartCount',
            'recentTransactions',
            'activeRentalsList',
            'upcomingReturns',
            'notifications',
            'cartItems'
        ));
    }
}