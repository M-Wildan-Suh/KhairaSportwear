<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured products
        $featuredProducts = Produk::with('kategori')
            ->active()
            ->inRandomOrder()
            ->limit(6)
            ->get();
            
        // Get products for sale
        $productsForSale = Produk::with('kategori')
            ->tipeJual()
            ->active()
            ->stokTersedia()
            ->limit(8)
            ->get();
            
        // Get products for rent
        $productsForRent = Produk::with('kategori')
            ->tipeSewa()
            ->active()
            ->stokTersedia()
            ->limit(8)
            ->get();
        
        return view('home', compact('featuredProducts', 'productsForSale', 'productsForRent'));
    }
}