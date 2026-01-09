<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Sewa;
use App\Models\Denda;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==================== QUICK STATS ====================
        
        // Total Revenue - DARI TRANSAKSI SELESAI/DIBAYAR
        $totalRevenue = Transaksi::whereIn('status', ['selesai', 'dibayar'])
            ->sum('total_bayar');
            
        // Revenue change from last month
        $currentMonthRevenue = Transaksi::whereIn('status', ['selesai', 'dibayar'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_bayar');
            
        $lastMonthRevenue = Transaksi::whereIn('status', ['selesai', 'dibayar'])
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('total_bayar');
            
        $revenueChange = $lastMonthRevenue > 0 
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($currentMonthRevenue > 0 ? 100 : 0);
        
        // Total Users
        $totalUsers = User::where('role', 'user')->count();
        
        // User change from last month
        $currentMonthUsers = User::where('role', 'user')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
            
        $lastMonthUsers = User::where('role', 'user')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();
            
        $userChange = $lastMonthUsers > 0
            ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1)
            : ($currentMonthUsers > 0 ? 100 : 0);
        
        
        // Total Products
        $totalProducts = Produk::count();
        $availableProducts = Produk::where('stok_tersedia', '>', 0)->count();
        
        // Product categories count
        $sportwearCount = Produk::whereHas('kategori', function($query) {
            $query->where('tipe', 'sportwear');
        })->count();
        
        $rentalCount = Produk::whereHas('kategori', function($query) {
            $query->where('tipe', 'sewa');
        })->count();
        
        // Today's activity
        $today = Carbon::today();
        
        $todaySalesCount = Transaksi::whereDate('created_at', $today)
            ->where('tipe', 'penjualan')
            ->whereIn('status', ['selesai', 'dibayar'])
            ->count();
            
        $todaySalesAmount = Transaksi::whereDate('created_at', $today)
            ->where('tipe', 'penjualan')
            ->whereIn('status', ['selesai', 'dibayar'])
            ->sum('total_bayar');
            
        $todayRentalsCount = Sewa::whereDate('created_at', $today)->count();
        
        $todayRentalsAmount = Sewa::whereDate('created_at', $today)->sum('total_harga');
        
        $pendingTransactions = Transaksi::where('status', 'pending')->count();
        $activeRentalsCount = Sewa::where('status', 'aktif')->count();
        
        // ==================== CHARTS DATA ====================
        
        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $sales = Transaksi::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('tipe', 'penjualan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->sum('total_bayar');
                
            $rentals = Transaksi::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('tipe', 'penyewaan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->sum('total_bayar');
                
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'sales' => $sales,
                'rentals' => $rentals,
                'total' => $sales + $rentals
            ];
        }
        
        // Transaction distribution percentages
        $totalTransactions = Transaksi::count();
        $salesCount = Transaksi::where('tipe', 'penjualan')->count();
        $rentalsCount = Transaksi::where('tipe', 'penyewaan')->count();
        $successCount = Transaksi::whereIn('status', ['selesai', 'dibayar'])->count();
        
        $salesPercentage = $totalTransactions > 0 
            ? round(($salesCount / $totalTransactions) * 100)
            : 0;
            
        $rentalsPercentage = $totalTransactions > 0 
            ? round(($rentalsCount / $totalTransactions) * 100)
            : 0;
            
        $successPercentage = $totalTransactions > 0 
            ? round(($successCount / $totalTransactions) * 100)
            : 0;
        
        // ==================== RECENT DATA ====================
        
        // Recent transactions with pagination
        $recentTransactions = Transaksi::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
            
        // Low stock products
        $lowStockProducts = Produk::with('kategori')
            ->where('stok_tersedia', '<', 5)
            ->orderBy('stok_tersedia')
            ->take(10)
            ->get();
            
        // Active rentals with return date calculation
        $activeRentals = Sewa::with(['user', 'produk'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_kembali_rencana')
            ->take(10)
            ->get();
            
        // Unpaid fines
        $unpaidFines = Denda::with(['user', 'pengembalian.sewa'])
            ->where('status_pembayaran', 'belum_dibayar')
            ->orderBy('tanggal_jatuh_tempo')
            ->take(10)
            ->get();
            
        // Sewa with fines
        $sewaWithFines = Sewa::with(['user', 'produk'])
            ->where('denda', '>', 0)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
            
        // ==================== RECENT ACTIVITIES ====================
        
        // Generate recent activities
        $recentActivities = $this->getRecentActivities();
        
        return view('admin.dashboard.index', compact(
            // Quick Stats
            'totalRevenue',
            'revenueChange',
            'totalUsers', 
            'userChange',
            'totalProducts',
            'availableProducts',
            'sportwearCount',
            'rentalCount',
            'todaySalesCount',
            'todaySalesAmount',
            'todayRentalsCount',
            'todayRentalsAmount',
            'pendingTransactions',
            'activeRentalsCount',
            
            // Charts Data
            'monthlyRevenue',
            'salesPercentage',
            'rentalsPercentage', 
            'successPercentage',
            
            // Recent Data
            'recentTransactions',
            'lowStockProducts',
            'activeRentals',
            'unpaidFines',
            'sewaWithFines',
            
            // Activities
            'recentActivities'
        ));
    }
    
    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent transactions
        $recentTransactions = Transaksi::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($recentTransactions as $transaction) {
            $activities[] = [
                'user' => $transaction->user->name,
                'action' => 'melakukan transaksi ' . ($transaction->tipe == 'penjualan' ? 'penjualan' : 'penyewaan'),
                'icon' => $transaction->tipe == 'penjualan' ? 'fas fa-shopping-cart' : 'fas fa-calendar-alt',
                'color' => $transaction->tipe == 'penjualan' ? 'bg-blue-500' : 'bg-purple-500',
                'time' => $transaction->created_at->diffForHumans(),
                'badge' => 'Rp ' . number_format($transaction->total_bayar, 0, ',', '.'),
                'badgeColor' => 'bg-gray-100 text-gray-800'
            ];
        }
        
        // Recent user registrations
        $recentUsers = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        foreach ($recentUsers as $user) {
            $activities[] = [
                'user' => 'System',
                'action' => 'mendaftarkan user baru: ' . $user->name,
                'icon' => 'fas fa-user-plus',
                'color' => 'bg-green-500',
                'time' => $user->created_at->diffForHumans(),
                'badge' => 'New',
                'badgeColor' => 'bg-green-100 text-green-800'
            ];
        }
        
        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 8);
    }
    
    /**
     * API endpoint for chart data
     */
    public function chartData(Request $request)
    {
        $period = $request->get('period', 'month');
        
        if ($period == 'week') {
            $data = $this->getWeeklyData();
        } else {
            $data = $this->getMonthlyData();
        }
        
        return response()->json($data);
    }
    
    /**
     * Get weekly data for chart
     */
    private function getWeeklyData()
    {
        $data = [
            'labels' => [],
            'sales' => [],
            'rentals' => []
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $sales = Transaksi::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('tipe', 'penjualan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->sum('total_bayar');
                
            $rentals = Transaksi::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('tipe', 'penyewaan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->sum('total_bayar');
            
            $data['labels'][] = $date->format('D');
            $data['sales'][] = $sales;
            $data['rentals'][] = $rentals;
        }
        
        return $data;
    }
    
    /**
     * Get monthly data for chart
     */
    private function getMonthlyData()
    {
        $data = [
            'labels' => [],
            'sales' => [],
            'rentals' => []
        ];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $sales = Transaksi::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('tipe', 'penjualan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->sum('total_bayar');
                
            $rentals = Transaksi::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('tipe', 'penyewaan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->sum('total_bayar');
            
            $data['labels'][] = $month->format('M');
            $data['sales'][] = $sales;
            $data['rentals'][] = $rentals;
        }
        
        return $data;
    }
    
    /**
     * API endpoint for quick stats
     */
    public function quickStats()
    {
        $today = Carbon::today();
        
        $stats = [
            'totalRevenue' => 'Rp ' . number_format(Transaksi::whereIn('status', ['selesai', 'dibayar'])->sum('total_bayar'), 0, ',', '.'),
            'todaySales' => number_format(Transaksi::whereDate('created_at', $today)
                ->where('tipe', 'penjualan')
                ->whereIn('status', ['selesai', 'dibayar'])
                ->count(), 0, ',', '.'),
            'todayRentals' => number_format(Sewa::whereDate('created_at', $today)->count(), 0, ',', '.'),
            'pendingTransactions' => number_format(Transaksi::where('status', 'pending')->count(), 0, ',', '.'),
            'activeRentals' => number_format(Sewa::where('status', 'aktif')->count(), 0, ',', '.'),
            'lowStockProducts' => number_format(Produk::where('stok_tersedia', '<', 5)->count(), 0, ',', '.'),
            'unpaidFines' => number_format(Denda::where('status_pembayaran', 'belum_dibayar')->count(), 0, ',', '.')
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Export dashboard data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf');
        
        // Generate export data
        $data = [
            'totalRevenue' => Transaksi::whereIn('status', ['selesai', 'dibayar'])->sum('total_bayar'),
            'totalUsers' => User::where('role', 'user')->count(),
            'totalProducts' => Produk::count(),
            'totalTransactions' => Transaksi::count(),
            'monthlyRevenue' => $this->getMonthlyData()
        ];
        
        if ($type == 'excel') {
            return $this->exportExcel($data);
        }
        
        return $this->exportPDF($data);
    }
    
    private function exportPDF($data)
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export feature coming soon']);
    }
    
    private function exportExcel($data)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export feature coming soon']);
    }
}