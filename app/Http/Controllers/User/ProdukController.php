<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $produks = Produk::with('kategori')
            ->tipeJual()
            ->active()
            ->stokTersedia()
            ->paginate(12);

        // Get featured rental products
        $featuredProducts = Produk::with('kategori')
            ->tipeJual()
            ->active()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $kategoris = \App\Models\Kategori::active()->get();

        return view('user.produk.index', compact(
            'produks',
            'featuredProducts',
            'kategoris'
        ));
    }

    public function show($slug)
    {
        $produk = Produk::with('kategori')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $produk = Produk::with([
                'kategori',
                'gambarTambahan' => function ($query) {
                    $query->orderBy('urutan', 'asc');
                },
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related products
        $relatedProducts = Produk::with('kategori')
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->active()
            ->stokTersedia()
            ->limit(4)
            ->get();

        $relatedProducts = Produk::where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('user.produk.show', compact('produk', 'relatedProducts'));
    }

    public function byKategori($slug)
    {
        $kategori = Kategori::where('slug', $slug)->firstOrFail();

        $produks = Produk::with('kategori')
            ->where('kategori_id', $kategori->id)
            ->active()
            ->stokTersedia()
            ->paginate(12);

        // Ambil semua kategori dengan count
        $kategoris = Kategori::active()
            ->withCount([
                'produks' => function ($query) {
                    $query->active()->stokTersedia();
                },
            ])
            ->get();

        $view = request()->get('view', 'grid');

        return view('user.produk.kategori', compact(
            'produks',
            'kategori',
            'kategoris',
            'view'
        ));
    }

    public function search(Request $request)
    {
        $search = $request->get('q');

        $produks = Produk::with('kategori')
            ->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->active()
            ->stokTersedia()
            ->paginate(12);

        // Ambil kategori dengan count untuk sidebar
        $kategoris = Kategori::active()
            ->withCount([
                'produks' => function ($query) {
                    $query->active()->stokTersedia();
                },
            ])
            ->get();

        // Hitung untuk sidebar
        $jualCount = Produk::active()->stokTersedia()->tipeJual()->count();
        $sewaCount = Produk::active()->stokTersedia()->tipeSewa()->count();

        $featuredProducts = Produk::with('kategori')
            ->active()
            ->stokTersedia()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $view = $request->get('view', 'grid');

        return view('user.produk.search', compact(
            'produks',
            'search',
            'view',
            'kategoris',
            'jualCount',
            'sewaCount',
            'featuredProducts'
        ));
    }
}