<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Sewa;
use App\Models\Keranjang;
use App\Models\Notifikasi;
use App\Models\Pengembalian;
use Carbon\Carbon;
use App\Services\SewaService;
use App\Services\ReportService;
use App\Services\PengembalianService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // User statistics (ORIGINAL CODE - TETAP ADA)
        $totalTransactions = $user->transaksis()->count();
        $totalSpent = $user->transaksis()->selesai()->sum('total_bayar');
        $activeRentals = $user->sewas()->aktif()->count();
        $cartCount = $user->keranjangs()->count();
        
        // NEW: Dashboard statistics from ReportService
        $dashboardStats = ReportService::getDashboardStats(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );
        
        // NEW: Filter options
        $filter = [
            'month' => $request->get('month', Carbon::now()->month),
            'year' => $request->get('year', Carbon::now()->year)
        ];
        
        // NEW: Monthly stats for charts
        $monthlyStats = $this->getMonthlyStats($user->id, $filter['year']);
        
        // Recent transactions (ORIGINAL)
        $recentTransactions = $user->transaksis()
            ->with('detailTransaksis.produk')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Active rentals (ORIGINAL)
        $activeRentalsList = $user->sewas()
            ->with('produk')
            ->aktif()
            ->orderBy('tanggal_kembali_rencana')
            ->take(5)
            ->get();
            
        // Upcoming rental returns (ORIGINAL)
        $upcomingReturns = $user->sewas()
            ->with('produk')
            ->aktif()
            ->whereDate('tanggal_kembali_rencana', '>=', Carbon::today())
            ->orderBy('tanggal_kembali_rencana')
            ->take(5)
            ->get();
            
        // NEW: Late rentals
        $lateRentals = SewaService::getSewaTerlambat()
            ->where('user_id', $user->id)
            ->take(5)
            ->get();
            
        // NEW: Pending returns
        $pendingReturns = $user->sewas()
            ->where('status', Sewa::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN)
            ->with('produk')
            ->take(5)
            ->get();
            
        // NEW: Recent pengembalian
        $recentPengembalian = $user->sewas()
            ->whereHas('pengembalian')
            ->with(['produk', 'pengembalian'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
            
        // Recent notifications (ORIGINAL)
        $notifications = $user->notifikasis()
            ->terbaru()
            ->take(10)
            ->get();
            
        // Cart items (ORIGINAL)
        $cartItems = $user->keranjangs()
            ->with('produk')
            ->get();
            
        // NEW: Quick actions availability
        $quickActions = [
            'can_checkout' => $cartCount > 0,
            'has_active_rentals' => $activeRentals > 0,
            'has_pending_returns' => $pendingReturns->count() > 0,
            'has_late_rentals' => $lateRentals->count() > 0
        ];
        
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
            'cartItems',
            // NEW variables
            'dashboardStats',
            'monthlyStats',
            'lateRentals',
            'pendingReturns',
            'recentPengembalian',
            'quickActions',
            'filter'
        ));
    }
    
    /**
     * NEW: Get monthly statistics for user
     */
    private function getMonthlyStats($userId, $year)
    {
        return [
            'rentals' => $this->getMonthlyRentals($userId, $year),
            'spending' => $this->getMonthlySpending($userId, $year),
            'returns' => $this->getMonthlyReturns($userId, $year)
        ];
    }
    
    private function getMonthlyRentals($userId, $year)
    {
        return Sewa::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
    }
    
    private function getMonthlySpending($userId, $year)
    {
        return Transaksi::selectRaw('MONTH(created_at) as month, SUM(total_bayar) as total')
            ->where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->where('status', 'selesai')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }
    
    private function getMonthlyReturns($userId, $year)
    {
        return Pengembalian::selectRaw('MONTH(tanggal_kembali) as month, COUNT(*) as count')
            ->whereHas('sewa', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereYear('tanggal_kembali', $year)
            ->groupByRaw('MONTH(tanggal_kembali)')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
    }
    
    /**
     * NEW: API endpoint for dashboard charts
     */
    public function getChartData(Request $request)
    {
        $user = auth()->user();
        $year = $request->get('year', Carbon::now()->year);
        
        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'datasets' => [
                [
                    'label' => 'Total Sewa',
                    'data' => array_values($this->getMonthlyRentals($user->id, $year)),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Total Pengeluaran',
                    'data' => array_values($this->getMonthlySpending($user->id, $year)),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        return response()->json($data);
    }
    
    /**
     * NEW: Get dashboard summary for AJAX
     */
    public function getSummary()
    {
        $user = auth()->user();
        
        return response()->json([
            'total_transactions' => $user->transaksis()->count(),
            'active_rentals' => $user->sewas()->aktif()->count(),
            'pending_returns' => $user->sewas()
                ->where('status', Sewa::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN)
                ->count(),
            'cart_count' => $user->keranjangs()->count(),
            'notifications_count' => $user->notifikasis()->where('dibaca', false)->count()
        ]);
    }
    
    /**
     * NEW: Quick actions
     */
    public function quickAction(Request $request)
    {
        $action = $request->get('action');
        $user = auth()->user();
        
        switch ($action) {
            case 'checkout':
                return redirect()->route('user.checkout.index');
                
            case 'view_rentals':
                return redirect()->route('user.sewa.index');
                
            case 'view_returns':
                return redirect()->route('user.pengembalian.index');
                
            case 'add_rental':
                return redirect()->route('produk.index', ['tipe' => 'sewa']);
                
            default:
                return redirect()->back();
        }
    }

    public function getStats(Request $request)
    {
        $user = auth()->user();
        
        $data = [
            'active_rentals' => $user->sewas()->aktif()->count(),
            'cart_count' => $user->keranjangs()->count(),
            'unread_notifications' => $user->notifikasis()->where('dibaca', false)->count(),
            'late_rentals' => $user->sewas()
                ->aktif()
                ->whereDate('tanggal_kembali_rencana', '<', Carbon::today())
                ->count(),
        ];
        
        return response()->json($data);
    }
}