<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Sewa;
use App\Models\Denda;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::where('role', 'user')->count();
        $totalProducts = Produk::count();
        
        // Today's statistics
        $today = Carbon::today();
        $todaySales = Transaksi::whereDate('created_at', $today)
            ->where('tipe', 'penjualan')
            ->where('status', 'selesai')
            ->sum('total_bayar');
            
        $todayRentals = Sewa::whereDate('created_at', $today)->count();
        
        // Recent transactions
        $recentTransactions = Transaksi::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Active rentals
        $activeRentals = Sewa::with(['user', 'produk'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_kembali_rencana')
            ->take(10)
            ->get();
            
        // Unpaid fines
        $unpaidFines = Denda::with('user')
            ->where('status_pembayaran', 'belum_dibayar')
            ->orderBy('tanggal_jatuh_tempo')
            ->take(10)
            ->get();
            
        // Monthly revenue data for chart (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $sales = Transaksi::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('tipe', 'penjualan')
                ->where('status', 'selesai')
                ->sum('total_bayar');
                
            $rentals = Sewa::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_harga');
                
            $fines = Denda::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status_pembayaran', 'lunas')
                ->sum('jumlah_denda');
                
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'sales' => $sales,
                'rentals' => $rentals,
                'fines' => $fines,
                'total' => $sales + $rentals + $fines
            ];
        }
        
        // Low stock products
        $lowStockProducts = Produk::where('stok_tersedia', '<', 5)
            ->orderBy('stok_tersedia')
            ->take(10)
            ->get();
        
        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalProducts',
            'todaySales',
            'todayRentals',
            'recentTransactions',
            'activeRentals',
            'unpaidFines',
            'monthlyRevenue',
            'lowStockProducts'
        ));
    }
}