<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Produk;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource - khusus penjualan
     */
    public function index(Request $request)
    {
        // Hanya ambil transaksi penjualan
        $query = Transaksi::with(['user', 'detailTransaksis.produk'])
            ->withCount('detailTransaksis as items_count') // TAMBAHKAN INI
            ->latest();

        // Filter status
        if ($request->filled('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Filter metode bayar
        if ($request->filled('metode_bayar') && $request->metode_bayar != 'semua') {
            $query->where('metode_pembayaran', $request->metode_bayar);
        }

        // Filter tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter minimal total
        if ($request->filled('min_total')) {
            $query->where('total_bayar', '>=', $request->min_total);
        }

        $transactions = $query->paginate(20);

        // Statistik khusus penjualan
        $totalTransactions = Transaksi::where('tipe', 'penjualan')->count();
        $totalSelesai = Transaksi::where('tipe', 'penjualan')->where('status', 'selesai')->count();
        $totalPending = Transaksi::where('tipe', 'penjualan')->where('status', 'pending')->count();
        $totalPendapatan = Transaksi::where('tipe', 'penjualan')->where('status', 'selesai')->sum('total_bayar');

        return view('admin.transaksi.index', compact(
            'transactions',
            'totalTransactions',
            'totalSelesai',
            'totalPending',
            'totalPendapatan'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::where('status', 'aktif')
            ->where('stok', '>', 0)
            ->orderBy('nama')
            ->get();
            
        return view('admin.transaksi.create', compact('produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_address' => 'nullable|string',
                'metode_bayar' => 'required|in:tunai,transfer,debit,kartu_kredit',
                'bukti_bayar' => 'nullable|image|max:2048',
                'note' => 'nullable|string|max:500',
                'items' => 'required|array|min:1',
                'items.*.produk_id' => 'required|exists:produks,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.harga' => 'required|numeric|min:0',
                'items.*.diskon' => 'nullable|numeric|min:0',
            ]);
            
            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Hitung total
            $subtotal = 0;
            $totalDiskon = 0;
            
            foreach ($request->items as $item) {
                $produk = Produk::find($item['produk_id']);
                $itemSubtotal = $item['harga'] * $item['quantity'];
                $itemDiskon = $item['diskon'] ?? 0;
                
                $subtotal += $itemSubtotal;
                $totalDiskon += $itemDiskon;
                
                // Cek stok
                if ($produk->stok < $item['quantity']) {
                    throw new \Exception("Stok produk {$produk->nama} tidak mencukupi. Stok tersedia: {$produk->stok}");
                }
            }
            
            $totalBayar = $subtotal - $totalDiskon;
            
            // Upload bukti bayar jika ada
            $buktiBayarPath = null;
            if ($request->hasFile('bukti_bayar')) {
                $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
            }
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'user_id' => auth()->id(),
                'kode_transaksi' => $kodeTransaksi,
                'tipe' => 'penjualan',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_address' => $request->customer_address,
                'subtotal' => $subtotal,
                'diskon' => $totalDiskon,
                'total_bayar' => $totalBayar,
                'metode_bayar' => $request->metode_bayar,
                'bukti_bayar' => $buktiBayarPath,
                'status' => $request->metode_bayar == 'tunai' ? 'dibayar' : 'pending',
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);
            
            // Simpan item transaksi dan kurangi stok
            foreach ($request->items as $item) {
                $produk = Produk::find($item['produk_id']);
                $itemSubtotal = $item['harga'] * $item['quantity'];
                $itemDiskon = $item['diskon'] ?? 0;
                
                // Buat detail transaksi
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk_id'],
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga'],
                    'diskon' => $itemDiskon,
                    'subtotal' => $itemSubtotal - $itemDiskon,
                ]);
                
                // Kurangi stok produk
                $produk->decrement('stok', $item['quantity']);
                
                // Log stok
                \App\Models\StokLog::create([
                    'produk_id' => $produk->id,
                    'user_id' => auth()->id(),
                    'tipe' => 'keluar',
                    'quantity' => $item['quantity'],
                    'stok_sebelum' => $produk->stok + $item['quantity'],
                    'stok_sesudah' => $produk->stok,
                    'keterangan' => "Penjualan transaksi {$kodeTransaksi}",
                    'referensi_id' => $transaksi->id,
                    'referensi_tipe' => Transaksi::class,
                ]);
            }
            
            // Set tanggal pembayaran jika tunai
            if ($request->metode_bayar == 'tunai') {
                $transaksi->update([
                    'tanggal_pembayaran' => now(),
                    'verifikasi_oleh' => auth()->id(),
                    'tanggal_verifikasi' => now(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi penjualan berhasil dibuat.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create transaction:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal membuat transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaksi::with([
            'user',
            'detailTransaksis.produk.kategori',
            'verifikator'
        ])->where('tipe', 'penjualan')->findOrFail($id);

        return view('admin.transaksi.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $transaction = Transaksi::with(['user', 'detailTransaksis.produk'])
            ->findOrFail($id);
        
        // Statuses khusus penjualan
        $statuses = [
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'dibayar' => 'Dibayar',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return view('admin.transaksi.edit', compact('transaction', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
/**
 * Update the specified resource in storage - khusus penjualan.
 */
public function update(Request $request, $id)
{
    $transaction = Transaksi::with(['detailTransaksis.produk'])
        ->findOrFail($id);

    // Validasi khusus penjualan
    $validated = $request->validate([
        'status' => 'required|in:pending,diproses,dibayar,dikirim,selesai,dibatalkan',
        'catatan' => 'nullable|string|max:500',
        'tanggal_pengiriman' => 'nullable|date',
        'alamat_pengiriman' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $oldStatus = $transaction->status;
        $newStatus = $request->status;
        
        // ================= LOGIKA PERUBAHAN STATUS PENJUALAN =================
        
        // 1. Jika status berubah ke DIBAYAR (Verifikasi pembayaran)
        if ($newStatus == 'dibayar' && $oldStatus != 'dibayar') {
            // Set tanggal pembayaran dan verifikasi
            if (!$transaction->tanggal_pembayaran) {
                $transaction->tanggal_pembayaran = now();
                // $validated['tanggal_pembayaran'] = now();
            }

            $validated['verifikasi_oleh'] = auth()->id();
            $validated['tanggal_verifikasi'] = now();

            $sewa = Sewa::find($transaction->sewa->id);

            if ($sewa) {
                $sewa->status = 'aktif';
                $sewa->save();
            }
            
            // Hanya kurangi stok jika sebelumnya PENDING
            if ($oldStatus == 'pending') {
                foreach ($transaction->detailTransaksis as $detail) {
                    if ($detail->produk) {
                        $detail->produk->decrement('stok', $detail->quantity);
                        
                        $detail->bundle?->decrement('stok', $detail->quantity);

                        
                        // Log stok keluar
                        \App\Models\StokLog::create([
                            'produk_id' => $detail->produk->id,
                            'user_id' => auth()->id(),
                            'tipe' => 'keluar',
                            'quantity' => $detail->quantity,
                            'stok_sebelum' => $detail->produk->stok + $detail->quantity,
                            'stok_sesudah' => $detail->produk->stok,
                            'keterangan' => "Pembayaran transaksi {$transaction->kode_transaksi}",
                            'referensi_id' => $transaction->id,
                            'referensi_tipe' => Transaksi::class,
                        ]);
                    }
                }
            }
        }
        
        // 2. Jika status berubah ke DIKIRIM
        if ($newStatus == 'dikirim' && $oldStatus != 'dikirim') {
            $validated['tanggal_pengiriman'] = $request->tanggal_pengiriman ?? now();
        }
        
        // 3. Jika status berubah ke SELESAI
        if ($newStatus == 'selesai' && $oldStatus != 'selesai') {
            $validated['completed_at'] = now();

            $sewa = Sewa::find($transaction->sewa->id);

            if ($sewa) {
                $sewa->status = 'aktif';
                $sewa->save();
            }
        }
        
        // 4. Jika status berubah ke DIBATALKAN
        if ($newStatus == 'dibatalkan' && $oldStatus != 'dibatalkan') {

            $sewa = Sewa::find($transaction->sewa->id);

            if ($sewa) {
                $sewa->status = 'aktif';
                $sewa->save();
            }
            // Kembalikan stok jika sebelumnya sudah dibayar atau diproses
            if (in_array($oldStatus, ['dibayar', 'diproses', 'dikirim'])) {
                foreach ($transaction->detailTransaksis as $detail) {
                    if ($detail->produk) {
                        $detail->produk->increment('stok', $detail->quantity);
                        
                        // Log stok masuk (kembali)
                        \App\Models\StokLog::create([
                            'produk_id' => $detail->produk->id,
                            'user_id' => auth()->id(),
                            'tipe' => 'masuk',
                            'quantity' => $detail->quantity,
                            'stok_sebelum' => $detail->produk->stok - $detail->quantity,
                            'stok_sesudah' => $detail->produk->stok,
                            'keterangan' => "Pembatalan transaksi {$transaction->kode_transaksi}",
                            'referensi_id' => $transaction->id,
                            'referensi_tipe' => Transaksi::class,
                        ]);
                    }
                }
            }
        }
        
        // 5. Jika status berubah dari DIBATALKAN ke status lain
        if ($oldStatus == 'dibatalkan' && $newStatus != 'dibatalkan') {

            $sewa = Sewa::find($transaction->sewa->id);

            if ($sewa) {
                $sewa->status = 'aktif';
                $sewa->save();
            }
            // Kurangi stok lagi untuk status yang membutuhkan stok
            if (in_array($newStatus, ['dibayar', 'diproses', 'dikirim'])) {
                foreach ($transaction->detailTransaksis as $detail) {
                    if ($detail->produk) {
                        $detail->produk->decrement('stok', $detail->quantity);
                        
                        // Log stok keluar
                        \App\Models\StokLog::create([
                            'produk_id' => $detail->produk->id,
                            'user_id' => auth()->id(),
                            'tipe' => 'keluar',
                            'quantity' => $detail->quantity,
                            'stok_sebelum' => $detail->produk->stok + $detail->quantity,
                            'stok_sesudah' => $detail->produk->stok,
                            'keterangan' => "Reaktivasi transaksi {$transaction->kode_transaksi}",
                            'referensi_id' => $transaction->id,
                            'referensi_tipe' => Transaksi::class,
                        ]);
                    }
                }
                
                // Jika berubah ke DIBAYAR, set tanggal pembayaran
                if ($newStatus == 'dibayar') {
                    $validated['tanggal_pembayaran'] = now();
                    $validated['verifikasi_oleh'] = auth()->id();
                    $validated['tanggal_verifikasi'] = now();
                }
            }
        }

        // Update transaction
        $transaction->update($validated);

        DB::commit();

        // Create notification for user
        if ($oldStatus != $newStatus) {
            $this->createStatusNotification($transaction, $oldStatus);
        }

        return redirect()->route('admin.transaksi.show', $transaction->id)
            ->with('success', 'Transaksi berhasil diperbarui.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Failed to update transaction:', [
            'id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Update status transaksi
     */
    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaksi::with(['detailTransaksis.produk'])
            ->where('tipe', 'penjualan')
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,diproses,dibayar,dikirim,selesai,dibatalkan'
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $transaction->status;
            
            // Handle stock management when cancelling paid transaction
            if ($request->status == 'dibatalkan' && $transaction->status == 'dibayar') {
                // Kembalikan stok
                foreach ($transaction->detailTransaksis as $detail) {
                    if ($detail->produk) {
                        $detail->produk->increment('stok', $detail->quantity);
                        
                        // Log stok kembali
                        \App\Models\StokLog::create([
                            'produk_id' => $detail->produk->id,
                            'user_id' => auth()->id(),
                            'tipe' => 'masuk',
                            'quantity' => $detail->quantity,
                            'stok_sebelum' => $detail->produk->stok - $detail->quantity,
                            'stok_sesudah' => $detail->produk->stok,
                            'keterangan' => "Pembatalan transaksi {$transaction->kode_transaksi}",
                            'referensi_id' => $transaction->id,
                            'referensi_tipe' => Transaksi::class,
                        ]);
                    }
                }
            }
            
            // Handle stock when marking as paid (if coming from pending)
            if ($request->status == 'dibayar' && $transaction->status == 'pending') {
                // Stok sudah dikurangi saat transaksi dibuat (untuk penjualan offline)
            }

            // Update transaction status
            $transaction->status = $request->status;

            if ($request->status == 'dibayar') {
                $transaction->tanggal_pembayaran = now();
                $transaction->tanggal_verifikasi = now();
                $transaction->verifikasi_oleh = auth()->id();
            }
            if ($request->status == 'dikirim') {
                $transaction->tanggal_pengiriman = now();
            }
            if ($request->status == 'selesai') {
                $transaction->completed_at = now();
            }

            $transaction->save();

            DB::commit();

            // Create notification
            $this->createStatusNotification($transaction, $oldStatus);

            return redirect()->back()
                ->with('success', 'Status transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Failed to update transaction status:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaction = Transaksi::with(['detailTransaksis'])
            ->where('tipe', 'penjualan')
            ->findOrFail($id);

        // Only allow deletion of pending or cancelled transactions
        if (!in_array($transaction->status, ['pending', 'dibatalkan'])) {
            return redirect()->back()->with('error', 'Transaksi dengan status ini tidak dapat dihapus.');
        }

        DB::beginTransaction();

        try {
            // Kembalikan stok jika transaksi bukan dibatalkan
            if ($transaction->status != 'dibatalkan') {
                foreach ($transaction->detailTransaksis as $detail) {
                    if ($detail->produk) {
                        $detail->produk->increment('stok', $detail->quantity);
                        
                        // Log stok kembali
                        \App\Models\StokLog::create([
                            'produk_id' => $detail->produk->id,
                            'user_id' => auth()->id(),
                            'tipe' => 'masuk',
                            'quantity' => $detail->quantity,
                            'stok_sebelum' => $detail->produk->stok - $detail->quantity,
                            'stok_sesudah' => $detail->produk->stok,
                            'keterangan' => "Hapus transaksi {$transaction->kode_transaksi}",
                            'referensi_id' => $transaction->id,
                            'referensi_tipe' => Transaksi::class,
                        ]);
                    }
                }
            }

            $transaction->detailTransaksis()->delete();
            $transaction->delete();

            DB::commit();

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice
     */
    public function invoice($id)
    {
        $transaksi = Transaksi::with([
            'user',
            'detailTransaksis.produk.kategori',
            'verifikator'
        ])->where('tipe', 'penjualan')->findOrFail($id);

        return view('admin.transaksi.invoice', compact('transaksi'));
    }

    /**
     * Print invoice
     */
    public function print($id)
    {
        $transaksi = Transaksi::with([
            'user',
            'detailTransaksis.produk.kategori',
            'verifikator'
        ])->where('tipe', 'penjualan')->findOrFail($id);

        return view('admin.transaksi.print', compact('transaksi'));
    }

    /**
     * Verifikasi pembayaran transaksi
     */
    public function verifyPayment($id)
    {
        $transaction = Transaksi::with(['detailTransaksis.produk'])
            ->where('tipe', 'penjualan')
            ->findOrFail($id);
        
        \Log::info('Admin verifying payment:', [
            'transaction_id' => $transaction->id,
            'current_status' => $transaction->status
        ]);
        
        if ($transaction->status !== 'pending' && $transaction->status !== 'diproses') {
            return redirect()->back()
                ->with('error', 'Hanya transaksi dengan status pending/diproses yang dapat diverifikasi.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update transaction
            $transaction->update([
                'status' => 'dibayar',
                'tanggal_pembayaran' => now(),
                'verifikasi_oleh' => auth()->id(),
                'tanggal_verifikasi' => now()
            ]);
            
            \Log::info('Transaction verified:', [
                'new_status' => $transaction->status
            ]);
            
            DB::commit();
            
            // Notifikasi ke user
            $this->createStatusNotification($transaction, 'diproses');
            
            return redirect()->back()
                ->with('success', 'Pembayaran berhasil diverifikasi.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to verify payment:', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Export ke Excel
     */
    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $transactions = $query->get();
        
        $fileName = 'transaksi-penjualan-' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';
        
        return \Excel::download(new \App\Exports\TransaksiExport($transactions), $fileName);
    }
    
    /**
     * Export ke PDF
     */
    public function exportPDF(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $transactions = $query->get();
        
        $data = [
            'transactions' => $transactions,
            'filters' => $request->all(),
            'tanggal' => Carbon::now()->format('d/m/Y H:i'),
            'total' => $transactions->sum('total_bayar'),
            'total_transaksi' => $transactions->count(),
        ];
        
        $pdf = \PDF::loadView('admin.transaksi.export-pdf', $data);
        
        $fileName = 'transaksi-penjualan-' . Carbon::now()->format('Y-m-d-H-i') . '.pdf';
        
        return $pdf->download($fileName);
    }
    
    /**
     * Export ke CSV
     */
    public function exportCSV(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $transactions = $query->get();
        
        $fileName = 'transaksi-penjualan-' . Carbon::now()->format('Y-m-d-H-i') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'Kode Transaksi',
                'Tanggal',
                'Customer',
                'Telepon',
                'Items',
                'Subtotal',
                'Diskon',
                'Total',
                'Status',
                'Metode Bayar',
                'Kasir',
            ]);
            
            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->kode_transaksi,
                    $transaction->created_at->format('d/m/Y H:i'),
                    $transaction->customer_name,
                    $transaction->customer_phone,
                    $transaction->detailTransaksis->sum('quantity'),
                    $transaction->subtotal,
                    $transaction->diskon,
                    $transaction->total_bayar,
                    ucfirst($transaction->status),
                    ucfirst(str_replace('_', ' ', $transaction->metode_bayar)),
                    $transaction->user->name ?? '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Helper untuk query dengan filter
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Transaksi::with(['user', 'detailTransaksis'])
            ->where('tipe', 'penjualan')
            ->latest();
            
        // Filter berdasarkan status
        if ($request->filled('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan metode bayar
        if ($request->filled('metode_bayar') && $request->metode_bayar != 'semua') {
            $query->where('metode_bayar', $request->metode_bayar);
        }
        
        // Filter tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        // Filter tanggal selesai
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        
        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter minimal total
        if ($request->filled('min_total')) {
            $query->where('total_bayar', '>=', $request->min_total);
        }
        
        return $query;
    }

    /**
     * Create notification for status change
     */
    private function createStatusNotification($transaction, $oldStatus)
    {
        if (!class_exists(\App\Models\Notifikasi::class)) {
            return;
        }

        $messages = [
            'diproses' => [
                'title' => 'Transaksi Diproses',
                'message' => 'Transaksi ' . $transaction->kode_transaksi . ' sedang diproses.',
                'type' => 'info'
            ],
            'dibayar' => [
                'title' => 'Pembayaran Diverifikasi',
                'message' => 'Pembayaran transaksi ' . $transaction->kode_transaksi . ' telah diverifikasi.',
                'type' => 'success'
            ],
            'dikirim' => [
                'title' => 'Pesanan Dikirim',
                'message' => 'Pesanan ' . $transaction->kode_transaksi . ' telah dikirim.',
                'type' => 'info'
            ],
            'selesai' => [
                'title' => 'Transaksi Selesai',
                'message' => 'Transaksi ' . $transaction->kode_transaksi . ' telah selesai.',
                'type' => 'success'
            ],
            'dibatalkan' => [
                'title' => 'Transaksi Dibatalkan',
                'message' => 'Transaksi ' . $transaction->kode_transaksi . ' telah dibatalkan.',
                'type' => 'warning'
            ],
        ];

        if (isset($messages[$transaction->status])) {
            $msg = $messages[$transaction->status];
            
            \App\Models\Notifikasi::create([
                'user_id' => $transaction->user_id,
                'judul' => $msg['title'],
                'pesan' => $msg['message'],
                'tipe' => $msg['type'],
                'data' => json_encode([
                    'transaksi_id' => $transaction->id,
                    'kode_transaksi' => $transaction->kode_transaksi,
                    'old_status' => $oldStatus,
                    'new_status' => $transaction->status
                ]),
                'link' => route('user.transaksi.show', $transaction->id),
                'dibaca' => false
            ]);
        }
    }
}