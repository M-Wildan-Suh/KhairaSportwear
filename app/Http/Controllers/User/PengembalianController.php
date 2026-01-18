<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Services\PengembalianService;
use App\Models\Sewa;

class PengembalianController extends Controller
{
    public function ajukan($sewaId)
    {
        $sewa = Sewa::with(['produk', 'user'])->findOrFail($sewaId);

        // Pastikan user hanya bisa mengajukan sewa miliknya sendiri
        if ($sewa->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('pengembalian.ajukan', compact('sewa'));
    }

    public function store(Request $request, $sewaId)
    {
        $request->validate([
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_mulai',
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_kondisi' => 'nullable|string'
        ]);

        try {
            $pengembalian = PengembalianService::ajukanPengembalian(
                $sewaId,
                $request->tanggal_kembali,
                $request->only(['kondisi_alat', 'catatan_kondisi'])
            );

            return redirect()->route('sewa.show', $sewaId)
                ->with('success', 'Pengembalian berhasil diajukan. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Pengajuan pengembalian gagal: ' . $e->getMessage());
        }
    }

    public function verifikasi($pengembalianId)
    {
        // Hanya admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $pengembalian = \App\Models\Pengembalian::with(['sewa', 'sewa.produk'])->findOrFail($pengembalianId);

        return view('admin.pengembalian.verifikasi', compact('pengembalian'));
    }

    public function prosesVerifikasi(Request $request, $pengembalianId)
    {
        $request->validate([
            'denda_kerusakan' => 'nullable|numeric|min:0',
            'catatan_admin' => 'nullable|string'
        ]);

        try {
            $pengembalian = PengembalianService::verifikasiPengembalian(
                $pengembalianId,
                auth()->id(),
                $request->only(['denda_kerusakan', 'catatan_admin'])
            );

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diverifikasi.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Verifikasi gagal: ' . $e->getMessage());
        }
    }
}