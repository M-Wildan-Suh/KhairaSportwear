{{-- resources/views/admin/transaksi/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Transaksi Penjualan')
@section('page-title', 'Transaksi Penjualan')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['label' => 'Transaksi Penjualan']
        ];
    @endphp
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header & Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-gray text-sm mt-1">Kelola semua transaksi penjualan produk</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-white bg-white hover:bg-gray-50">
                    <i class="fas fa-download mr-2"></i> Export
                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>
                
                <!-- Dropdown Export Options -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <a href="{{ route('admin.transaksi.export.excel') . '?' . http_build_query(request()->query()) }}" 
                           class="flex items-center px-4 py-2 text-sm text-white hover:bg-gray-100">
                            <i class="fas fa-file-excel text-green-500 mr-2"></i>
                            Excel (.xlsx)
                        </a>
                        <a href="{{ route('admin.transaksi.export.pdf') . '?' . http_build_query(request()->query()) }}" 
                           class="flex items-center px-4 py-2 text-sm text-white hover:bg-gray-100">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            PDF (.pdf)
                        </a>
                        <a href="{{ route('admin.transaksi.export.csv') . '?' . http_build_query(request()->query()) }}" 
                           class="flex items-center px-4 py-2 text-sm text-white hover:bg-gray-100">
                            <i class="fas fa-file-csv text-blue-500 mr-2"></i>
                            CSV (.csv)
                        </a>
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="toggleFilter()" 
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-white bg-white hover:bg-gray-50">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </div>
    </div>

    <!-- Stats Compact -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Transaksi</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalTransactions ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-green-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Selesai</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalSelesai ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-yellow-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-yellow-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Pending</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalPending ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-purple-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-purple-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Pendapatan</p>
                    <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card - Collapsible -->
    <div id="filterCard" class="bg-white rounded-lg border border-gray-200 p-4 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-900">Filter Transaksi</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.transaksi.index') }}" class="text-xs text-gray-600 hover:text-gray-900">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
                <button type="button" onclick="toggleFilter()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.transaksi.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                        <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode Bayar</label>
                    <select name="metode_bayar" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="semua" {{ request('metode_bayar') == 'semua' ? 'selected' : '' }}>Semua Metode</option>
                        <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ request('metode_bayar') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="kartu_kredit" {{ request('metode_bayar') == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                        <option value="debit" {{ request('metode_bayar') == 'debit' ? 'selected' : '' }}>Debit</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                    <div class="flex">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari kode transaksi atau nama customer..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Minimal Total</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50 text-gray-500 text-sm">
                            Rp
                        </span>
                        <input type="number" name="min_total" value="{{ request('min_total') }}" 
                               placeholder="0"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-sm font-medium text-gray-900">Daftar Transaksi Penjualan</h3>
            <div class="text-xs text-gray-500">
                {{ $transactions->total() }} transaksi ditemukan
            </div>
        </div>

        @if($transactions->count() > 0)
        <div class="overflow-x-auto -mx-4">
            <div class="inline-block min-w-full align-middle px-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-500 {{ $transaction->status == 'dibatalkan' ? 'bg-red-50' : '' }}">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="font-mono text-xs font-semibold text-gray-900">
                                        {{ $transaction->kode_transaksi }}
                                    </div>
                                    <div class="text-[10px] text-gray-500">
                                        {{ $transaction->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</p>
                                        <div class="ml-2">
                                            <div class="text-xs font-medium text-gray-900 truncate max-w-[120px]">
                                                {{ $transaction->customer_name }}
                                            </div>
                                            @if($transaction->customer_phone)
                                            <div class="text-[10px] text-gray-500 truncate max-w-[120px]">
                                                {{ $transaction->customer_phone }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-xs text-gray-900">
                                        {{ $transaction->items_count ?? 0 }} item
                                    </div>
                                    @if($transaction->note)
                                    <div class="text-[10px] text-gray-500 truncate max-w-[100px]" title="{{ $transaction->note }}">
                                        <i class="fas fa-sticky-note mr-1"></i>{{ Str::limit($transaction->note, 20) }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-900">
                                        Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                                    </div>
                                    @if($transaction->diskon > 0)
                                    <div class="text-[10px] text-red-600">
                                        Diskon: Rp {{ number_format($transaction->diskon, 0, ',', '.') }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'pending' => [
                                                'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'icon' => 'fas fa-clock',
                                                'badge' => 'Pending'
                                            ],
                                            'diproses' => [
                                                'color' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'icon' => 'fas fa-cog',
                                                'badge' => 'Diproses'
                                            ],
                                            'dibayar' => [
                                                'color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'icon' => 'fas fa-check-circle',
                                                'badge' => 'Dibayar'
                                            ],
                                            'dikirim' => [
                                                'color' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'icon' => 'fas fa-truck',
                                                'badge' => 'Dikirim'
                                            ],
                                            'selesai' => [
                                                'color' => 'bg-green-50 text-green-700 border-green-200',
                                                'icon' => 'fas fa-check-double',
                                                'badge' => 'Selesai'
                                            ],
                                            'dibatalkan' => [
                                                'color' => 'bg-red-50 text-red-700 border-red-200',
                                                'icon' => 'fas fa-times-circle',
                                                'badge' => 'Dibatalkan'
                                            ],
                                        ];
                                        $config = $statusConfig[$transaction->status] ?? $statusConfig['pending'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $config['color'] }}">
                                        <i class="{{ $config['icon'] }} mr-1 text-[8px]"></i>
                                        {{ $config['badge'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-xs text-gray-900">
                                        @php
                                            $metodeBayar = [
                                                'tunai' => 'Tunai',
                                                'transfer' => 'Transfer',
                                                'debit' => 'Kartu Debit',
                                                'kartu_kredit' => 'Kartu Kredit',
                                                'qris' => 'QRIS',
                                                'e_wallet' => 'E-Wallet'
                                            ];
                                        @endphp
                                        {{ $metodeBayar[$transaction->metode_pembayaran] ?? ucfirst($transaction->metode_pembayaran) }}
                                    </div>
                                    @if($transaction->bukti_bayar && $transaction->status != 'pending')
                                    <div class="text-[10px] text-green-600">
                                        <i class="fas fa-check-circle"></i> Terverifikasi
                                    </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $transaction->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
                                           class="p-1 text-gray-400 hover:text-blue-600" title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        
                                        <!-- TAMBAHKAN TOMBOL PRINT -->
                                        <a href="{{ route('admin.transaksi.print', $transaction->id) }}" 
                                           target="_blank"
                                           class="p-1 text-gray-400 hover:text-red-600" title="Print Struk">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                        
                                        @if($transaction->status != 'dibatalkan')
                                        <a href="{{ route('admin.transaksi.edit', $transaction->id) }}" 
                                           class="p-1 text-gray-400 hover:text-green-600" title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endif
                                        
                                        <a href="{{ route('admin.transaksi.invoice', $transaction->id) }}" 
                                           target="_blank"
                                           class="p-1 text-gray-400 hover:text-purple-600" title="Invoice">
                                            <i class="fas fa-file-invoice text-xs"></i>
                                        </a>
                                        
                                        @if($transaction->bukti_bayar)
                                        <a href="{{ Storage::url($transaction->bukti_bayar) }}" 
                                           target="_blank"
                                           class="p-1 text-gray-400 hover:text-orange-600" title="Lihat Bukti Bayar">
                                            <i class="fas fa-receipt text-xs"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-gray-700">
                    Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() ?? 0 }} transaksi
                </div>
                <div class="flex items-center gap-1">
                    {{ $transactions->withQueryString()->onEachSide(1)->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-12 px-4">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-shopping-cart text-gray-400 text-xl"></i>
            </div>
            <h4 class="text-sm font-medium text-gray-900 mb-2">Belum ada transaksi penjualan</h4>
            <p class="text-xs text-gray-600 mb-4">Mulai buat transaksi penjualan pertama Anda</p>
            <a href="{{ route('admin.transaksi.create') }}" 
               class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-lg text-xs font-medium text-white hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Buat Transaksi Baru
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Tambahkan Alpine.js untuk dropdown -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function toggleFilter() {
    const filterCard = document.getElementById('filterCard');
    filterCard.classList.toggle('hidden');
}

// Quick status update
function updateStatus(transactionId, status) {
    if (confirm('Apakah Anda yakin ingin mengubah status transaksi?')) {
        fetch(`/admin/transaksi/${transactionId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal mengubah status');
            }
        });
    }
}

// Quick print
function quickPrint(transactionId) {
    window.open(`/admin/transaksi/${transactionId}/print?autoprint=1`, '_blank');
}
</script>
@endpush