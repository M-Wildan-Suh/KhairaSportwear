@extends('admin.layouts.app')

@section('title', 'Laporan Penyewaan')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Report Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">LAPORAN PENYEWAAN</h1>
        <p class="text-gray-600">{{ config('app.name') }}</p>
        <p class="text-gray-500">
            Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
        </p>
        <p class="text-gray-500">Dibuat pada: {{ now()->format('d F Y H:i') }}</p>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-sm text-purple-700">Total Pendapatan Sewa</div>
            <div class="text-2xl font-bold text-purple-900">Rp {{ number_format($totalRentalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-sm text-blue-700">Total Penyewaan</div>
            <div class="text-2xl font-bold text-blue-900">{{ $totalRentals }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-sm text-green-700">Produk Disewa</div>
            <div class="text-2xl font-bold text-green-900">{{ $totalItemsRented }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-sm text-yellow-700">Rata-rata Durasi</div>
            <div class="text-2xl font-bold text-yellow-900">{{ number_format($averageRentalDuration, 1) }} hari</div>
        </div>
    </div>
    
    <!-- Rental Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Berjalan</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statusCounts['ongoing'] ?? 0 }}</div>
                </div>
                <div class="text-blue-600">
                    <i class="fas fa-sync-alt text-3xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Selesai</div>
                    <div class="text-2xl font-bold text-green-600">{{ $statusCounts['completed'] ?? 0 }}</div>
                </div>
                <div class="text-green-600">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600">Terlambat</div>
                    <div class="text-2xl font-bold text-red-600">{{ $statusCounts['overdue'] ?? 0 }}</div>
                </div>
                <div class="text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rental Trends Chart -->
    @if($dailyRentals->count() > 0)
    <div class="mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Penyewaan Harian</h3>
            <div class="h-64">
                <canvas id="rentalChart"></canvas>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Most Rented Products -->
    @if($mostRentedProducts->count() > 0)
    <div class="mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Produk Paling Sering Disewa</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left">Produk</th>
                            <th class="p-3 text-left">Kategori</th>
                            <th class="p-3 text-left">Jumlah Sewa</th>
                            <th class="p-3 text-left">Total Durasi</th>
                            <th class="p-3 text-left">Pendapatan</th>
                            <th class="p-3 text-left">Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mostRentedProducts as $product)
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="p-3">
                                <div class="flex items-center">
                                    @if($product->gambar)
                                    <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama }}" 
                                         class="h-10 w-10 rounded-lg object-cover mr-3">
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $product->nama }}</div>
                                        <div class="text-sm text-gray-500">Harga: Rp {{ number_format($product->harga_sewa_harian, 0, ',', '.') }}/hari</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3">{{ $product->kategori->nama ?? '-' }}</td>
                            <td class="p-3">{{ $product->rental_count }}</td>
                            <td class="p-3">{{ $product->total_duration }} hari</td>
                            <td class="p-3 font-semibold">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                            <td class="p-3">
                                @if($product->stok_tersedia > 0)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                    {{ $product->stok_tersedia }} tersedia
                                </span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">
                                    Habis
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Rental Details -->
    <div class="mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Penyewaan</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left">Kode Sewa</th>
                            <th class="p-3 text-left">Tanggal Mulai</th>
                            <th class="p-3 text-left">Customer</th>
                            <th class="p-3 text-left">Produk</th>
                            <th class="p-3 text-left">Durasi</th>
                            <th class="p-3 text-left">Total</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Jatuh Tempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sewas as $sewa)
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="p-3 font-medium text-purple-600">
                                <a href="{{ route('admin.sewa.show', $sewa->id) }}" class="hover:underline">
                                    {{ $sewa->kode_sewa }}
                                </a>
                            </td>
                            <td class="p-3">{{ $sewa->tanggal_mulai->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $sewa->user->name ?? 'Guest' }}</td>
                            <td class="p-3">
                                <div class="flex items-center">
                                    @if($sewa->produk->gambar)
                                    <img src="{{ asset('storage/' . $sewa->produk->gambar) }}" alt="{{ $sewa->produk->nama }}" 
                                         class="h-8 w-8 rounded-lg object-cover mr-2">
                                    @endif
                                    <span>{{ $sewa->produk->nama }}</span>
                                </div>
                            </td>
                            <td class="p-3">
                                {{ $sewa->durasi }} hari
                            </td>
                            <td class="p-3 font-semibold">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-bold 
                                    @if($sewa->status == 'selesai') bg-green-100 text-green-800
                                    @elseif($sewa->status == 'aktif') bg-blue-100 text-blue-800
                                    @elseif($sewa->status == 'terlambat') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ strtoupper($sewa->status) }}
                                </span>
                            </td>
                            <td class="p-3 {{ $sewa->hitungKeterlambatan() > 0 ? 'text-red-600 font-bold' : '' }}">
                                {{ $sewa->tanggal_kembali_rencana->format('d/m/Y') }}
                                @if($sewa->hitungKeterlambatan() > 0)
                                <br><small class="text-red-500">(terlambat {{ $sewa->hitungKeterlambatan() }} hari)</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($sewas->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $sewas->links() }}
            </div>
            @endif
        </div>
    </div>
    
    <!-- Overdue Rentals -->
    @if($overdueRentals->count() > 0)
    <div class="mb-8">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-900 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Penyewaan Terlambat
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-red-100">
                            <th class="p-3 text-left">Kode Sewa</th>
                            <th class="p-3 text-left">Customer</th>
                            <th class="p-3 text-left">Produk</th>
                            <th class="p-3 text-left">Jatuh Tempo</th>
                            <th class="p-3 text-left">Keterlambatan</th>
                            <th class="p-3 text-left">Estimasi Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueRentals as $sewa)
                        <tr class="border-t border-red-200">
                            <td class="p-3 font-medium">{{ $sewa->kode_sewa }}</td>
                            <td class="p-3">{{ $sewa->user->name ?? 'Guest' }}</td>
                            <td class="p-3">{{ $sewa->produk->nama }}</td>
                            <td class="p-3 font-bold">{{ $sewa->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td class="p-3 text-red-600 font-bold">{{ $sewa->hitungKeterlambatan() }} hari</td>
                            <td class="p-3 font-bold">Rp {{ number_format($sewa->hitungDenda(), 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-red-50">
                        <tr>
                            <td colspan="5" class="p-3 text-right font-bold">Total Potensi Denda:</td>
                            <td class="p-3 font-bold text-red-700">
                                Rp {{ number_format($overdueRentals->sum('hitungDenda'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Summary -->
    <div class="bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analisis Penyewaan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Statistik Durasi</h4>
                <ul class="space-y-2">
                    <li class="flex justify-between">
                        <span>Durasi Sewa Terpendek:</span>
                        <span class="font-semibold">{{ $durationAnalysis['min_duration'] ?? 0 }} hari</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Durasi Sewa Terpanjang:</span>
                        <span class="font-semibold">{{ $durationAnalysis['max_duration'] ?? 0 }} hari</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Rata-rata Durasi Sewa:</span>
                        <span>{{ number_format($durationAnalysis['average_duration'] ?? 0, 1) }} hari</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Modus Durasi Sewa:</span>
                        <span>{{ $durationAnalysis['most_common_duration'] ?? 0 }} hari</span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Analisis Keuangan</h4>
                <ul class="space-y-2">
                    <li class="flex justify-between">
                        <span>Pendapatan Sewa/Hari:</span>
                        <span class="font-semibold">Rp {{ number_format($financialAnalysis['revenue_per_day'] ?? 0, 0, ',', '.') }}</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Rata-rata Sewa/Transaksi:</span>
                        <span>Rp {{ number_format($financialAnalysis['average_rental_value'] ?? 0, 0, ',', '.') }}</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Total Denda Diterima:</span>
                        <span class="font-semibold">Rp {{ number_format($financialAnalysis['total_penalties'] ?? 0, 0, ',', '.') }}</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Tingkat Pengembalian Tepat Waktu:</span>
                        <span class="font-bold {{ ($financialAnalysis['on_time_return_rate'] ?? 0) >= 90 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($financialAnalysis['on_time_return_rate'] ?? 0, 1) }}%
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Recommendations -->
    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="font-semibold text-blue-900 mb-2">
            <i class="fas fa-lightbulb mr-2"></i>Rekomendasi
        </h4>
        <ul class="list-disc pl-5 text-sm text-blue-800 space-y-1">
            @if($overdueRentals->count() > 0)
            <li>Segera hubungi {{ $overdueRentals->count() }} customer yang terlambat mengembalikan barang.</li>
            @endif
            @if(($statusCounts['ongoing'] ?? 0) > 10)
            <li>Terdapat {{ $statusCounts['ongoing'] ?? 0 }} penyewaan aktif. Periksa stok ketersediaan produk.</li>
            @endif
            @if($mostRentedProducts->isNotEmpty())
            <li>"{{ $mostRentedProducts->first()->nama }}" adalah produk paling populer. Pertimbangkan untuk menambah stok.</li>
            @endif
            @if(($financialAnalysis['on_time_return_rate'] ?? 0) < 90)
            <li>Tingkat pengembalian tepat waktu {{ number_format($financialAnalysis['on_time_return_rate'] ?? 0, 1) }}% perlu ditingkatkan.</li>
            @endif
        </ul>
    </div>
</div>

@if($dailyRentals->count() > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Rental Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rentalChart').getContext('2d');
        
        // Data dari controller
        const dailyRentals = @json($dailyRentals);
        const labels = dailyRentals.map(item => item.date);
        const counts = dailyRentals.map(item => item.count);
        const revenues = dailyRentals.map(item => item.revenue);
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Jumlah Sewa',
                        data: counts,
                        backgroundColor: 'rgba(99, 102, 241, 0.6)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Pendapatan (Rp)',
                        data: revenues,
                        backgroundColor: 'rgba(16, 185, 129, 0.3)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 2,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Jumlah Sewa'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Pendapatan (Rp)'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
    
    // Auto print if needed
    @if(request()->has('print'))
        setTimeout(() => {
            window.print();
        }, 1000);
    @endif
</script>
@endpush
@endif
@endsection