<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Sewa;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Denda;
use App\Models\DetailTransaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman index laporan
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $totalLaporan = Transaksi::all()->count();

        $salesThisMonth = Transaksi::where('tipe', 'penjualan')
            ->where('status', 'selesai')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_bayar');

        $rentalsThisMonth = Sewa::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $unpaidFinesTotal = Denda::where('status_pembayaran', 'belum_bayar')
            ->sum('jumlah_denda');

        $todaySales = Transaksi::where('tipe', 'penjualan')
            ->where('status', 'selesai')
            ->whereDate('created_at', today())
            ->sum('total_bayar');

        $weekSales = Transaksi::where('tipe', 'penjualan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_bayar');

        $activeRentals = Sewa::where('status', 'aktif')->count();
        $overdueRentals = Sewa::where('status', 'terlambat')->count();

        $kategoris = Kategori::all();

        $laporansQuery = Laporan::with('pembuat')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $laporansQuery->where('kode_laporan', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")
                ->orWhere('periode', 'like', "%{$search}%");
        }

        $laporans = $laporansQuery->paginate(10);

        $defaultStartDate = now()->subDays(30)->format('Y-m-d');
        $defaultEndDate = now()->format('Y-m-d');

        return view('admin.laporan.index', compact(
            'totalLaporan',
            'salesThisMonth',
            'rentalsThisMonth',
            'unpaidFinesTotal',
            'todaySales',
            'weekSales',
            'activeRentals',
            'overdueRentals',
            'kategoris',
            'laporans',
            'defaultStartDate',
            'defaultEndDate',
            'search'
        ));
    }

    /**
     * Generate laporan penjualan
     */
    public function penjualan(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'tipe' => 'nullable|in:jual,sewa',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        $query = Transaksi::with(['user', 'items.produk.kategori'])
            ->where('tipe', 'penjualan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('kategori_id')) {
            $query->whereHas('items.produk', function ($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        $transactions = $query->get();

        $summary = $this->calculateSalesSummary($transactions);

        $topProducts = $this->getTopProducts($startDate, $endDate, 'penjualan', $request->kategori_id);

        $kategoris = Kategori::all();

        if ($request->has('download') && $request->download === 'pdf') {
            return $this->generateSalesPdf($startDate, $endDate, $request, $transactions, $summary, $topProducts);
        }

        return view('admin.laporan.penjualan', compact(
            'transactions',
            'summary',
            'topProducts',
            'kategoris',
            'startDate',
            'endDate',
            'request'
        ));
    }

/**
 * Generate laporan penyewaan
 */
/**
 * Generate laporan penyewaan
 */
public function penyewaan(Request $request)
{
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'nullable|in:all,menunggu_pembayaran,menunggu_konfirmasi,aktif,selesai,dibatalkan,expired,menunggu_verifikasi_pengembalian,terlambat',
        'download' => 'nullable|in:pdf,excel',
    ]);

    // Set tanggal default - 30 hari terakhir
    $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
    $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

    // Query utama
    $query = Sewa::with(['user', 'produk.kategori', 'transaksi', 'pengembalian'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    // Filter status
    if ($request->filled('status') && $request->status !== 'all') {
        if ($request->status === 'terlambat') {
            // Sewa aktif yang sudah melewati tanggal kembali rencana
            $query->where('status', Sewa::STATUS_AKTIF)
                ->where('tanggal_kembali_rencana', '<', now());
        } else {
            $query->where('status', $request->status);
        }
    }

    $sewas = $query->orderBy('created_at', 'desc')->paginate(20);

    $topProducts = $this->getTopProducts($startDate, $endDate, 'penjualan', $request->kategori_id);
    
    // Hitung semua statistik
    $summary = $this->calculateRentalSummary($sewas);
    
    // Data untuk chart
    $dailyRentals = $this->getDailyRentalData($sewas);
    
    // Produk paling banyak disewa
    $mostRentedProducts = $this->getMostRentedProducts($sewas);
    
    // Analisis status
    $statusAnalysis = $this->getStatusAnalysis($startDate, $endDate);
    
    // Rentals terlambat
    $overdueRentals = Sewa::where('status', Sewa::STATUS_AKTIF)
        ->where('tanggal_kembali_rencana', '<', now())
        ->count();
    
    $overdueRentalitem = Sewa::where('status', Sewa::STATUS_AKTIF)
        ->where('tanggal_kembali_rencana', '<', now())
        ->get();
    
    // Rentals akan berakhir (3 hari ke depan)
    $upcomingReturns = Sewa::where('status', Sewa::STATUS_AKTIF)
        ->whereBetween('tanggal_kembali_rencana', [now(), now()->addDays(3)])
        ->count();

    // Variabel untuk view lama (backward compatibility)
    $totalRentalRevenue = DetailTransaksi::where('tipe_produk', 'sewa')->get()->sum('subtotal') ?? 0;
    $totalRentals = DetailTransaksi::where('tipe_produk', 'sewa')->get()->count() ?? 0;
    $totalItemsRented = $totalRentals; // Sama dengan total rentals karena 1 sewa = 1 item
    $averageRentalDuration = $summary['durasi_rata_rata'] ?? 0;
    
    // Status counts untuk view lama
    $statusCounts = [
        'ongoing' => $sewas->where('status', Sewa::STATUS_AKTIF)->count(),
        'completed' => $sewas->where('status', Sewa::STATUS_SELESAI)->count(),
        'overdue' => $sewas->where('status', Sewa::STATUS_AKTIF)
            ->where('tanggal_kembali_rencana', '<', now())
            ->count()
    ];
    
    // Export PDF
    if ($request->has('download') && $request->download === 'pdf') {
        return $this->generateRentalPdf($sewas, $startDate, $endDate, $summary, $topProducts);
    }

    // Export Excel
    if ($request->has('download') && $request->download === 'excel') {
        return $this->exportRentalExcel($sewas, $startDate, $endDate, $summary);
    }

    return view('admin.laporan.penyewaan', compact(
        'sewas',
        'summary',
        'startDate',
        'endDate',
        'request',
        'dailyRentals',
        'mostRentedProducts',
        'statusAnalysis',
        'overdueRentals',
        'overdueRentalitem',
        'upcomingReturns',
        // Variabel untuk view lama
        'totalRentalRevenue',
        'totalRentals',
        'totalItemsRented',
        'averageRentalDuration',
        'statusCounts'
    ));
}

/**
 * Analisis status
 */
private function getStatusAnalysis($startDate, $endDate)
{
    $statuses = Sewa::whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('status, COUNT(*) as count, SUM(total_harga) as revenue')
        ->groupBy('status')
        ->get();
    
    $statusMap = [
        Sewa::STATUS_MENUNGGU_PEMBAYARAN => [
            'label' => 'Menunggu Pembayaran',
            'color' => 'warning',
            'icon' => 'clock',
        ],
        Sewa::STATUS_MENUNGGU_KONFIRMASI => [
            'label' => 'Menunggu Konfirmasi',
            'color' => 'info',
            'icon' => 'check-circle',
        ],
        Sewa::STATUS_AKTIF => [
            'label' => 'Aktif',
            'color' => 'success',
            'icon' => 'play-circle',
        ],
        Sewa::STATUS_SELESAI => [
            'label' => 'Selesai',
            'color' => 'primary',
            'icon' => 'check-circle',
        ],
        Sewa::STATUS_DIBATALKAN => [
            'label' => 'Dibatalkan',
            'color' => 'danger',
            'icon' => 'x-circle',
        ],
        Sewa::STATUS_EXPIRED => [
            'label' => 'Expired',
            'color' => 'secondary',
            'icon' => 'clock',
        ],
        Sewa::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN => [
            'label' => 'Menunggu Verifikasi',
            'color' => 'purple',
            'icon' => 'shield-check',
        ],
    ];
    
    return $statuses->map(function ($item) use ($statusMap) {
        $info = $statusMap[$item->status] ?? [
            'label' => ucfirst(str_replace('_', ' ', $item->status)),
            'color' => 'secondary',
            'icon' => 'circle',
        ];
        
        return [
            'status' => $item->status,
            'label' => $info['label'],
            'color' => $info['color'],
            'icon' => $info['icon'],
            'count' => $item->count,
            'revenue' => $item->revenue,
            'percentage' => $item->count, // Akan dihitung di view
        ];
    });
}


    /**
     * Download sales PDF
     */
    public function downloadSalesPdf(Request $request)
    {
        $request->merge(['download' => 'pdf']);
        return $this->penjualan($request);
    }

    /**
     * Download rental PDF
     */
    public function downloadRentalPdf(Request $request)
    {
        $request->merge(['download' => 'pdf']);
        return $this->penyewaan($request);
    }

    /**
     * Display invoice
     */
    public function invoice($id)
    {
        $transaksi = Transaksi::with(['user', 'items.produk'])->find($id);

        if ($transaksi) {
            return view('admin.laporan.invoice', [
                'type' => 'transaksi',
                'data' => $transaksi
            ]);
        }

        $sewa = Sewa::with(['user', 'produk'])->find($id);

        if ($sewa) {
            return view('admin.laporan.invoice', [
                'type' => 'sewa',
                'data' => $sewa
            ]);
        }

        abort(404, 'Invoice tidak ditemukan');
    }

    public function printInvoice($id)
    {
        $transaksi = Transaksi::with(['user', 'items.produk'])->find($id);

        if (!$transaksi) {
            abort(404, 'Invoice tidak ditemukan');
        }

        return view('admin.laporan.print-invoice', compact('transaksi'));
    }

    public function downloadInvoicePdf($id)
    {
        $transaksi = Transaksi::with(['user', 'items.produk'])->find($id);

        if (!$transaksi) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $pdf = Pdf::loadView('admin.laporan.invoice-pdf', compact('transaksi'));

        $filename = 'invoice_' . $transaksi->kode_transaksi . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel($type)
    {
        return back()->with('info', 'Export Excel untuk ' . $type . ' akan segera tersedia');
    }

    /**
 * Get most rented products with detailed info
 */
private function getMostRentedProducts($sewas)
{
    $grouped = $sewas->groupBy('produk_id')->map(function ($rentals, $produkId) {
        $produk = $rentals->first()->produk;
        return (object) [
            'id' => $produk->id,
            'nama' => $produk->nama,
            'gambar' => $produk->gambar,
            'harga_sewa_harian' => $produk->harga_sewa,
            'kategori' => $produk->kategori,
            'stok_tersedia' => $produk->stok,
            'rental_count' => $rentals->count(),
            'total_duration' => $rentals->sum('jumlah_hari'),
            'total_revenue' => $rentals->sum('total_harga')
        ];
    });
    
    return $grouped->sortByDesc('rental_count')->take(10);
}

/**
 * Get daily rental data for chart
 */
private function getDailyRentalData($sewas)
{
    $dailyData = $sewas->groupBy(function ($sewa) {
        return $sewa->tanggal_mulai->format('Y-m-d');
    })->map(function ($rentals, $date) {
        return [
            'date' => Carbon::parse($date)->format('d M'),
            'count' => $rentals->count(),
            'revenue' => $rentals->sum('total_harga')
        ];
    });
    
    return $dailyData->values();
}

/**
 * Calculate rental duration analysis
 */
private function calculateDurationAnalysis($sewas)
{
    if ($sewas->isEmpty()) {
        return [
            'min_duration' => 0,
            'max_duration' => 0,
            'average_duration' => 0,
            'most_common_duration' => 0
        ];
    }
    
    $durations = $sewas->pluck('jumlah_hari');
    
    return [
        'min_duration' => $durations->min(),
        'max_duration' => $durations->max(),
        'average_duration' => $durations->avg(),
        'most_common_duration' => $this->calculateMode($durations)
    ];
}

/**
 * Calculate financial analysis
 */
private function calculateFinancialAnalysis($sewas)
{
    if ($sewas->isEmpty()) {
        return [
            'revenue_per_day' => 0,
            'average_rental_value' => 0,
            'total_penalties' => 0,
            'on_time_return_rate' => 0
        ];
    }
    
    $totalRevenue = $sewas->sum('total_harga');
    $totalDays = $sewas->sum('jumlah_hari');
    $totalRentals = $sewas->count();
    
    // Hitung denda
    $totalPenalties = $sewas->sum('denda');
    
    // Hitung tingkat pengembalian tepat waktu
    $onTimeReturns = $sewas->where('status', 'selesai')
        ->filter(function ($sewa) {
            return Carbon::parse($sewa->tanggal_kembali_aktual)->lte(
                Carbon::parse($sewa->tanggal_kembali_rencana)
            );
        })->count();
    
    $completedRentals = $sewas->where('status', 'selesai')->count();
    $onTimeReturnRate = $completedRentals > 0 ? ($onTimeReturns / $completedRentals) * 100 : 0;
    
    return [
        'revenue_per_day' => $totalDays > 0 ? $totalRevenue / $totalDays : 0,
        'average_rental_value' => $totalRentals > 0 ? $totalRevenue / $totalRentals : 0,
        'total_penalties' => $totalPenalties,
        'on_time_return_rate' => $onTimeReturnRate
    ];
}

/**
 * Calculate mode (most frequent value)
 */
private function calculateMode($values)
{
    $counts = $values->countBy();
    return $counts->sortDesc()->keys()->first() ?? 0;
}

    /**
     * =========================
     * PRIVATE HELPERS
     * =========================
     */

    private function calculateSalesSummary($transactions)
    {
        $totalAmount = $transactions->sum('total_bayar');
        $totalTransactions = $transactions->count();
        $totalItems = $transactions->sum(function ($transaction) {
            return $transaction->items->sum('quantity');
        });

        $averageTransaction = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;

        $paymentMethods = $transactions->groupBy('metode_pembayaran')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_bayar')
                ];
            });

        $dailySales = $transactions->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum('total_bayar');
        });

        return [
            'total_amount' => $totalAmount,
            'total_transactions' => $totalTransactions,
            'total_items' => $totalItems,
            'average_transaction' => $averageTransaction,
            'payment_methods' => $paymentMethods,
            'daily_sales' => $dailySales,
        ];
    }

    private function calculateRentalSummary($sewas)
{
    $totalRevenue = $sewas->sum('total_harga');
    $totalRentals = $sewas->count();
    
    $statusCount = $sewas->groupBy('status')
        ->map(function ($group) {
            return $group->count();
        });
    
    $totalDays = $sewas->sum('jumlah_hari');
    $averageDuration = $totalRentals > 0 ? $totalDays / $totalRentals : 0;
    
    $dailyRentals = $sewas->groupBy(function ($item) {
        return Carbon::parse($item->tanggal_mulai)->format('Y-m-d');
    })->map(function ($group) {
        return $group->count();
    });
    
    return [
        'total_amount' => $totalRevenue,
        'total_rentals' => $totalRentals,
        'status_count' => $statusCount,
        'total_days' => $totalDays,
        'average_duration' => $averageDuration,
        'daily_rentals' => $dailyRentals,
    ];
}

    private function getTopProducts($startDate, $endDate, $type, $kategoriId = null)
{
    if ($type === 'penjualan') {
        $query = DB::table('detail_transaksis')
            ->join('transaksis', 'detail_transaksis.transaksi_id', '=', 'transaksis.id')
            ->join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
            ->where('transaksis.tipe', 'penjualan')
            ->where('transaksis.status', 'selesai')
            ->whereBetween('transaksis.created_at', [$startDate, $endDate]);

        if ($kategoriId) {
            $query->where('produks.kategori_id', $kategoriId);
        }

        // PERBAIKAN DI SINI: ganti 'jumlah' dengan 'quantity'
        $query->select('produks.id', 'produks.nama', DB::raw('SUM(detail_transaksis.quantity) as total_terjual'))
            ->groupBy('produks.id', 'produks.nama')
            ->orderByDesc('total_terjual')
            ->limit(10);
    } else {
        $query = DB::table('sewas')
            ->join('produks', 'sewas.produk_id', '=', 'produks.id')
            ->whereBetween('sewas.tanggal_mulai', [$startDate, $endDate])
            ->select('produks.id', 'produks.nama', DB::raw('COUNT(sewas.id) as total_disewa'))
            ->groupBy('produks.id', 'produks.nama')
            ->orderByDesc('total_disewa')
            ->limit(10);
    }

    return $query->get();
    }

    private function generateSalesPdf($startDate, $endDate, $request, $transactions, $summary, $topProducts)
    {
        $data = [
            'transactions' => $transactions,
            'summary' => $summary,
            'topProducts' => $topProducts,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => $request->all(),
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf.penjualan', $data);

        $filename = 'laporan_penjualan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    private function generaterentalPdf($sewas, $startDate, $endDate, $summary, $topProducts)
    {
        $data = [
            'sewas' => $sewas,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'topProducts' => $topProducts,

        ];

        $pdf = Pdf::loadView('admin.laporan.pdf.penyewaan', $data);

        $filename = 'laporan_penyewaan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    private function saveLaporanToDatabase($tipe, $data, $startDate, $endDate)
    {
        $daysDiff = $startDate->diffInDays($endDate);

        if ($daysDiff <= 1) $periode = 'harian';
        elseif ($daysDiff <= 7) $periode = 'mingguan';
        elseif ($daysDiff <= 31) $periode = 'bulanan';
        else $periode = 'kustom';

        $laporan = Laporan::create([
            'tipe' => $tipe,
            'periode' => $periode,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'data_summary' => $data,
            'total_penjualan' => $tipe === 'penjualan' ? ($data['summary']['total_amount'] ?? 0) : 0,
            'total_penyewaan' => $tipe === 'penyewaan' ? ($data['summary']['total_amount'] ?? 0) : 0,
            'total_pendapatan' => $data['summary']['total_amount'] ?? 0,
            'total_transaksi' => $tipe === 'penjualan' ? ($data['summary']['total_transactions'] ?? 0) : ($data['summary']['total_rentals'] ?? 0),
            'dibuat_oleh' => Auth::id(),
        ]);

        return $laporan;
    }
}
