@extends('admin.layouts.app')

@section('title', 'Laporan Penjualan')

@section('page-title', 'Laporan Penjualan')
@section('page-subtitle', 'Rekap transaksi penjualan')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Laporan', 'url' => route('admin.laporan.index')],
            ['label' => 'Penjualan']
        ];
    @endphp
@endsection

@section('content')
    <div class="admin-card mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-3 text-green-600"></i>Filter Laporan
        </h3>
        
        <form action="{{ route('admin.laporan.penjualan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full btn-admin-primary">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="admin-card">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Total Penjualan</h4>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</p>
        </div>
        
        <div class="admin-card">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Total Transaksi</h4>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['total_transactions']) }}</p>
        </div>
        
        <div class="admin-card">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Total Item Terjual</h4>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($summary['total_items']) }}</p>
        </div>
        
        <div class="admin-card">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Rata-rata Transaksi</h4>
            <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($summary['average_transaction'], 0, ',', '.') }}</p>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="admin-card mb-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-shopping-cart mr-3"></i>Transaksi Penjualan
            </h3>
            <div class="flex space-x-2">
                <a href="{{ route('admin.laporan.penjualan.pdf') . '?' . http_build_query(request()->all()) }}" 
                   class="btn-admin-accent">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                {{-- <a href="{{ route('admin.laporan.export.excel', 'penjualan') . '?' . http_build_query(request()->all()) }}" 
                   class="btn-admin-primary">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a> --}}
            </div>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="admin-card">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Metode Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="font-mono text-sm">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $transaction->user->name }}</td>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $transaction->items->sum('jumlah') }}</td>
                                <td class="font-semibold">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                                <td>
                                    <span class="admin-badge badge-info">
                                        {{ $transaction->metode_pembayaran }}
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-badge badge-success">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-600 text-lg">Tidak ada transaksi penjualan pada periode ini</p>
            </div>
        @endif
    </div>
    
    <!-- Top Products -->
    @if($topProducts->count() > 0)
        <div class="admin-card mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-star mr-3 text-yellow-500"></i>Produk Terlaris
            </h3>
            
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Produk</th>
                            <th>Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $index => $product)
                            <tr>
                                <td class="text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>{{ $product->nama }}</td>
                                <td class="font-semibold">{{ number_format($product->total_terjual) }} unit</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection