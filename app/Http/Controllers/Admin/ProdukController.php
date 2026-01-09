<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
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

        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            $fileName = time() . '_' . Str::slug($validated['nama']) . '.' . $request->file('gambar')->getClientOriginalExtension();
            $validated['gambar'] = $request->file('gambar')->storeAs('produk', $fileName, 'public');
        }

        // Set stok disewa default
        $validated['stok_disewa'] = $validated['stok_total'] - $validated['stok_tersedia'];

        // Generate slug
        $validated['slug'] = Str::slug($validated['nama']);

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

        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }
            
            $fileName = time() . '_' . Str::slug($validated['nama']) . '.' . $request->file('gambar')->getClientOriginalExtension();
            $validated['gambar'] = $request->file('gambar')->storeAs('produk', $fileName, 'public');
        }

        // Update stok disewa
        $validated['stok_disewa'] = $validated['stok_total'] - $validated['stok_tersedia'];

        // Update slug if nama changed
        if ($produk->nama !== $validated['nama']) {
            $validated['slug'] = Str::slug($validated['nama']);
        }

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