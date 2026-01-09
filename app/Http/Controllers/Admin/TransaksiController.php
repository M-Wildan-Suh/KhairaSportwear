<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'sewa', 'detailTransaksis.produk'])->latest();

        // Filter tipe
        if ($request->filled('tipe') && $request->tipe != 'semua') {
            $query->where('tipe', $request->tipe);
        }

        // Filter status
        if ($request->filled('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
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
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(20);

        // Statistik
        $totalTransactions = Transaksi::count();
        $totalPenjualan = Transaksi::where('tipe', 'penjualan')->count();
        $totalPenyewaan = Transaksi::where('tipe', 'penyewaan')->count();

        return view('admin.transaksi.index', compact(
            'transactions', 
            'totalTransactions',
            'totalPenjualan',
            'totalPenyewaan'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $transaction = Transaksi::with(['user', 'sewa', 'detailTransaksis.produk'])->findOrFail($id);
        $statuses = ['pending', 'diproses', 'dibayar', 'dikirim', 'selesai', 'dibatalkan'];

        return view('admin.transaksi.edit', compact('transaction', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    $transaction = Transaksi::with(['sewa'])->findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|in:pending,diproses,dibayar,dikirim,selesai,dibatalkan',
        'catatan' => 'nullable|string|max:500',
        'metode_pembayaran' => 'nullable|string|max:50',
        'nama_bank' => 'nullable|string|max:100',
        'no_rekening' => 'nullable|string|max:50',
        'atas_nama' => 'nullable|string|max:100',
        'tanggal_pembayaran' => 'nullable|date',
        'tanggal_pengiriman' => 'nullable|date',
        'tanggal_mulai' => 'nullable|date', // For rental
        'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai', // For rental
    ]);

    // Set tanggal otomatis jika status berubah
    if ($request->status == 'dibayar' && $transaction->status != 'dibayar') {
        $validated['tanggal_pembayaran'] = now();
    }
    if ($request->status == 'dikirim' && $transaction->status != 'dikirim') {
        $validated['tanggal_pengiriman'] = now();
    }

    // Update transaction
    $transaction->update($validated);

    // Update rental dates if transaction is rental type
    if ($transaction->tipe == 'penyewaan' && $transaction->sewa) {
        if ($request->filled('tanggal_mulai')) {
            $transaction->sewa->update(['tanggal_mulai' => $request->tanggal_mulai]);
        }
        if ($request->filled('tanggal_selesai')) {
            $transaction->sewa->update(['tanggal_selesai' => $request->tanggal_selesai]);
        }
        
        // Update sewa status when transaction is completed
        if ($request->status == 'selesai' && $transaction->sewa->status != 'selesai') {
            $transaction->sewa->update(['status' => 'selesai']);
        }
    }

    return redirect()->route('admin.transaksi.show', $transaction->id)
        ->with('success', 'Transaksi berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaction = Transaksi::with(['detailTransaksis', 'sewa'])->findOrFail($id);

        if (!in_array($transaction->status, ['pending', 'dibatalkan'])) {
            return redirect()->back()->with('error', 'Transaksi dengan status ini tidak dapat dihapus.');
        }

        $transaction->detailTransaksis()->delete();

        if ($transaction->sewa) {
            $transaction->sewa()->delete();
        }

        $transaction->delete();

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaksi::with([
            'user',
            'detailTransaksis.produk.kategori',
            'sewa.produk'
        ])->findOrFail($id);

        return view('admin.transaksi.show', compact('transaction'));
    }

    /**
     * Update status transaksi (null-safe)
     */
    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaksi::with('sewa')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,diproses,dibayar,dikirim,selesai,dibatalkan'
        ]);

        $transaction->status = $request->status;

        if ($request->status == 'dibayar') {
            $transaction->tanggal_pembayaran = now();
        }
        if ($request->status == 'dikirim') {
            $transaction->tanggal_pengiriman = now();
        }
        if ($request->status == 'selesai' && $transaction->tipe == 'penyewaan' && $transaction->sewa) {
            $transaction->sewa->update(['status' => 'selesai']);
        }

        $transaction->save();

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }

    /**
     * Generate invoice
     */
    public function invoice(Transaksi $transaksi)
    {
        $transaksi->load([
            'user',
            'detailTransaksis.produk.kategori',
            'sewa.produk'
        ]);

        return view('admin.transaksi.invoice', compact('transaksi'));
    }

    /**
     * Print invoice
     */
    public function print(Transaksi $transaksi)
    {
        $transaksi->load([
            'user',
            'detailTransaksis.produk.kategori',
            'sewa.produk'
        ]);

        return view('admin.transaksi.invoice', compact('transaksi'));
    }
}
