<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penyewaan - {{ config('app.name') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .summary { margin-bottom: 20px; }
        .summary-grid { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 15px; 
            margin-bottom: 20px;
        }
        .summary-box { 
            border: 1px solid #ddd; 
            padding: 15px; 
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .summary-box h3 { 
            font-size: 14px; 
            margin-top: 0; 
            margin-bottom: 10px;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .summary-value { 
            font-size: 18px; 
            font-weight: bold; 
            text-align: center;
        }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { 
            background-color: #6c757d; 
            color: white;
            border: 1px solid #dee2e6; 
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        .table td { 
            border: 1px solid #dee2e6; 
            padding: 8px;
        }
        .table tr:nth-child(even) { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-primary { background-color: #cce5ff; color: #004085; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .page-break { page-break-after: always; }
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .page-number:after { content: counter(page); }
        .status-box {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-aktif { background-color: #d1ecf1; color: #0c5460; }
        .status-selesai { background-color: #d4edda; color: #155724; }
        .status-terlambat { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENYEWAAN</h1>
        <p><strong>{{ config('app.name') }}</strong></p>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
        <p>Dibuat pada: {{ now()->format('d F Y H:i') }}</p>
        
        @if(request('status') && request('status') != 'all')
            <p>Status: 
                @if(request('status') == 'ongoing') Berjalan
                @elseif(request('status') == 'completed') Selesai
                @elseif(request('status') == 'overdue') Terlambat
                @elseif(request('status') == 'cancelled') Dibatalkan
                @endif
            </p>
        @endif
    </div>
    
    <!-- Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-box">
                <h3>Total Pendapatan Sewa</h3>
                <div class="summary-value" style="color: #28a745;">
                    Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-box">
                <h3>Total Penyewaan</h3>
                <div class="summary-value" style="color: #007bff;">
                    {{ number_format($summary['total_rentals']) }}
                </div>
            </div>
            <div class="summary-box">
                <h3>Total Hari Sewa</h3>
                <div class="summary-value" style="color: #17a2b8;">
                    {{ number_format($summary['total_days']) }}
                </div>
            </div>
            <div class="summary-box">
                <h3>Rata-rata Durasi</h3>
                <div class="summary-value" style="color: #ffc107;">
                    {{ number_format($summary['average_duration'], 1) }} hari
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Overview -->
    <div style="margin-bottom: 20px;">
        <h3 style="color: #333; border-bottom: 2px solid #6c757d; padding-bottom: 5px;">Status Penyewaan</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRentals = $summary['total_rentals'];
                @endphp
                @foreach($summary['status_count'] as $status => $count)
                <tr>
                    <td>
                        @if($status == 'aktif')
                            <span class="status-box status-aktif">Berjalan</span>
                        @elseif($status == 'selesai')
                            <span class="status-box status-selesai">Selesai</span>
                        @elseif($status == 'terlambat')
                            <span class="status-box status-terlambat">Terlambat</span>
                        @else
                            <span class="status-box">{{ ucfirst($status) }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($count) }}</td>
                    <td class="text-right">
                        {{ $totalRentals > 0 ? number_format(($count / $totalRentals) * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <td>Total</td>
                    <td class="text-right">{{ number_format($totalRentals) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Top Products -->
    @if($topProducts->count() > 0)
    <div style="margin-bottom: 20px; page-break-inside: avoid;">
        <h3 style="color: #333; border-bottom: 2px solid #28a745; padding-bottom: 5px;">Produk Paling Sering Disewa</h3>
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Produk</th>
                    <th width="15%" class="text-right">Jumlah Sewa</th>
                    <th width="20%" class="text-right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRented = $topProducts->sum('total_disewa');
                @endphp
                @foreach($topProducts as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $product->nama }}</td>
                    <td class="text-right">{{ number_format($product->total_disewa) }}</td>
                    <td class="text-right">
                        {{ $totalRented > 0 ? number_format(($product->total_disewa / $totalRented) * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Rental Details -->
    <div class="page-break">
        <h3 style="color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">Detail Penyewaan</h3>
        <table class="table">
            <thead>
                <tr>
                    <th width="12%">Kode Sewa</th>
                    <th width="12%">Tanggal Mulai</th>
                    <th width="15%">Customer</th>
                    <th width="20%">Produk</th>
                    <th width="8%" class="text-center">Durasi</th>
                    <th width="15%" class="text-right">Total</th>
                    <th width="10%">Status</th>
                    <th width="8%">Jatuh Tempo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sewas as $sewa)
                <tr>
                    <td>{{ $sewa->kode_sewa }}</td>
                    <td>{{ $sewa->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>{{ $sewa->user->name ?? 'Guest' }}</td>
                    <td>{{ $sewa->produk->nama }}</td>
                    <td class="text-center">{{ $sewa->durasi }} hari</td>
                    <td class="text-right">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</td>
                    <td>
                        @if($sewa->status == 'aktif')
                            <span class="status-box status-aktif">Berjalan</span>
                        @elseif($sewa->status == 'selesai')
                            <span class="status-box status-selesai">Selesai</span>
                        @elseif($sewa->status == 'terlambat')
                            <span class="status-box status-terlambat">Terlambat</span>
                        @else
                            <span class="status-box">{{ ucfirst($sewa->status) }}</span>
                        @endif
                    </td>
                    <td class="{{ $sewa->hitungKeterlambatan() > 0 ? 'text-danger' : '' }}">
                        {{ $sewa->tanggal_kembali_rencana->format('d/m/Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background-color: #e9ecef;">
                <tr>
                    <td colspan="5" class="text-right">Total:</td>
                    <td class="text-right">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</td>
                    <td colspan="2">{{ $sewas->count() }} penyewaan</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Daily Rentals -->
    <div style="margin-top: 20px;">
        <h3 style="color: #333; border-bottom: 2px solid #fd7e14; padding-bottom: 5px;">Penyewaan Harian</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-right">Jumlah Sewa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['daily_rentals']->take(15) as $date => $count)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</td>
                    <td class="text-right">{{ number_format($count) }}</td>
                </tr>
                @endforeach
                @if($summary['daily_rentals']->count() > 15)
                <tr>
                    <td colspan="2" class="text-center">... dan {{ $summary['daily_rentals']->count() - 15 }} hari lainnya</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <!-- Notes -->
    <div style="margin-top: 30px; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; background-color: #f8f9fa;">
        <h4 style="margin-top: 0; color: #495057;">Catatan:</h4>
        <ul style="margin-bottom: 0; padding-left: 20px;">
            <li>Laporan ini mencakup semua penyewaan dalam periode yang ditentukan</li>
            <li>Data diambil pada {{ now()->format('d F Y H:i') }}</li>
            <li>Total potensi denda: Rp {{ number_format($sewas->sum('denda'), 0, ',', '.') }}</li>
            <li>Total penyewaan aktif: {{ $sewas->where('status', 'aktif')->count() }}</li>
            <li>Total penyewaan terlambat: {{ $sewas->where('status', 'terlambat')->count() }}</li>
        </ul>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div>Halaman <span class="page-number"></span></div>
        <div>Laporan Penyewaan - {{ config('app.name') }} - {{ now()->format('d F Y H:i') }}</div>
    </div>
</body>
</html>