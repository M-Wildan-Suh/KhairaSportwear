@extends('admin.layouts.master')

@section('title', 'Dashboard Admin - SportWear')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-flex">
            <a href="{{ route('admin.laporan.create') }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Laporan
            </a>
            <span class="text-muted">Last updated: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalProducts) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Penjualan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Rentals Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Sewa Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayRentals }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pendapatan 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="font-weight-bold">Produk Stok Rendah:</span>
                        <span class="badge badge-danger ml-2">{{ $lowStockProducts->count() }}</span>
                        @if($lowStockProducts->count() > 0)
                        <ul class="mt-2">
                            @foreach($lowStockProducts->take(3) as $product)
                            <li>{{ $product->nama }} ({{ $product->stok_tersedia }} stok)</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Sewa Aktif:</span>
                        <span class="badge badge-info ml-2">{{ $activeRentals->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Denda Belum Dibayar:</span>
                        <span class="badge badge-warning ml-2">{{ $unpaidFines->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>User</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->kode_transaksi }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                                    <td>{!! $transaction->status_badge !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Rentals -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sewa Aktif</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Produk</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeRentals as $rental)
                                <tr>
                                    <td>{{ $rental->kode_sewa }}</td>
                                    <td>{{ $rental->produk->nama }}</td>
                                    <td>{{ $rental->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                                    <td>{!! $rental->status_badge !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Revenue Chart
    const revenueChart = document.getElementById('revenueChart').getContext('2d');
    const revenueData = {
        labels: @json(array_column($monthlyRevenue, 'month')),
        datasets: [{
            label: 'Penjualan',
            data: @json(array_column($monthlyRevenue, 'sales')),
            backgroundColor: 'rgba(78, 115, 223, 0.5)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 2
        }, {
            label: 'Penyewaan',
            data: @json(array_column($monthlyRevenue, 'rentals')),
            backgroundColor: 'rgba(54, 185, 204, 0.5)',
            borderColor: 'rgba(54, 185, 204, 1)',
            borderWidth: 2
        }, {
            label: 'Denda',
            data: @json(array_column($monthlyRevenue, 'fines')),
            backgroundColor: 'rgba(231, 74, 59, 0.5)',
            borderColor: 'rgba(231, 74, 59, 1)',
            borderWidth: 2
        }]
    };

    new Chart(revenueChart, {
        type: 'line',
        data: revenueData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush