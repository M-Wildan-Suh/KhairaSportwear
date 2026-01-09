{{-- resources/views/admin/dashboard/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard Admin')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Dashboard']
        ];
    @endphp
@endsection

@section('content')
    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="{{ $revenueChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }} mr-1"></i>
                            {{ abs($revenueChange) }}% dari bulan lalu
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Target: Rp 50M</span>
                    <span class="font-medium text-gray-700">{{ min(100, round(($totalRevenue / 50000000) * 100)) }}%</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full mt-1">
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-600" 
                         style="width: {{ min(100, round(($totalRevenue / 50000000) * 100)) }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Total Users Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="{{ $userChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            <i class="fas fa-arrow-{{ $userChange >= 0 ? 'up' : 'down' }} mr-1"></i>
                            {{ abs($userChange) }}% dari bulan lalu
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Products Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Produk</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-green-500">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            {{ $availableProducts }} tersedia
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-box text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="text-center p-2 bg-green-50 rounded-lg">
                        <p class="font-semibold text-green-700">{{ $sportwearCount }}</p>
                        <p class="">Sportwear</p>
                    </div>
                    <div class="text-center p-2 bg-blue-50 rounded-lg">
                        <p class="font-semibold text-blue-700">{{ $rentalCount }}</p>
                        <p class="">Sewa</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Today's Activity Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($todaySalesAmount + $todayRentalsAmount) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Transaksi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-sm">
                    <div class="text-center">
                        <p class="font-semibold text-blue-600">{{ number_format($todaySalesAmount) }}</p>
                        <p class="text-gray-600 text-xs">Penjualan</p>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-purple-600">{{ number_format($todayRentalsAmount) }}</p>
                        <p class="text-gray-600 text-xs">Sewa</p>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-red-600">{{ number_format($pendingTransactions) }}</p>
                        <p class="text-gray-600 text-xs">Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Revenue Overview</h3>
                <div class="flex space-x-2">
                    <button onclick="updateChart('month')" 
                            class="px-3 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-700">
                        Bulanan
                    </button>
                    <button onclick="updateChart('week')" 
                            class="px-3 py-1 text-xs font-medium rounded-lg hover:bg-gray-100">
                        Mingguan
                    </button>
                </div>
            </div>
            <div class="h-72">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        
        <!-- Transaction Types -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-500 mb-6">Distribusi Transaksi</h3>
            <div class="h-72">
                <canvas id="transactionChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                        <span class="text-sm text-gray-500">Penjualan</span>
                    </div>
                    <span class="text-sm font-medium text-gray-500">{{ $salesPercentage }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                        <span class="text-sm text-gray-500">Sewa</span>
                    </div>
                    <span class="text-sm font-medium text-gray-500">{{ $rentalsPercentage }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                        <span class="text-sm text-gray-500">Sukses</span>
                    </div>
                    <span class="text-sm font-medium text-gray-500">{{ $successPercentage }}%</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.transaksi.create') }}" 
                   class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:shadow-md transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center mb-2">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <span class="text-sm font-medium">Transaksi Baru</span>
                    <span class="text-xs text-black mt-1">Buat sekarang</span>
                </a>
                
                <a href="{{ route('admin.produk.create') }}" 
                   class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl hover:shadow-md transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center mb-2">
                        <i class="fas fa-box text-white"></i>
                    </div>
                    <span class="text-sm font-medium ">Tambah Produk</span>
                    <span class="text-xs  mt-1">Produk baru</span>
                </a>
                
                <a href="{{ route('admin.sewa.index') }}" 
                   class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl hover:shadow-md transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center mb-2">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <span class="text-sm font-medium ">Kelola Sewa</span>
                    <span class="text-xs mt-1">{{ $activeRentalsCount }} aktif</span>
                </a>
                
                <a href="{{ route('admin.laporan.index') }}" 
                   class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl hover:shadow-md transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg bg-yellow-500 flex items-center justify-center mb-2">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <span class="text-sm font-medium ">Generate Laporan</span>
                    <span class="text-xs mt-1">Bulanan/Tahunan</span>
                </a>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Transaksi Terbaru</h3>
                <a href="{{ route('admin.transaksi.index') }}" 
                   class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->kode_transaksi }}</p>
                                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M H:i') }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</p>
                                            <p class="text-xs text-gray-500 truncate max-w-[120px]">{{ $transaction->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($transaction->tipe) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
                                            'diproses' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-cog'],
                                            'dibayar' => ['color' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check'],
                                            'dikirim' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-truck'],
                                            'selesai' => ['color' => 'bg-emerald-100 text-emerald-800', 'icon' => 'fas fa-check-double'],
                                            'dibatalkan' => ['color' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-times']
                                        ];
                                        $config = $statusConfig[$transaction->status] ?? ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-question'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $config['color'] }}">
                                        <i class="{{ $config['icon'] }} mr-1"></i>
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
                                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.transaksi.edit', $transaction->id) }}" 
                                           class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.transaksi.invoice', $transaction->id) }}" 
                                           target="_blank"
                                           class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                           title="Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($recentTransactions->isEmpty())
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-receipt text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500">Belum ada transaksi</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Low Stock & Active Rentals -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Low Stock Products -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Produk Stok Rendah</h3>
                <a href="{{ route('admin.produk.index') }}?filter=low_stock" 
                   class="text-sm font-medium text-red-600 hover:text-red-800">
                    {{ $lowStockProducts->count() }} item <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @if($lowStockProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($lowStockProducts->take(5) as $product)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-red-50 transition-colors">
                            <div class="flex items-center">
                                @if($product->gambar)
                                    <img src="{{ asset('storage/' . $product->gambar) }}" 
                                         alt="{{ $product->nama }}"
                                         class="w-10 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->kategori->nama ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center justify-end mb-1">
                                    <span class="text-xs font-medium text-red-600">{{ $product->stok_tersedia }} stok</span>
                                </div>
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $product->stok_tersedia == 0 ? 'bg-red-500' : 'bg-yellow-500' }}" 
                                         style="width: {{ min(100, ($product->stok_tersedia / ($product->stok_awal ?: 1)) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check text-green-500"></i>
                    </div>
                    <p class="text-gray-600">Semua stok produk dalam kondisi aman</p>
                </div>
            @endif
        </div>
        
        <!-- Active Rentals -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Sewa Aktif</h3>
                <a href="{{ route('admin.sewa.index') }}?status=active" 
                   class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    {{ $activeRentals->count() }} aktif <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @if($activeRentals->count() > 0)
                <div class="space-y-3">
                    @foreach($activeRentals->take(5) as $rental)
                        @php
                            $daysLeft = \Carbon\Carbon::now()->diffInDays($rental->tanggal_kembali_rencana, false);
                            $statusColor = $daysLeft < 0 ? 'text-red-600' : ($daysLeft <= 1 ? 'text-yellow-600' : 'text-green-600');
                        @endphp
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors">
                            <div class="flex items-center">
                                @if($rental->produk->gambar)
                                    <img src="{{ asset('storage/' . $rental->produk->gambar) }}" 
                                         alt="{{ $rental->produk->nama }}"
                                         class="w-10 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $rental->produk->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $rental->user->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center justify-end mb-1">
                                    <i class="fas fa-calendar-day text-gray-400 text-xs mr-1"></i>
                                    <span class="text-xs font-medium {{ $statusColor }}">
                                        @if($daysLeft < 0)
                                            {{ abs($daysLeft) }}d terlambat
                                        @elseif($daysLeft == 0)
                                            Hari ini
                                        @else
                                            {{ $daysLeft }}d lagi
                                        @endif
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rental->tanggal_kembali_rencana)->format('d M') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <p class="text-gray-600">Tidak ada sewa aktif saat ini</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Activity Feed -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Aktivitas Terbaru</h3>
        <div class="space-y-4">
            @if(count($recentActivities) > 0)
                @foreach($recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full {{ $activity['color'] }} flex items-center justify-center">
                                <i class="{{ $activity['icon'] }} text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">{{ $activity['user'] }}</span> 
                                {{ $activity['action'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                        </div>
                        @if(isset($activity['badge']))
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $activity['badgeColor'] }}">
                                {{ $activity['badge'] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-history text-gray-400"></i>
                    </div>
                    <p class="text-gray-600">Belum ada aktivitas</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .admin-card {
        @apply bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const monthlyRevenue = @json($monthlyRevenue);
        
        const months = monthlyRevenue.map(item => item.month);
        const salesData = monthlyRevenue.map(item => item.sales);
        const rentalsData = monthlyRevenue.map(item => item.rentals);
        
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Penjualan',
                        data: salesData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Sewa',
                        data: rentalsData,
                        borderColor: 'rgb(139, 92, 246)',
                        backgroundColor: 'rgba(77, 75, 81, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: Rp ${context.raw.toLocaleString('id-ID')}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                }
            }
        });
    }
    
    // Transaction Distribution Chart
    const transactionCtx = document.getElementById('transactionChart');
    if (transactionCtx) {
        new Chart(transactionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Penjualan', 'Sewa'],
                datasets: [{
                    data: [{{ $salesPercentage }}, {{ $rentalsPercentage }}],
                    backgroundColor: [
                        'rgba(243, 245, 248, 1)',
                        'rgb(139, 92, 246)'
                    ],
                    borderWidth: 2,
                    borderColor: 'white'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Chart period switcher
    window.updateChart = function(period) {
        console.log('Switch to', period + ' period');
        // Implement AJAX call to update chart data
        fetch(`/admin/dashboard/chart-data?period=${period}`)
            .then(response => response.json())
            .then(data => {
                // Update chart data
                if (revenueChart) {
                    revenueChart.data.labels = data.labels;
                    revenueChart.data.datasets[0].data = data.sales;
                    revenueChart.data.datasets[1].data = data.rentals;
                    revenueChart.update();
                }
            });
    };
    
    // Auto refresh stats every minute
    setInterval(() => {
        fetch('/admin/dashboard/quick-stats')
            .then(response => response.json())
            .then(data => {
                // Update quick stats
                document.querySelectorAll('[data-stat]').forEach(el => {
                    const stat = el.getAttribute('data-stat');
                    if (data[stat]) {
                        el.textContent = data[stat];
                    }
                });
            });
    }, 60000);
});
</script>
@endpush