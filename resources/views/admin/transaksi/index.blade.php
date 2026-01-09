{{-- resources/views/admin/transaksi/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Kelola Transaksi')
@section('page-title', 'Kelola Transaksi')
@section('page-subtitle', 'Daftar semua transaksi')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['label' => 'Transaksi']
        ];
    @endphp
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header & Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Transaksi</h1>
            <p class="text-gray-600 text-sm mt-1">Kelola semua transaksi penjualan dan penyewaan</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleFilter()" 
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            <a href="#" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-download mr-2"></i> Export
            </a>
        </div>
    </div>

    <!-- Stats Compact -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalTransactions ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-green-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center mr-3">
                    <i class="fas fa-shopping-cart text-green-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Penjualan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalPenjualan ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-purple-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-purple-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Penyewaan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalPenyewaan ?? 0) }}</p>
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe</label>
                    <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="semua" {{ request('tipe') == 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="penjualan" {{ request('tipe') == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                        <option value="penyewaan" {{ request('tipe') == 'penyewaan' ? 'selected' : '' }}>Penyewaan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @php
                            $statuses = ['semua', 'pending', 'diproses', 'dibayar', 'dikirim', 'selesai', 'dibatalkan'];
                        @endphp
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dari</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sampai</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kode atau nama..."
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">Daftar Transaksi</h3>
        </div>

        @if($transactions->count() > 0)
        <div class="overflow-x-auto -mx-4">
            <div class="inline-block min-w-full align-middle px-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap font-mono text-xs font-semibold text-gray-900">
                                    {{ $transaction->kode_transaksi ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 text-xs font-medium">
                                            {{ strtoupper(substr($transaction->user->name ?? '-', 0, 1)) }}
                                        </div>
                                        <div class="ml-2">
                                            <div class="text-xs font-medium text-gray-900 truncate max-w-[120px]">
                                                {{ $transaction->user->name ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-gray-500 truncate max-w-[120px]">
                                                {{ $transaction->user->email ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($transaction->tipe == 'penjualan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-700 border border-green-200">
                                            <i class="fas fa-shopping-cart mr-1 text-[8px]"></i> Jual
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                            <i class="fas fa-calendar-alt mr-1 text-[8px]"></i> Sewa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs font-semibold text-gray-900">
                                    Rp {{ number_format($transaction->total_bayar ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['color' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'icon' => 'fas fa-clock'],
                                            'diproses' => ['color' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fas fa-cog'],
                                            'dibayar' => ['color' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => 'fas fa-check-circle'],
                                            'dikirim' => ['color' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => 'fas fa-truck'],
                                            'selesai' => ['color' => 'bg-green-50 text-green-700 border-green-200', 'icon' => 'fas fa-check-double'],
                                            'dibatalkan' => ['color' => 'bg-red-50 text-red-700 border-red-200', 'icon' => 'fas fa-times-circle'],
                                        ];
                                        $config = $statusConfig[$transaction->status ?? ''] ?? ['color' => 'bg-gray-50 text-gray-700 border-gray-200', 'icon' => 'fas fa-question-circle'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $config['color'] }}">
                                        <i class="{{ $config['icon'] }} mr-1 text-[8px]"></i>
                                        {{ ucfirst($transaction->status ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $transaction->created_at?->format('d/m/Y') ?? '-' }}
                                    <div class="text-[10px] text-gray-500">{{ $transaction->created_at?->format('H:i') ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
                                           class="text-gray-400 hover:text-blue-600 text-sm" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.transaksi.edit', $transaction->id) }}" 
                                           class="text-gray-400 hover:text-green-600 text-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.transaksi.invoice', $transaction->id) }}" 
                                           target="_blank"
                                           class="text-gray-400 hover:text-purple-600 text-sm" title="Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
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
        <div class="text-center py-8 px-4">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                <i class="fas fa-receipt text-gray-400"></i>
            </div>
            <h4 class="text-sm font-medium text-gray-900 mb-1">Tidak ada transaksi</h4>
            <p class="text-xs text-gray-600">Tidak ditemukan transaksi yang sesuai dengan filter</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFilter() {
    const filterCard = document.getElementById('filterCard');
    filterCard.classList.toggle('hidden');
}
</script>
@endpush
