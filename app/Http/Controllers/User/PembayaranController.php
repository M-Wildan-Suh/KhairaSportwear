<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SewaService;
use App\Models\Transaksi;

class PembayaranController extends Controller
{
    public function uploadBuktiBayar(Request $request, $transaksiId)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|max:2048',
            'metode_pembayaran' => 'required|string',
            'nama_bank' => 'required_if:metode_pembayaran,transfer',
            'no_rekening' => 'required_if:metode_pembayaran,transfer',
            'atas_nama' => 'required_if:metode_pembayaran,transfer'
        ]);

        $transaksi = Transaksi::findOrFail($transaksiId);

        // Upload bukti bayar
        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            $transaksi->bukti_pembayaran = $path;
        }

        $transaksi->update([
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'status' => Transaksi::STATUS_MENUNGGU_KONFIRMASI
        ]);

        return redirect()->back()
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    public function verifikasiPembayaran($transaksiId)
    {
        // Hanya admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        try {
            $transaksi = SewaService::verifikasiPembayaran($transaksiId, auth()->id());

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Pembayaran berhasil diverifikasi. Sewa telah aktif.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Verifikasi gagal: ' . $e->getMessage());
        }
    }
}