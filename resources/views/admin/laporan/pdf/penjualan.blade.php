<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - {{ config('app.name') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .summary { margin-bottom: 20px; }
        .summary-box { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .summary-box h3 { 
            font-size: 16px; 
            margin-top: 0; 
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { 
            background-color: #f8f9fa; 
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
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p><strong>{{ config('app.name') }}</strong></p>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
        <p>Dibuat pada: {{ now()->format('d F Y H:i') }}</p>
        
        @if(request('kategori_id'))
            <p>Kategori: {{ \App\Models\Kategori::find(request('kategori_id'))->nama ?? 'Semua Kategori' }}</p>
        @endif
    </div>
    
    <!-- Summary -->
    <div class="summary">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div class="summary-box">
                <h3>Total Pendapatan</h3>
                <div style="font-size: 18px; font-weight: bold; color: #28a745;">
                    Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-box">
                <h3>Total Transaksi</h3>
                <div style="font-size: 18px; font-weight: bold; color: #007bff;">
                    {{ number_format($summary['total_transactions']) }}
                </div>
            </div>
            <div class="summary-box">
                <h3>Total Item Terjual</h3>
                <div style="font-size: 18px; font-weight: bold; color: #17a2b8;">
                    {{ number_format($summary['total_items']) }}
                </div>
            </div>
            <div class="summary-box">
                <h3>Rata-rata/Transaksi</h3>
                <div style="font-size: 18px; font-weight: bold; color: #ffc107;">
                    Rp {{ number_format($summary['average_transaction'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Methods -->
    <div style="margin-bottom: 20px;">
        <h3 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px;">Metode Pembayaran</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Metode</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Nilai</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTransactions = $summary['total_transactions'];
                    $totalAmount = $summary['total_amount'];
                @endphp
                @foreach($summary['payment_methods'] as $method => $data)
                <tr>
                    <td>{{ ucfirst($method) }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $totalTransactions > 0 ? number_format(($data['count'] / $totalTransactions) * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <td>Total</td>
                    <td>{{ $totalTransactions }}</td>
                    <td class="text-right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Top Products -->
    @if($topProducts->count() > 0)
    <div style="margin-bottom: 20px; page-break-inside: avoid;">
        <h3 style="color: #333; border-bottom: 2px solid #28a745; padding-bottom: 5px;">Produk Terlaris (Top 10)</h3>
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Produk</th>
                    <th width="15%" class="text-right">Jumlah Terjual</th>
                    <th width="20%" class="text-right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSold = $topProducts->sum('total_terjual');
                @endphp
                @foreach($topProducts as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $product->nama }}</td>
                    <td class="text-right">{{ number_format($product->total_terjual) }}</td>
                    <td class="text-right">
                        {{ $totalSold > 0 ? number_format(($product->total_terjual / $totalSold) * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Transaction Details -->
    <div class="page-break">
        <h3 style="color: #333; border-bottom: 2px solid #6c757d; padding-bottom: 5px;">Detail Transaksi</h3>
        <table class="table">
            <thead>
                <tr>
                    <th width="10%">Kode</th>
                    <th width="15%">Tanggal</th>
                    <th width="20%">Customer</th>
                    <th width="15%">Metode</th>
                    <th width="15%" class="text-right">Total</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->kode_transaksi }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->user->name ?? 'Guest' }}</td>
                    <td>{{ ucfirst($transaction->metode_pembayaran) }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                    <td>
                        @if($transaction->status == 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @elseif($transaction->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($transaction->status) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background-color: #e9ecef;">
                <tr>
                    <td colspan="4" class="text-right">Total:</td>
                    <td class="text-right">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</td>
                    <td>{{ $transactions->count() }} transaksi</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Daily Sales Chart (Text Representation) -->
    <div style="margin-top: 20px;">
        <h3 style="color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">Penjualan Harian</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-right">Jumlah Transaksi</th>
                    <th class="text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['daily_sales']->take(15) as $date => $amount)
                @php
                    $dailyTransactions = $transactions->filter(function($t) use ($date) {
                        return $t->created_at->format('Y-m-d') == $date;
                    });
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</td>
                    <td class="text-right">{{ $dailyTransactions->count() }}</td>
                    <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @if($summary['daily_sales']->count() > 15)
                <tr>
                    <td colspan="3" class="text-center">... dan {{ $summary['daily_sales']->count() - 15 }} hari lainnya</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div>Halaman <span class="page-number"></span></div>
        <div>Laporan Penjualan - {{ config('app.name') }} - {{ now()->format('d F Y H:i') }}</div>
    </div>
</body>
</html>