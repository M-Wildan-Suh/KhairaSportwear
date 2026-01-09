<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\User;
use App\Models\Sewa;
use App\Models\Denda;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        
        if (!$query) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Masukkan kata kunci pencarian');
        }
        
        $results = [];
        $totalResults = 0;
        
        // Search based on type
        switch ($type) {
            case 'transaksi':
                $results['transactions'] = $this->searchTransactions($query);
                break;
                
            case 'produk':
                $results['products'] = $this->searchProducts($query);
                break;
                
            case 'user':
                $results['users'] = $this->searchUsers($query);
                break;
                
            case 'sewa':
                $results['rentals'] = $this->searchRentals($query);
                break;
                
            case 'denda':
                $results['fines'] = $this->searchFines($query);
                break;
                
            default: // 'all'
                $results['transactions'] = $this->searchTransactions($query);
                $results['products'] = $this->searchProducts($query);
                $results['users'] = $this->searchUsers($query);
                $results['rentals'] = $this->searchRentals($query);
                $results['fines'] = $this->searchFines($query);
                break;
        }
        
        // Count total results
        foreach ($results as $category) {
            $totalResults += count($category);
        }
        
        return view('admin.search.index', compact('results', 'query', 'type', 'totalResults'));
    }
    
    private function searchTransactions($query)
    {
        return Transaksi::with('user')
            ->where(function($q) use ($query) {
                $q->where('kode_transaksi', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%")
                  ->orWhere('tipe', 'like', "%{$query}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$query}%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function searchProducts($query)
    {
        return Produk::where(function($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%")
                  ->orWhere('kode_produk', 'like', "%{$query}%")
                  ->orWhere('kategori', 'like', "%{$query}%")
                  ->orWhere('deskripsi', 'like', "%{$query}%")
                  ->orWhere('merk', 'like', "%{$query}%");
            })
            ->orderBy('nama')
            ->limit(10)
            ->get();
    }
    
    private function searchUsers($query)
    {
        return User::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
    }
    
    private function searchRentals($query)
    {
        return Sewa::with(['user', 'produk'])
            ->where(function($q) use ($query) {
                $q->where('kode_sewa', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                  })
                  ->orWhereHas('produk', function($q) use ($query) {
                      $q->where('nama', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function searchFines($query)
    {
        return Denda::with(['user', 'pengembalian.sewa'])
            ->where(function($q) use ($query) {
                $q->where('kode_denda', 'like', "%{$query}%")
                  ->orWhere('status_pembayaran', 'like', "%{$query}%")
                  ->orWhere('keterangan', 'like', "%{$query}%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}