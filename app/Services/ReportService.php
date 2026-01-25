<?php

namespace App\Services;

use App\Models\Sewa;
use App\Models\Transaksi;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Dashboard statistics
     */
    public static function getDashboardStats($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : now()->endOfMonth();

        return [
            // Sewa
            'total_sewa' => Sewa::where('user_id', Auth::id())->whereBetween('created_at', [$startDate, $endDate])->count(),
            'sewa_aktif' => Sewa::where('user_id', Auth::id())->where('status', Sewa::STATUS_AKTIF)->count(),
            'sewa_selesai' => Sewa::where('user_id', Auth::id())->where('status', Sewa::STATUS_SELESAI)
                ->whereBetween('updated_at', [$startDate, $endDate])->count(),
            'sewa_dibatalkan' => Sewa::where('user_id', Auth::id())->where('status', Sewa::STATUS_DIBATALKAN)
                ->whereBetween('updated_at', [$startDate, $endDate])->count(),

            // Pendapatan
            'pendapatan_sewa' => Sewa::where('user_id', Auth::id())->where('status', Sewa::STATUS_SELESAI)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('total_harga'),
            'total_denda' => Sewa::where('user_id', Auth::id())->where('status', Sewa::STATUS_SELESAI)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('denda'),

            // Pengembalian
            'pengembalian_pending' => Pengembalian::where('status', 'menunggu')
                ->whereHas('sewa', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->count(),
            'pengembalian_selesai' => Pengembalian::where('status', 'selesai')
                ->whereHas('sewa', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->whereBetween('updated_at', [$startDate, $endDate])->count(),
        ];
    }

    /**
     * Monthly revenue report
     */
    public static function getMonthlyRevenue($year = null)
    {
        $year = $year ?? now()->year;

        return Sewa::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('COUNT(*) as total_sewa'),
            DB::raw('SUM(total_harga) as pendapatan_sewa'),
            DB::raw('SUM(denda) as pendapatan_denda')
        )
            ->whereYear('created_at', $year)
            ->where('status', Sewa::STATUS_SELESAI)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('bulan')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->bulan => [
                        'total_sewa' => $item->total_sewa,
                        'pendapatan_sewa' => $item->pendapatan_sewa,
                        'pendapatan_denda' => $item->pendapatan_denda,
                        'total_pendapatan' => $item->pendapatan_sewa + $item->pendapatan_denda
                    ]
                ];
            });
    }

    /**
     * Top products rented
     */
    public static function getTopProducts($limit = 10, $startDate = null, $endDate = null)
    {
        $query = Sewa::select(
            'produk_id',
            DB::raw('COUNT(*) as total_sewa'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            ->with('produk')
            ->where('status', Sewa::STATUS_SELESAI)
            ->groupBy('produk_id')
            ->orderByDesc('total_sewa');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate),
                Carbon::parse($endDate)
            ]);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Late returns report
     */
    public static function getLateReturnsReport($startDate = null, $endDate = null)
    {
        $query = Pengembalian::with(['sewa.user', 'sewa.produk'])
            ->where('keterlambatan_hari', '>', 0)
            ->where('status', 'selesai');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_kembali', [
                Carbon::parse($startDate),
                Carbon::parse($endDate)
            ]);
        }

        return $query->orderByDesc('keterlambatan_hari')->get();
    }
}
