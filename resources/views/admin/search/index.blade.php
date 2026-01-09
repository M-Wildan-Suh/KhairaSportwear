@extends('admin.layouts.app')

@section('title', 'Pencarian Admin')

@section('page-title', 'Hasil Pencarian')
@section('page-subtitle', 'Hasil pencarian untuk: "' . $query . '"')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['label' => 'Pencarian']
        ];
    @endphp
@endsection

@section('content')
    <!-- Search Box -->
    <div class="admin-card mb-6">
        <form action="{{ route('admin.search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $query }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="Cari transaksi, produk, user, sewa, atau denda..."
                        autofocus
                    >
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select name="type" class="w-full border border-gray-300 rounded-lg py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="transaksi" {{ $type == 'transaksi' ? 'selected' : '' }}>Transaksi</option>
                    <option value="produk" {{ $type == 'produk' ? 'selected' : '' }}>Produk</option>
                    <option value="user" {{ $type == 'user' ? 'selected' : '' }}>User</option>
                    <option value="sewa" {{ $type == 'sewa' ? 'selected' : '' }}>Sewa</option>
                    <option value="denda" {{ $type == 'denda' ? 'selected' : '' }}>Denda</option>
                </select>
            </div>
            
            <button type="submit" class="btn-admin-primary px-6 py-3">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
        </form>
        
        <div class="mt-4 text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            Ditemukan <span class="font-semibold">{{ $totalResults }}</span> hasil pencarian
        </div>
    </div>
    
    <!-- Search Results -->
    @if($totalResults > 0)
        <!-- Transactions Results -->
        @if(isset($results['transactions']) && count($results['transactions']) > 0)
        <div class="admin-card mb-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-receipt mr-2 text-blue-500"></i>
                Transaksi ({{ count($results['transactions']) }})
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($results['transactions'] as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-semibold">{{ $transaction->kode_transaksi }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                                        {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium">{{ $transaction->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $transaction->tipe == 'penjualan' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $transaction->tipe == 'penjualan' ? 'Penjualan' : 'Penyewaan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $transaction->status == 'selesai' ? 'bg-green-100 text-green-800' : 
                                       ($transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
                                   class="text-primary hover:text-accent"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($results['transactions']) == 10)
            <div class="mt-4 text-center">
                <a href="{{ route('admin.transaksi.index', ['q' => $query]) }}" class="text-primary hover:text-accent font-medium">
                    Lihat semua hasil transaksi <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Products Results -->
        @if(isset($results['products']) && count($results['products']) > 0)
        <div class="admin-card mb-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-box mr-2 text-green-500"></i>
                Produk ({{ count($results['products']) }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($results['products'] as $product)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start">
                        @if($product->gambar)
                        <img src="{{ asset('storage/' . $product->gambar) }}" 
                             alt="{{ $product->nama }}" 
                             class="w-16 h-16 object-cover rounded">
                        @else
                        <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        @endif
                        <div class="ml-4 flex-1">
                            <h4 class="font-medium text-gray-900">{{ $product->nama }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $product->kategori }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-sm font-semibold text-primary">
                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded-full {{ $product->stok_tersedia > 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    Stok: {{ $product->stok_tersedia }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Users Results -->
        @if(isset($results['users']) && count($results['users']) > 0)
        <div class="admin-card mb-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-users mr-2 text-purple-500"></i>
                User ({{ count($results['users']) }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($results['users'] as $user)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold text-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-medium text-gray-900">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs px-2 py-1 rounded-full {{ $user->role == 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                <span class="text-xs text-gray-500 ml-2">
                                    Bergabung: {{ $user->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Other results sections (sewa, denda) bisa ditambahkan serupa -->
        
    @else
        <!-- No Results -->
        <div class="admin-card text-center py-12">
            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ada hasil ditemukan</h3>
            <p class="text-gray-500 mb-6">Tidak ada data yang cocok dengan pencarian "<strong>{{ $query }}</strong>"</p>
            <a href="{{ route('admin.dashboard') }}" class="btn-admin-primary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    @endif
@endsection