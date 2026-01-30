<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\Sewa;
use App\Models\Konfigurasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = auth()->user()->transaksis()
            ->with(['detailTransaksis.produk'])
            ->latest()
            ->paginate(10);

        return view('user.transaksi.index', compact('transaksis'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $query = $user->keranjangs()->with('produk');

        // 🔥 FILTER ITEM TERPILIH
        if ($request->filled('items')) {
            $ids = explode(',', $request->items);
            $query->whereIn('id', $ids);
        }

        $keranjangs = $query->get();


        // Debug info
        \Log::info('=== CHECKOUT DEBUG ===');
        \Log::info('User: ' . $user->id . ' - ' . $user->name);
        \Log::info('Total items in cart: ' . $keranjangs->count());

        foreach ($keranjangs as $item) {
            \Log::info('Cart Item:', [
                'id' => $item->id,
                'produk_id' => $item->produk_id,
                'produk_nama' => $item->produk->nama,
                'tipe' => $item->tipe,
                'quantity' => $item->quantity,
                'harga' => $item->harga,
                'subtotal' => $item->subtotal,
                'stok_tersedia' => $item->produk->stok_tersedia,
                'stok_sewa' => $item->produk->stok_tersedia,
                'opsi_sewa' => $item->opsi_sewa,
                'opsi_sewa_type' => gettype($item->opsi_sewa)
            ]);
        }

        // Validate stock before checkout
        foreach ($keranjangs as $item) {
            if ($item->tipe === 'jual' && $item->produk->stok_tersedia < $item->quantity) {
                \Log::error('Stock insufficient for sale', [
                    'produk' => $item->produk->nama,
                    'stok' => $item->produk->stok_tersedia,
                    'required' => $item->quantity
                ]);

                return redirect()->route('user.keranjang.index')
                    ->with('error', "Stok {$item->produk->nama} tidak mencukupi.");
            }

            if ($item->tipe === 'sewa' && $item->produk->stok_tersedia < $item->quantity) {
                \Log::error('Stock insufficient for rental', [
                    'produk' => $item->produk->nama,
                    'stok_sewa' => $item->produk->stok_tersedia,
                    'required' => $item->quantity
                ]);

                return redirect()->route('user.keranjang.index')
                    ->with('error', "Stok sewa {$item->produk->nama} tidak mencukupi.");
            }
        }

        // Calculate totals
        $subtotal = $keranjangs->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

        \Log::info('Calculated totals:', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total
        ]);

        // Get bank transfer info from config
        $bankInfo = Konfigurasi::getValue('bank_transfer', []);
        $noRekening = Konfigurasi::getValue('no_rekening_admin');
        $namaRekening = Konfigurasi::getValue('nama_rekening_admin');

        \Log::info('Bank info:', [
            'bankInfo' => $bankInfo,
            'noRekening' => $noRekening,
            'namaRekening' => $namaRekening
        ]);

        return view('user.transaksi.create', compact(
            'keranjangs',
            'subtotal',
            'tax',
            'total',
            'bankInfo',
            'noRekening',
            'namaRekening'
        ));
    }

    private function createSewaFromKeranjang($transaksi, $detailTransaksi, $keranjangItem)
    {
        $opsiSewa = $keranjangItem->opsi_sewa;
        $tanggalMulai = Carbon::parse($opsiSewa['tanggal_mulai']);
        $jumlahHari = $opsiSewa['jumlah_hari'] ?? 1;
        $durasi = $opsiSewa['durasi'] ?? 'harian';

        // Hitung tanggal selesai
        $tanggalSelesai = $tanggalMulai->copy()->addDays($jumlahHari);

        // Buat entri Sewa
        $sewa = Sewa::create([
            'transaksi_id' => $transaksi->id,
            'detail_transaksi_id' => $detailTransaksi->id, // Hubungkan ke detail transaksi
            'user_id' => $transaksi->user_id,
            'produk_id' => $keranjangItem->produk_id,
            'kode_sewa' => Sewa::generateKodeSewa(),
            'durasi' => $durasi,
            'jumlah_hari' => $jumlahHari,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'tanggal_kembali_rencana' => $tanggalSelesai,
            'status' => 'tidak aktif',
            'total_harga' => $detailTransaksi->subtotal,
            'data_tambahan' => [
                'quantity' => $keranjangItem->quantity,
                'harga_per_hari' => $keranjangItem->harga,
                'durasi' => $durasi,
                'tanggal_mulai' => $tanggalMulai->toDateString(),
                'tanggal_selesai' => $tanggalSelesai->toDateString()
            ]
        ]);

        return $sewa;
    }

    public function store(Request $request)
    {
        \Log::info('=== TRANSACTION STORE START ===');
        \Log::info('Request Data:', $request->all());
        \Log::info('User ID: ' . auth()->id());
        \Log::info('User Name: ' . auth()->user()->name);

        // Log semua input termasuk file
        \Log::info('All Inputs:', [
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan' => $request->catatan,
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'has_file' => $request->hasFile('bukti_pembayaran')
        ]);

        $request->validate([
            'metode_pembayaran' => 'required|in:transfer_bank,tunai,qris',
            'catatan' => 'nullable|string|max:500',
            'alamat_pengiriman' => 'nullable|string|max:1000',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $ids = [];

        if ($request->filled('items')) {
            $ids = explode(',', $request->items);
        }

        $keranjangs = $user->keranjangs()
            ->with('produk')
            ->when(!empty($ids), function ($q) use ($ids) {
                $q->whereIn('id', $ids);
            })
            ->get();

        \Log::info('Cart items retrieved:', [
            'count' => $keranjangs->count(),
            'items' => $keranjangs->map(function ($item) {
                // Debug detail untuk setiap produk
                $opsiDecoded = null;
                if (is_string($item->opsi_sewa)) {
                    $opsiDecoded = json_decode($item->opsi_sewa, true);
                    $jsonError = json_last_error_msg();
                } else {
                    $opsiDecoded = $item->opsi_sewa;
                    $jsonError = 'N/A';
                }

                return [
                    'id' => $item->id,
                    'produk_id' => $item->produk_id,
                    'produk_nama' => $item->produk->nama,
                    'tipe' => $item->tipe,
                    'quantity' => $item->quantity,
                    'harga' => $item->harga,
                    'subtotal' => $item->subtotal,
                    'stok_tersedia' => $item->produk->stok_tersedia,
                    'stok_disewa' => $item->produk->stok_tersedia, // Gunakan stok_disewa langsung
                    'stok_total' => $item->produk->stok_total,
                    'opsi_sewa' => $item->opsi_sewa,
                    'opsi_sewa_decoded' => $opsiDecoded,
                    'json_error' => $jsonError
                ];
            })->toArray()
        ]);

        if ($keranjangs->isEmpty()) {
            \Log::warning('Cart is empty for user: ' . $user->id);
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong.'
            ], 400);
        }

        // Validasi stok dengan debugging detail
        \Log::info('=== STOCK VALIDATION ===');
        foreach ($keranjangs as $item) {
            $stokTersedia = $item->produk->stok_tersedia;
            $stokSewa = $item->produk->stok_tersedia; // Gunakan stok_disewa dari database

            \Log::info('Validating item: ' . $item->produk->nama, [
                'product_id' => $item->produk_id,
                'product_name' => $item->produk->nama,
                'tipe' => $item->tipe,
                'quantity' => $item->quantity,
                'stok_tersedia' => $stokTersedia,
                'stok_disewa' => $stokSewa,
                'comparison_jual' => $item->tipe === 'jual' ? ($stokTersedia . ' >= ' . $item->quantity . ' = ' . ($stokTersedia >= $item->quantity ? 'PASS' : 'FAIL')) : 'N/A',
                'comparison_sewa' => $item->tipe === 'sewa' ? ($stokSewa . ' >= ' . $item->quantity . ' = ' . ($stokSewa >= $item->quantity ? 'PASS' : 'FAIL')) : 'N/A'
            ]);

            if ($item->tipe === 'jual') {
                if ($stokTersedia < $item->quantity) {
                    \Log::error('Stock validation failed for sale item', [
                        'product' => $item->produk->nama,
                        'required' => $item->quantity,
                        'available' => $stokTersedia,
                        'deficit' => $item->quantity - $stokTersedia
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$item->produk->nama} tidak mencukupi. Stok tersedia: {$stokTersedia}, dibutuhkan: {$item->quantity}",
                        'debug' => [
                            'stok_tersedia' => $stokTersedia,
                            'quantity' => $item->quantity
                        ]
                    ], 400);
                }
            } elseif ($item->tipe === 'sewa') {
                // Validasi stok sewa - GUNAKAN stok_disewa
                if ($stokSewa < $item->quantity) {
                    \Log::error('Stock validation failed for rental item', [
                        'product' => $item->produk->nama,
                        'required' => $item->quantity,
                        'available' => $stokSewa,
                        'deficit' => $item->quantity - $stokSewa,
                        'field_used' => 'stok_disewa'
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => "Stok sewa {$item->produk->nama} tidak mencukupi. Stok sewa tersedia: {$stokSewa}, dibutuhkan: {$item->quantity}",
                        'debug' => [
                            'stok_disewa' => $stokSewa,
                            'quantity' => $item->quantity,
                            'product_id' => $item->produk_id
                        ]
                    ], 400);
                }

                // Validasi tambahan untuk data sewa
                $opsi = $item->opsi_sewa;
                if (is_string($opsi)) {
                    $opsi = json_decode($opsi, true);
                }

                if (empty($opsi) || !isset($opsi['tanggal_mulai'])) {
                    \Log::error('Rental options incomplete', [
                        'product' => $item->produk->nama,
                        'opsi' => $opsi
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => "Data sewa tidak lengkap untuk produk: {$item->produk->nama}"
                    ], 400);
                }
            }
        }

        \Log::info('=== STOCK VALIDATION PASSED ===');

        $checkedOutCartIds = $keranjangs->pluck('id')->toArray();

        DB::beginTransaction();
        \Log::info('Transaction started');

        try {
            $hasJual = $keranjangs->where('tipe', 'jual')->isNotEmpty();
            $hasSewa = $keranjangs->where('tipe', 'sewa')->isNotEmpty();
            $transactionCodes = [];

            \Log::info('Transaction types:', [
                'has_jual' => $hasJual,
                'has_sewa' => $hasSewa,
                'jual_count' => $keranjangs->where('tipe', 'jual')->count(),
                'sewa_count' => $keranjangs->where('tipe', 'sewa')->count()
            ]);

            // Penjualan
            if ($hasJual) {
                $jualItems = $keranjangs->where('tipe', 'jual');
                \Log::info('Creating sales transaction with ' . $jualItems->count() . ' items');

                $transaksiJual = $this->createTransactionForItems($user, $jualItems, 'penjualan', $request);
                $transactionCodes[] = $transaksiJual->kode_transaksi;

                \Log::info('Sales transaction created:', [
                    'id' => $transaksiJual->id,
                    'kode' => $transaksiJual->kode_transaksi,
                    'total' => $transaksiJual->total_bayar,
                    'status' => $transaksiJual->status
                ]);
            }

            // Penyewaan
            if ($hasSewa) {
                $sewaItems = $keranjangs->where('tipe', 'sewa');
                \Log::info('Creating rental transaction with ' . $sewaItems->count() . ' items');

                foreach ($sewaItems as $item) {
                    // Decode opsi sewa jika perlu
                    $opsi = $item->opsi_sewa;
                    if (is_string($opsi)) {
                        $opsi = json_decode($opsi, true);
                    }

                    \Log::info('Rental item details:', [
                        'produk_id' => $item->produk_id,
                        'produk_nama' => $item->produk->nama,
                        'quantity' => $item->quantity,
                        'harga' => $item->harga,
                        'stok_disewa_before' => $item->produk->stok_tersedia,
                        'opsi_sewa_raw' => $item->opsi_sewa,
                        'opsi_sewa_parsed' => $opsi,
                        'has_tanggal_mulai' => isset($opsi['tanggal_mulai']),
                        'jumlah_hari' => $opsi['jumlah_hari'] ?? 'N/A',
                        'durasi' => $opsi['durasi'] ?? 'N/A'
                    ]);
                }

                $transaksiSewa = $this->createTransactionForItems($user, $sewaItems, 'penyewaan', $request);
                $transactionCodes[] = $transaksiSewa->kode_transaksi;

                \Log::info('Rental transaction created:', [
                    'id' => $transaksiSewa->id,
                    'kode' => $transaksiSewa->kode_transaksi,
                    'total' => $transaksiSewa->total_bayar,
                    'status' => $transaksiSewa->status
                ]);
            }

            // Kosongkan keranjang
            $deleted = Keranjang::where('user_id', $user->id)
                ->whereIn('id', $checkedOutCartIds)
                ->delete();

            \Log::info('Cart items deleted (checked out only):', [
                'deleted_count' => $deleted,
                'cart_ids' => $checkedOutCartIds
            ]);

            DB::commit();
            \Log::info('Transaction committed successfully');

            \Log::info('Transaksi berhasil dibuat:', [
                'transaction_codes' => $transactionCodes,
                'user_id' => $user->id,
                'total_transactions' => count($transactionCodes)
            ]);

            // Notifikasi
            if (class_exists(\App\Models\Notifikasi::class)) {
                \App\Models\Notifikasi::createNotifikasi(
                    $user->id,
                    'Transaksi Berhasil',
                    'Transaksi Anda berhasil dibuat dengan kode: ' . implode(', ', $transactionCodes) . '. Silakan upload bukti pembayaran.',
                    'transaksi',
                    route('user.transaksi.index')
                );
                \Log::info('Notification created');
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat.',
                'transaction_code' => implode(', ', $transactionCodes),
                'redirect' => route('user.transaksi.index'),
                'debug' => [
                    'transaction_count' => count($transactionCodes),
                    'codes' => $transactionCodes
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal membuat transaksi:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'cart_count' => $keranjangs->count()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'debug' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Buat transaksi untuk item (penjualan atau penyewaan)
     */
    private function createTransactionForItems($user, $items, $tipe, $request)
    {
        \Log::info('=== CREATE TRANSACTION FOR ITEMS ===');
        \Log::info('Type: ' . $tipe);
        \Log::info('Items count: ' . $items->count());

        $subtotal = $items->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

        \Log::info('Calculations:', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total
        ]);

        $transaksi = Transaksi::create([
            'kode_transaksi' => Transaksi::generateKodeTransaksi(),
            'user_id' => $user->id,
            'tipe' => $tipe,
            'total_harga' => $subtotal,
            'diskon' => 0,
            'total_bayar' => $total,
            'status' => 'pending',
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'catatan' => $request->catatan,
            'alamat_pengiriman' => $tipe === 'penjualan' ? $request->alamat_pengiriman : null
        ]);

        \Log::info('Transaction created:', [
            'id' => $transaksi->id,
            'kode' => $transaksi->kode_transaksi,
            'user_id' => $transaksi->user_id,
            'tipe' => $transaksi->tipe
        ]);

        foreach ($items as $item) {
            \Log::info('Creating detail for item:', [
                'produk_id' => $item->produk_id,
                'produk_nama' => $item->produk->nama,
                'tipe_produk' => $item->tipe,
                'quantity' => $item->quantity,
                'harga_satuan' => $item->harga,
                'subtotal' => $item->subtotal,
                'opsi_sewa' => $item->opsi_sewa
            ]);

            $detail = DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $item->produk_id,
                'tipe_produk' => $item->tipe,
                'quantity' => $item->quantity,
                'bundle_id' => $item->bundle_id,
                'harga_satuan' => $item->harga,
                'subtotal' => $item->subtotal,
                'opsi_sewa' => $item->opsi_sewa
            ]);

            \Log::info('Detail created:', [
                'detail_id' => $detail->id,
                'transaksi_id' => $detail->transaksi_id
            ]);

            if ($tipe === 'penjualan') {
                // Update stok untuk penjualan
                $oldStock = $item->produk->stok_tersedia;
                \Log::info('Updating sale stock for product:', [
                    'produk_id' => $item->produk_id,
                    'quantity' => $item->quantity,
                    'old_stock' => $oldStock,
                    'new_stock' => $oldStock - $item->quantity
                ]);

                $item->produk->updateStok($item->quantity, 'keluar');

                \Log::info('Sale stock updated:', [
                    'current_stock' => $item->produk->fresh()->stok_tersedia
                ]);
            } else {
                // Update stok untuk penyewaan - GUNAKAN stok_disewa
                $oldStockSewa = $item->produk->stok_tersedia;
                $oldStockTersedia = $item->produk->stok_tersedia;

                \Log::info('Updating rental stock for product:', [
                    'produk_id' => $item->produk_id,
                    'produk_nama' => $item->produk->nama,
                    'quantity' => $item->quantity,
                    'old_stok_disewa' => $oldStockSewa,
                    'old_stok_tersedia' => $oldStockTersedia
                ]);

                // Create sewa record
                $sewa = $this->createSewaRecord($transaksi, $item);

                // Update stok menggunakan method yang benar
                $item->produk->updateStokSewa($item->quantity, 'keluar');

                $item->produk->refresh();

                \Log::info('Rental processing completed', [
                    'sewa_id' => $sewa ? $sewa->id : null,
                    'new_stok_disewa' => $item->produk->stok_tersedia,
                    'new_stok_tersedia' => $item->produk->stok_tersedia,
                    'stock_updated' => true
                ]);
            }
        }

        return $transaksi;
    }

    /**
     * Buat record sewa dari item keranjang
     */
    private function createSewaRecord($transaksi, $item)
    {
        \Log::info('=== CREATE SEWA RECORD ===');
        \Log::info('Transaction ID: ' . $transaksi->id);
        \Log::info('Item ID: ' . $item->id);
        \Log::info('Product ID: ' . $item->produk_id);

        $opsi = $item->opsi_sewa ?? [];

        \Log::info('Original opsi_sewa:', [
            'raw' => $opsi,
            'type' => gettype($opsi),
            'is_string' => is_string($opsi),
            'is_array' => is_array($opsi)
        ]);

        // Jika opsi_sewa adalah JSON string, decode
        if (is_string($opsi) && !empty($opsi)) {
            $decoded = json_decode($opsi, true);
            \Log::info('Decoded JSON:', [
                'decoded' => $decoded,
                'json_error' => json_last_error_msg()
            ]);

            if (json_last_error() === JSON_ERROR_NONE) {
                $opsi = $decoded;
            } else {
                \Log::error('Failed to decode JSON opsi_sewa:', [
                    'json_error' => json_last_error_msg(),
                    'raw_string' => $opsi
                ]);
                throw new \Exception('Format data sewa tidak valid.');
            }
        }

        \Log::info('Processed opsi:', $opsi);

        // Validasi data yang diperlukan
        if (empty($opsi)) {
            \Log::error('Empty rental options');
            throw new \Exception('Data sewa tidak ditemukan.');
        }

        if (!isset($opsi['tanggal_mulai'])) {
            \Log::error('Missing tanggal_mulai', ['opsi' => $opsi]);
            throw new \Exception('Tanggal mulai sewa tidak ditemukan.');
        }

        $jumlahHari = max(1, $opsi['jumlah_hari'] ?? 1);
        $durasi = in_array($opsi['durasi'] ?? '', ['harian', 'mingguan', 'bulanan'])
            ? $opsi['durasi']
            : 'harian';

        \Log::info('Rental parameters:', [
            'jumlah_hari' => $jumlahHari,
            'durasi' => $durasi,
            'tanggal_mulai' => $opsi['tanggal_mulai']
        ]);

        try {
            $tanggalMulai = Carbon::parse($opsi['tanggal_mulai']);
            $tanggalSelesai = $tanggalMulai->copy()->addDays((int) $jumlahHari);

            \Log::info('Parsed dates:', [
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai' => $tanggalSelesai->format('Y-m-d')
            ]);

            $sewa = Sewa::create([
                'transaksi_id' => $transaksi->id,
                'user_id' => $transaksi->user_id,
                'produk_id' => $item->produk_id,
                'kode_sewa' => Sewa::generateKodeSewa(),
                'durasi' => $durasi,
                'jumlah_hari' => $jumlahHari,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'tanggal_kembali_rencana' => $tanggalSelesai,
                'total_harga' => $item->subtotal ?? 0,
                'status' => 'tidak aktif',
                'denda' => 0,
                'catatan' => $opsi['catatan'] ?? null
            ]);

            \Log::info('Sewa record created successfully:', [
                'sewa_id' => $sewa->id,
                'kode_sewa' => $sewa->kode_sewa,
                'status' => $sewa->status
            ]);

            return $sewa;
        } catch (\Exception $e) {
            \Log::error('Failed to create sewa record', [
                'item' => $item->toArray(),
                'opsi' => $opsi,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Gagal membuat record sewa: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaksi = auth()->user()->transaksis()
            ->with(['detailTransaksis.produk', 'sewa.produk'])
            ->findOrFail($id);

        return view('user.transaksi.show', compact('transaksi'));
    }

    public function uploadBukti(Request $request, $id)
    {
        \Log::info('=== UPLOAD BUKTI PEMBAYARAN ===');
        \Log::info('Transaction ID: ' . $id);
        \Log::info('User ID: ' . auth()->id());

        $validator = Validator::make(
            $request->all(),
            [
                'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ],
            [
                'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
                'bukti_pembayaran.image'    => 'File bukti pembayaran harus berupa gambar.',
                'bukti_pembayaran.mimes'    => 'Format gambar harus JPG, JPEG, atau PNG.',
                'bukti_pembayaran.max'      => 'Ukuran gambar maksimal 2MB.'
            ]
        );

        $transaksi = auth()->user()->transaksis()->findOrFail($id);

        \Log::info('Transaction found:', [
            'id' => $transaksi->id,
            'kode' => $transaksi->kode_transaksi,
            'status' => $transaksi->status
        ]);

        if ($transaksi->status !== 'pending') {
            \Log::warning('Transaction not in pending status', [
                'current_status' => $transaksi->status,
                'expected' => 'pending'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hanya transaksi pending yang dapat diupload bukti pembayaran.'
            ], 400);
        }

        // Upload image
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');

            // Nama file
            $filename = time() . '_' . Str::random(10);
            $extension = $file->getClientOriginalExtension();

            // Path tujuan
            $path = public_path('storage/bukti-pembayaran/');

            // Pastikan folder ada
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Pindahkan file
            $file->move($path, $filename . '.' . $extension);

            // Simpan ke database
            $transaksi->bukti_pembayaran = $filename . '.' . $extension;
            $transaksi->tanggal_pembayaran = now();
            $transaksi->status = 'diproses';
            $transaksi->save();

            // Create notification
            if (class_exists(\App\Models\Notifikasi::class)) {
                \App\Models\Notifikasi::createNotifikasi(
                    $transaksi->user_id,
                    'Bukti Pembayaran Diupload',
                    'Bukti pembayaran untuk transaksi ' . $transaksi->kode_transaksi . ' telah diupload.',
                    'transaksi',
                    route('user.transaksi.show', $transaksi->id)
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload.',
                'bukti_url' => $transaksi->bukti_pembayaran_url
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupload bukti pembayaran.'
        ], 400);
    }

    public function cancel($id)
    {
        \Log::info('=== CANCEL TRANSACTION ===');
        \Log::info('Transaction ID: ' . $id);
        \Log::info('User ID: ' . auth()->id());

        $transaksi = auth()->user()->transaksis()->findOrFail($id);

        \Log::info('Transaction found:', [
            'id' => $transaksi->id,
            'kode' => $transaksi->kode_transaksi,
            'status' => $transaksi->status,
            'tipe' => $transaksi->tipe
        ]);

        if (!in_array($transaksi->status, ['pending', 'diproses'])) {
            \Log::warning('Transaction cannot be cancelled', [
                'current_status' => $transaksi->status,
                'allowed_statuses' => ['pending', 'diproses']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dibatalkan.'
            ], 400);
        }

        DB::beginTransaction();
        \Log::info('Transaction started for cancellation');

        try {
            // Restore stock
            \Log::info('Restoring stock for transaction items');

            foreach ($transaksi->detailTransaksis as $detail) {
                \Log::info('Processing detail:', [
                    'id' => $detail->id,
                    'produk_id' => $detail->produk_id,
                    'tipe_produk' => $detail->tipe_produk,
                    'quantity' => $detail->quantity
                ]);

                if ($detail->tipe_produk === 'jual') {
                    $detail->produk->updateStok($detail->quantity, 'masuk');
                    \Log::info('Sale stock restored');
                } else {
                    $detail->produk->updateStokSewa($detail->quantity, 'masuk');
                    \Log::info('Rental stock restored');
                }
            }

            // Cancel any rental records
            if ($transaksi->sewa) {
                \Log::info('Cancelling rental record:', [
                    'sewa_id' => $transaksi->sewa->id,
                    'kode_sewa' => $transaksi->sewa->kode_sewa
                ]);

                $transaksi->sewa->update(['status' => 'dibatalkan']);
                \Log::info('Rental record cancelled');
            }

            // Update transaction status
            $transaksi->status = 'dibatalkan';
            $transaksi->save();

            \Log::info('Transaction cancelled:', [
                'new_status' => $transaksi->status
            ]);

            DB::commit();
            \Log::info('Cancellation transaction committed');

            // Create notification
            if (class_exists(\App\Models\Notifikasi::class)) {
                \App\Models\Notifikasi::createNotifikasi(
                    $transaksi->user_id,
                    'Transaksi Dibatalkan',
                    'Transaksi ' . $transaksi->kode_transaksi . ' telah dibatalkan.',
                    'warning',
                    route('user.transaksi.show', $transaksi->id)
                );
                \Log::info('Notification created');
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to cancel transaction:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}
