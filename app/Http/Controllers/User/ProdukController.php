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
        $query = Produk::with('kategori')->active()->stokTersedia();
        
        // Filter by type
        if ($request->has('tipe')) {
            if ($request->tipe === 'jual') {
                $query->tipeJual();
            } elseif ($request->tipe === 'sewa') {
                $query->tipeSewa();
            }
        }
        
        // Get current kategori jika ada filter
        $currentKategori = null;
        if ($request->has('kategori')) {
            $currentKategori = Kategori::where('slug', $request->kategori)->first();
            if ($currentKategori) {
                $query->where('kategori_id', $currentKategori->id);
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sort
        $sort = $request->get('sort', 'terbaru');
        switch ($sort) {
            case 'harga_terendah':
                $query->orderByRaw('COALESCE(harga_beli, harga_sewa_harian) ASC');
                break;
            case 'harga_tertinggi':
                $query->orderByRaw('COALESCE(harga_beli, harga_sewa_harian) DESC');
                break;
            case 'nama_az':
                $query->orderBy('nama', 'ASC');
                break;
            case 'nama_za':
                $query->orderBy('nama', 'DESC');
                break;
            default:
                $query->latest();
        }
        
        $produks = $query->paginate(12);
        
        // **FIXED: Ambil kategori dengan menghitung produk aktif**
        $kategoris = Kategori::active()
            ->withCount(['produks' => function($query) {
                $query->active()->stokTersedia();
            }])
            ->get();

        // Count total jual and sewa (hanya produk aktif dengan stok)
        $jualCount = Produk::active()->stokTersedia()->tipeJual()->count();
        $sewaCount = Produk::active()->stokTersedia()->tipeSewa()->count();
        
        // Get featured products for sidebar
        $featuredProducts = Produk::with('kategori')
            ->active()
            ->stokTersedia()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $totalProducts = Produk::active()->stokTersedia()->count();
        
        // Get view preference from request or default to 'grid'
        $view = $request->get('view', 'grid');
        
        return view('user.produk.index', compact(
            'produks', 
            'kategoris', 
            'featuredProducts', 
            'totalProducts', 
            'jualCount',
            'sewaCount',
            'currentKategori',
            'view'
        ));
    }
    
    public function show($slug)
    {
        $produk = Produk::with('kategori')->where('slug', $slug)->active()->firstOrFail();
        
        // Get related products
        $relatedProducts = Produk::with('kategori')
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->active()
            ->stokTersedia()
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
            ->withCount(['produks' => function($query) {
                $query->active()->stokTersedia();
            }])
            ->get();
        
        $view = request()->get('view', 'grid');
        
        return view('user.produk.kategori', compact('produks', 'kategori', 'kategoris', 'view'));
    }
    
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $produks = Produk::with('kategori')
            ->where(function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhereHas('kategori', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      });
            })
            ->active()
            ->stokTersedia()
            ->paginate(12);
        
        // Ambil kategori dengan count untuk sidebar
        $kategoris = Kategori::active()
            ->withCount(['produks' => function($query) {
                $query->active()->stokTersedia();
            }])
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