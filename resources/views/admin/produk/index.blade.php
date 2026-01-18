@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')

@section('page-title', 'Manajemen Produk')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Produk'],
            ['label' => 'Produk']
        ];
    @endphp
@endsection

@section('content')
    <!-- Header with Actions -->
    <div class="flex md:flex-row justify-between items-start md:items-start mb-6 gap-4 mr-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Produk</h1>
            <p class="text-gray-800">Total {{ $produks->total() }} produk ditemukan</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Search -->
            <form action="{{ route('admin.produk.index') }}" method="GET" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" 
                           value="{{ request('search') }}"
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent w-64"
                           placeholder="Cari produk...">
                </div>
            </form>
            
            <!-- Filter Dropdown -->
            <div class="relative">
                <button id="filterDropdownButton" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center">
                    <i class="fas fa-filter mr-2 text-gray-600"></i>
                    <span>Filter</span>
                </button>
                
                <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="p-4 space-y-4">
                        <!-- Tipe Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Produk</label>
                            <select name="tipe" onchange="window.location.href='{{ route('admin.produk.index') }}?tipe='+this.value" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Semua Tipe</option>
                                <option value="jual" {{ request('tipe') == 'jual' ? 'selected' : '' }}>Jual</option>
                                <option value="sewa" {{ request('tipe') == 'sewa' ? 'selected' : '' }}>Sewa</option>
                                <option value="both" {{ request('tipe') == 'both' ? 'selected' : '' }}>Jual & Sewa</option>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" onchange="window.location.href='{{ route('admin.produk.index') }}?status='+this.value" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        
                        <!-- Reset Button -->
                        <div>
                            <a href="{{ route('admin.produk.index') }}" class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                Reset Filter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Product Button -->
            <a href="{{ route('admin.produk.create') }}" class="btn-admin-accent px-4 py-2 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Produk
            </a>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 mr-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mr-3">
                    <i class="fas fa-box text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($produks->total()) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Produk Aktif</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($produks->where('is_active', true)->count()) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($produks->where('stok_tersedia', '<', 5)->count()) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Sedang Disewa</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($produks->sum('stok_disewa')) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">PRODUK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">KATEGORI</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">TIPE</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">HARGA</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STOK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STATUS</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($produks as $produk)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                            <!-- Produk Info -->
                            <td class="px-2 py-1">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $produk->nama }}</div>
                                        <div class="text-xs text-gray-500 mt-1 max-w-xs">
                                            {{ $produk->deskripsi ? Str::limit($produk->deskripsi, 40) : 'Tidak ada deskripsi' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Kategori -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    <i class="fas fa-tag mr-1.5"></i>
                                    {{ $produk->kategori->nama ?? '-' }}
                                </span>
                            </td>
                            
                            <!-- Tipe -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $tipeColors = [
                                        'jual' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'sewa' => 'bg-violet-50 text-violet-700 border-violet-100',
                                        'both' => 'bg-amber-50 text-amber-700 border-amber-100'
                                    ];
                                    $tipeIcons = [
                                        'jual' => 'fas fa-shopping-cart',
                                        'sewa' => 'fas fa-calendar-alt',
                                        'both' => 'fas fa-exchange-alt'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $tipeColors[$produk->tipe] ?? 'bg-gray-50 text-gray-700 border-gray-100' }}">
                                    <i class="{{ $tipeIcons[$produk->tipe] ?? 'fas fa-box' }} mr-1.5"></i>
                                    {{ ucfirst($produk->tipe) }}
                                </span>
                            </td>
                            
                            <!-- Harga -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if(in_array($produk->tipe, ['jual', 'both']))
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                                        </div>
                                    @endif
                                    @if(in_array($produk->tipe, ['sewa', 'both']))
                                        <div class="text-xs">
                                            <span class="text-gray-600 font-medium">Sewa:</span>
                                            <span class="text-gray-500 ml-1">Rp {{ number_format($produk->harga_sewa_harian, 0, ',', '.') }}/hari</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Stok -->
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <div class="w-20 mr-3">
                                            @php
                                                $stockPercentage = $produk->stok_total > 0 ? ($produk->stok_tersedia / $produk->stok_total) * 100 : 0;
                                                $bgColor = $stockPercentage > 50 ? 'bg-emerald-500' : ($stockPercentage > 20 ? 'bg-amber-500' : 'bg-rose-500');
                                            @endphp
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $bgColor }}" style="width: {{ $stockPercentage }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium {{ $produk->stok_tersedia < 5 ? 'text-rose-600' : 'text-gray-900' }}">
                                            {{ $produk->stok_tersedia }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <span>Total: {{ $produk->stok_total }}</span>
                                        @if($produk->stok_disewa > 0)
                                            <span class="ml-2 inline-flex items-center text-violet-600">
                                                <i class="fas fa-calendar-alt mr-1 text-xs"></i>
                                                {{ $produk->stok_disewa }} disewa
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.produk.toggle-status', $produk->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="focus:outline-none transition-transform hover:scale-105">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $produk->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                                            <i class="fas fa-circle mr-1.5" style="font-size: 6px;"></i>
                                            {{ $produk->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </button>
                                </form>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.produk.show', $produk->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors"
                                       title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.produk.edit', $produk->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.produk.destroy', $produk->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="text-center">
                                    <div class="mx-auto w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum ada produk</h3>
                                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Mulai dengan menambahkan produk pertama Anda untuk dijual atau disewa.</p>
                                    <a href="{{ route('admin.produk.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg hover:from-primary-dark hover:to-primary transition-all shadow-sm hover:shadow">
                                        <i class="fas fa-plus mr-2"></i> Tambah Produk Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($produks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Menampilkan {{ $produks->firstItem() ?? 0 }} - {{ $produks->lastItem() ?? 0 }} dari {{ $produks->total() }} produk
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $produks->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .btn-admin-accent {
        background: linear-gradient(135deg, var(--accent) 0%, #ED8936 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-admin-accent:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(214, 158, 46, 0.2);
    }
    
    /* Gradient untuk primary color */
    .from-primary { --tw-gradient-from: #1A365D; }
    .to-primary-dark { --tw-gradient-to: #2C5282; }
    
    /* Warna untuk status badge */
    .bg-emerald-50 { background-color: #ecfdf5; }
    .text-emerald-700 { color: #047857; }
    .border-emerald-100 { border-color: #d1fae5; }
    
    .bg-violet-50 { background-color: #f5f3ff; }
    .text-violet-700 { color: #6d28d9; }
    .border-violet-100 { border-color: #ede9fe; }
    
    .bg-amber-50 { background-color: #fffbeb; }
    .text-amber-700 { color: #b45309; }
    .border-amber-100 { border-color: #fef3c7; }
    
    .bg-rose-50 { background-color: #fff1f2; }
    .text-rose-700 { color: #be123c; }
    .border-rose-100 { border-color: #ffe4e6; }
    
    .bg-emerald-500 { background-color: #10b981; }
    .bg-amber-500 { background-color: #f59e0b; }
    .bg-rose-500 { background-color: #f43f5e; }
    
    .text-rose-600 { color: #e11d48; }
    .text-violet-600 { color: #7c3aed; }
</style>
@endpush

@push('scripts')
<script>
    // Filter Dropdown Toggle
    document.getElementById('filterDropdownButton').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('filterDropdown').classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('filterDropdown');
        const button = document.getElementById('filterDropdownButton');
        
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Auto close dropdown when selecting filter
    document.querySelectorAll('#filterDropdown select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterDropdown').classList.add('hidden');
        });
    });
</script>
@endpush