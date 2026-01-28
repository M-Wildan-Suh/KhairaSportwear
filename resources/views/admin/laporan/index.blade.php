@extends('admin.layouts.app')

@section('title', 'Laporan Admin')

@section('page-title', 'Laporan')
@section('page-subtitle', 'Generate dan Kelola Laporan Sistem')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Laporan', 'url' => route('admin.dashboard')],
            ['label' => 'Laporan']
        ];
    @endphp
@endsection

@section('content')
    <!-- Statistics Cards untuk Laporan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Laporan -->
        <div class="admin-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Laporan</h3>
                    <p class="text-2xl font-bold text-primary mt-1">{{ number_format($totalLaporan) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Penjualan Bulan Ini -->
        <div class="admin-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Penjualan</h3>
                    <p class="text-2xl font-bold text-primary mt-1">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Penyewaan Bulan Ini -->
        <div class="admin-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Penyewaan</h3>
                    <p class="text-2xl font-bold text-primary mt-1">{{ number_format($rentalsThisMonth) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Denda Belum Bayar -->
        <div class="admin-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Denda</h3>
                    <p class="text-2xl font-bold text-primary mt-1">Rp {{ number_format($unpaidFinesTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs untuk Laporan -->
    <div class="admin-card mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6">
                <button id="tab-generate" class="tab-button active py-3 px-1 border-b-2 border-primary text-sm font-medium text-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Generate Laporan
                </button>
                <button id="tab-history" class="tab-button py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-history mr-2"></i> Histori Laporan
                </button>
            </nav>
        </div>
    </div>
    
    <!-- Tab Content: Generate Laporan -->
    <div id="tab-content-generate" class="tab-content active">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Form Laporan Penjualan -->
            <div class="admin-card">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-green-600 mr-3"></i>
                        Laporan Penjualan
                    </h3>
                    <span class="badge-success px-3 py-1 text-sm font-medium">
                        <i class="fas fa-shopping-cart mr-1"></i> Jual
                    </span>
                </div>
                
                <form action="{{ route('admin.laporan.penjualan') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-start mr-2 text-primary"></i>Tanggal Mulai
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ $defaultStartDate }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-end mr-2 text-primary"></i>Tanggal Selesai
                            </label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ $defaultEndDate }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-filter mr-2 text-primary"></i>Filter Kategori
                        </label>
                        <select name="kategori_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 btn-admin-primary flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i> Tampilkan Laporan
                        </button>
                        
                        <button type="button"
                                onclick="downloadSalesPdf()"
                                class="flex-1 btn-admin-accent flex items-center justify-center">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Form Laporan Penyewaan -->
            <div class="admin-card">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 mr-3"></i>
                        Laporan Penyewaan
                    </h3>
                    <span class="badge-info px-3 py-1 text-sm font-medium">
                        <i class="fas fa-clock mr-1"></i> Sewa
                    </span>
                </div>
                
                <form action="{{ route('admin.laporan.penyewaan') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-start mr-2 text-primary"></i>Tanggal Mulai
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ $defaultStartDate }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-end mr-2 text-primary"></i>Tanggal Selesai
                            </label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ $defaultEndDate }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-2 text-primary"></i>Status Sewa
                        </label>
                        <select name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="all">Semua Status</option>
                            <option value="ongoing">Berjalan</option>
                            <option value="completed">Selesai</option>
                            <option value="overdue">Terlambat</option>
                        </select>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 btn-admin-primary flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i> Tampilkan Laporan
                        </button>
                        
                        <button type="button"
                                onclick="downloadRentalPdf()"
                                class="flex-1 btn-admin-accent flex items-center justify-center">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="admin-card">
            <h3 class="text-xl font-bold text-gray-800 mb-6">
                <i class="fas fa-bolt mr-3 text-primary"></i>Aksi Cepat
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.transaksi.index') }}" 
                   class="border border-blue-200 rounded-lg p-4 flex items-center justify-between hover:border-blue-300 hover:bg-blue-50 transition-all">
                    <div>
                        <div class="text-blue-700 font-medium">Invoice Terbaru</div>
                        <div class="text-sm text-gray-600">Lihat dan print invoice</div>
                    </div>
                    <i class="fas fa-file-invoice text-blue-500 text-xl"></i>
                </a>
                
                <a href="{{ route('admin.produk.index') }}" 
                   class="border border-green-200 rounded-lg p-4 flex items-center justify-between hover:border-green-300 hover:bg-green-50 transition-all">
                    <div>
                        <div class="text-green-700 font-medium">Stok Produk</div>
                        <div class="text-sm text-gray-600">Cek stok dan ketersediaan</div>
                    </div>
                    <i class="fas fa-boxes text-green-500 text-xl"></i>
                </a>
                
                <div onclick="generateMonthlyReport()"
                     class="border border-yellow-200 rounded-lg p-4 flex items-center justify-between hover:border-yellow-300 hover:bg-yellow-50 transition-all cursor-pointer">
                    <div>
                        <div class="text-yellow-700 font-medium">Laporan Bulanan</div>
                        <div class="text-sm text-gray-600">Generate otomatis</div>
                    </div>
                    <i class="fas fa-file-export text-yellow-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab Content: Histori Laporan -->
    <div id="tab-content-history" class="tab-content hidden">
        <div class="admin-card">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-history mr-3 text-primary"></i>Histori Laporan
                </h3>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" 
                               id="search-reports" 
                               placeholder="Cari laporan..."
                               class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                    <button onclick="refreshReports()" class="btn-admin-primary px-4">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
            
            @if($laporans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Kode Laporan</th>
                                <th>Tipe</th>
                                <th>Periode</th>
                                <th>Tanggal</th>
                                <th>Pendapatan</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporans as $laporan)
                                <tr>
                                    <td>
                                        <div class="font-mono text-sm font-semibold text-primary">
                                            {{ $laporan->kode_laporan }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ $laporan->tipe == 'penjualan' ? 'badge-success' : ($laporan->tipe == 'penyewaan' ? 'badge-info' : 'badge-warning') }}">
                                            {{ $laporan->tipe_laporan }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-gray-700">
                                        {{ $laporan->periode_laporan }}
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $laporan->tanggal_mulai->format('d M Y') }} - 
                                        {{ $laporan->tanggal_selesai->format('d M Y') }}
                                    </td>
                                    <td class="font-semibold text-gray-900">
                                        Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $laporan->pembuat->name ?? 'System' }}
                                    </td>
                                    <td class="text-sm text-gray-500">
                                        {{ $laporan->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $laporans->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-600 text-lg mb-2">Belum ada laporan tersimpan</p>
                    <p class="text-gray-500">Generate laporan pertama Anda menggunakan form di atas</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Switching
        const tabs = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const target = this.id.replace('tab-', 'tab-content-');
                
                // Remove active classes
                tabs.forEach(t => t.classList.remove('active', 'border-primary', 'text-primary'));
                tabContents.forEach(tc => tc.classList.remove('active'));
                
                // Hide all tab contents
                tabContents.forEach(tc => tc.classList.add('hidden'));
                
                // Add active classes
                this.classList.add('active', 'border-primary', 'text-primary');
                document.getElementById(target).classList.remove('hidden');
                document.getElementById(target).classList.add('active');
            });
        });
        
        // Set default dates to today
        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('input[name="end_date"]').forEach(input => {
            if (!input.value) input.value = today;
        });
        
        // Search reports
        const searchInput = document.getElementById('search-reports');
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchReports(this.value);
                }
            });
        }
    });
    
    function downloadSalesPdf() {
        const form = document.querySelector('form[action*="penjualan"]');
        const url = form.action;
        const params = new URLSearchParams(new FormData(form));
        
        window.location.href = "{{ route('admin.laporan.penjualan.pdf') }}?" + params.toString();
    }
    
    function downloadRentalPdf() {
        const form = document.querySelector('form[action*="penyewaan"]');
        const url = form.action;
        const params = new URLSearchParams(new FormData(form));
        
        window.location.href = "{{ route('admin.laporan.penyewaan.pdf') }}?" + params.toString();
    }
    
    function generateMonthlyReport() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        const startDate = firstDay.toISOString().split('T')[0];
        const endDate = lastDay.toISOString().split('T')[0];
        
        if (confirm('Generate laporan bulanan untuk bulan ini?')) {
            // Open both reports in new tabs
            window.open(`{{ route('admin.laporan.penjualan') }}?start_date=${startDate}&end_date=${endDate}`, '_blank');
            window.open(`{{ route('admin.laporan.penyewaan') }}?start_date=${startDate}&end_date=${endDate}`, '_blank');
        }
    }
    
    function searchReports(query) {
        if (query.trim() === '') {
            window.location.href = "{{ route('admin.laporan.index') }}";
        } else {
            window.location.href = `{{ route('admin.laporan.index') }}?search=${encodeURIComponent(query)}`;
        }
    }
    
    function refreshReports() {
        location.reload();
    }
</script>
@endpush