<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori')
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->when($request->kategori, function ($query, $kategori) {
                $query->where('kategori_id', $kategori);
            })
            ->when($request->tipe, function ($query, $tipe) {
                $query->where('tipe', $tipe);
            })
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        $produks = $query->paginate(10)->withQueryString();
        $kategoris = Kategori::active()->get();

        return view('admin.produk.index', compact('produks', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::active()->get();
        return view('admin.produk.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        if ($request->has('warna') && is_string($request->warna)) {
            $decodedWarna = json_decode($request->warna, true);
            $request->merge([
                'warna' => is_array($decodedWarna) ? $decodedWarna : []
            ]);
        }
        if ($request->has('size') && is_string($request->size)) {
            $decodedSize = json_decode($request->size, true);
            $request->merge([
                'size' => is_array($decodedSize) ? $decodedSize : []
            ]);
        }
        // Validasi dasar
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:jual,sewa,both',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'harga_sewa_mingguan' => 'nullable|numeric|min:0',
            'harga_sewa_bulanan' => 'nullable|numeric|min:0',
            'stok_total' => 'required|integer|min:0',
            'stok_tersedia' => 'nullable|integer|min:0|lte:stok_total',
            'stok_disewa' => 'nullable|integer|min:0|lte:stok_total',
            'warna' => 'nullable|array',
            'warna.*' => 'string|max:50',
            'size' => 'nullable|array',
            'size.*' => 'string|max:20',
            'gambar' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Validasi harga berdasarkan tipe
        if ($validated['tipe'] === 'jual' || $validated['tipe'] === 'both') {
            $request->validate(['harga_beli' => 'required|numeric|min:0']);
        }

        if ($validated['tipe'] === 'sewa' || $validated['tipe'] === 'both') {
            $request->validate(['harga_sewa_harian' => 'required|numeric|min:0']);
        }

        // Validasi stok berdasarkan tipe
        if ($validated['tipe'] === 'sewa' || $validated['tipe'] === 'both') {
            $request->validate(['stok_disewa' => 'nullable|integer|min:0|lte:stok_total']);
        } else {
            $validated['stok_disewa'] = 0; // Set ke 0 untuk produk jual saja
        }

        $validated['stok_tersedia'] = $validated['stok_total'];

        // Handle spesifikasi
        if ($request->has('spesifikasi')) {
            $spesifikasi = [];
            foreach ($request->spesifikasi as $key => $value) {
                if (!empty($key) && !empty($value)) {
                    $spesifikasi[$key] = $value;
                }
            }
            $validated['spesifikasi'] = count($spesifikasi) > 0 ? $spesifikasi : null;
        }

        // Handle warna dan size (konversi dari JSON string ke array)
        if ($request->filled('warna')) {
            $warna = is_array($request->warna) ? $request->warna : json_decode($request->warna, true);
            $validated['warna'] = !empty($warna) ? $warna : null;
        } else {
            $validated['warna'] = null;
        }

        if ($request->filled('size')) {
            $size = is_array($request->size) ? $request->size : json_decode($request->size, true);
            $validated['size'] = !empty($size) ? $size : null;
        } else {
            $validated['size'] = null;
        }

        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            // Nama file
            $filename = time() . '_' . Str::random(10);
            $extension = $file->getClientOriginalExtension();

            // Path tujuan
            $path = public_path('storage/produk/');

            // Pastikan folder ada
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Pindahkan file
            $file->move($path, $filename . '.' . $extension);

            // Simpan ke database
            $validated['gambar'] = $filename . '.' . $extension;
        }

        // Generate slug
        $validated['slug'] = Str::slug($validated['nama']);

        // Set default values jika tidak ada
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Produk::create($validated);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Produk $produk)
    {
        return view('admin.produk.show', compact('produk'));
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::active()->get();
        return view('admin.produk.edit', compact('produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        // ===== DECODE JSON WARNA & SIZE (WAJIB) =====
        if ($request->has('warna') && is_string($request->warna)) {
            $decodedWarna = json_decode($request->warna, true);
            $request->merge([
                'warna' => is_array($decodedWarna) ? $decodedWarna : []
            ]);
        }

        if ($request->has('size') && is_string($request->size)) {
            $decodedSize = json_decode($request->size, true);
            $request->merge([
                'size' => is_array($decodedSize) ? $decodedSize : []
            ]);
        }
        // Validasi dasar
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:jual,sewa,both',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_sewa_harian' => 'nullable|numeric|min:0',
            'harga_sewa_mingguan' => 'nullable|numeric|min:0',
            'harga_sewa_bulanan' => 'nullable|numeric|min:0',
            'stok_total' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0|lte:stok_total',
            'stok_disewa' => 'nullable|integer|min:0|lte:stok_total',
            'warna' => 'nullable|array',
            'warna.*' => 'string|max:50',
            'size' => 'nullable|array',
            'size.*' => 'string|max:20',
            'gambar' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Validasi harga berdasarkan tipe
        if ($validated['tipe'] === 'jual' || $validated['tipe'] === 'both') {
            $request->validate(['harga_beli' => 'required|numeric|min:0']);
        }

        if ($validated['tipe'] === 'sewa' || $validated['tipe'] === 'both') {
            $request->validate(['harga_sewa_harian' => 'required|numeric|min:0']);
        }

        // Validasi stok berdasarkan tipe
        // if ($validated['tipe'] === 'sewa' || $validated['tipe'] === 'both') {
        //     $request->validate(['stok_disewa' => 'required|integer|min:0|lte:stok_total']);
        // } else {
        //     $validated['stok_disewa'] = 0; // Set ke 0 untuk produk jual saja
        // }

        // Validasi total stok harus sama dengan stok tersedia + stok disewa (untuk produk both)
        if ($validated['tipe'] === 'both') {
            // $totalStok = $validated['stok_tersedia'] + $validated['stok_disewa'];
            // if ($totalStok > $validated['stok_total']) {
            //     return redirect()->back()
            //         ->withInput()
            //         ->withErrors(['stok_total' => 'Stok total tidak boleh kurang dari stok tersedia + stok disewa.']);
            // }
        }

        // Handle spesifikasi
        if ($request->has('spesifikasi')) {
            $spesifikasi = [];
            foreach ($request->spesifikasi as $key => $value) {
                if (!empty($key) && !empty($value)) {
                    $spesifikasi[$key] = $value;
                }
            }
            $validated['spesifikasi'] = count($spesifikasi) > 0 ? $spesifikasi : null;
        }

        // Handle warna dan size (konversi dari JSON string ke array)
        if ($request->filled('warna')) {
            $warna = is_array($request->warna) ? $request->warna : json_decode($request->warna, true);
            $validated['warna'] = !empty($warna) ? $warna : null;
        } else {
            $validated['warna'] = null;
        }

        if ($request->filled('size')) {
            $size = is_array($request->size) ? $request->size : json_decode($request->size, true);
            $validated['size'] = !empty($size) ? $size : null;
        } else {
            $validated['size'] = null;
        }

        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            if ($produk->gambar) {
                $path = public_path('storage/produk/' . $produk->gambar);
    
                if (file_exists($path)) {
                    unlink($path);
                }
            }


            // Nama file
            $filename = time() . '_' . Str::random(10);
            $extension = $file->getClientOriginalExtension();

            // Path tujuan
            $path = public_path('storage/produk/');

            // Pastikan folder ada
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Pindahkan file
            $file->move($path, $filename . '.' . $extension);

            // Simpan ke database
            $validated['gambar'] = $filename . '.' . $extension;
        }

        // Update slug if nama changed
        if ($produk->nama !== $validated['nama']) {
            $validated['slug'] = Str::slug($validated['nama']);
        }

        // Set default values jika tidak ada
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $produk->update($validated);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        // Delete image if exists
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function toggleStatus(Produk $produk)
    {
        $produk->update(['is_active' => !$produk->is_active]);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Status produk berhasil diubah.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:produks,id'
        ]);

        $produks = Produk::whereIn('id', $request->ids)->get();

        switch ($request->action) {
            case 'delete':
                foreach ($produks as $produk) {
                    if ($produk->gambar) {
                        Storage::disk('public')->delete($produk->gambar);
                    }
                    $produk->delete();
                }
                $message = 'Produk berhasil dihapus.';
                break;

            case 'activate':
                Produk::whereIn('id', $request->ids)->update(['is_active' => true]);
                $message = 'Produk berhasil diaktifkan.';
                break;

            case 'deactivate':
                Produk::whereIn('id', $request->ids)->update(['is_active' => false]);
                $message = 'Produk berhasil dinonaktifkan.';
                break;
        }

        return redirect()->route('admin.produk.index')
            ->with('success', $message);
    }
}
