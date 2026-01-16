{{-- resources/views/admin/transaksi/print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $transaksi->kode_transaksi }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* PRINT STYLES */
        @media print {
            @page {
                size: 80mm auto; /* Untuk struk thermal */
                margin: 2mm;
            }
            
            body {
                font-family: 'Courier New', monospace;
                font-size: 11px;
                line-height: 1.2;
                width: 76mm;
                margin: 0;
                padding: 0;
            }
            
            .no-print, .print-btn {
                display: none !important;
            }
            
            .break-after {
                page-break-after: always;
            }
        }
        
        /* SCREEN STYLES */
        @media screen {
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                max-width: 210mm; /* A4 */
                margin: 20px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            
            .print-container {
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border-radius: 8px;
            }
            
            .print-btn {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                padding: 10px 20px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            
            .print-btn:hover {
                background: #0056b3;
            }
        }
        
        /* COMMON STYLES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .underline { border-bottom: 1px dashed #000; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .pb-1 { padding-bottom: 5px; }
        .pb-2 { padding-bottom: 10px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 4px 2px;
            text-align: left;
        }
        
        .border-top {
            border-top: 1px solid #000;
        }
        
        .border-bottom {
            border-bottom: 1px solid #000;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">
        <i class="fas fa-print"></i> Cetak Struk
    </button>
    
    <div class="print-container">
        <!-- HEADER -->
        <div class="text-center mb-2">
            <div class="bold" style="font-size: 14px;">{{ config('app.name', 'Toko Saya') }}</div>
            <div style="font-size: 10px;">{{ config('app.address', 'Jl. Contoh No. 123') }}</div>
            <div style="font-size: 10px;">Telp: {{ config('app.phone', '0812-3456-7890') }}</div>
        </div>
        
        <div class="border-top pb-1 mb-1"></div>
        
        <!-- TRANSACTION INFO -->
        <table>
            <tr>
                <td>No. Transaksi</td>
                <td class="text-right bold">{{ $transaksi->kode_transaksi }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-right">{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td class="text-right">{{ $transaksi->user->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td>Customer</td>
                <td class="text-right">{{ $transaksi->customer_name ?? $transaksi->user->name ?? 'Walk-in Customer' }}</td>
            </tr>
            @if($transaksi->customer_phone)
            <tr>
                <td>Telp</td>
                <td class="text-right">{{ $transaksi->customer_phone }}</td>
            </tr>
            @endif
        </table>
        
        <div class="border-top pb-1 mt-1 mb-1"></div>
        
        <!-- ITEMS -->
        <table>
            <thead>
                <tr class="border-bottom">
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->detailTransaksis as $item)
                <tr>
                    <td>{{ $item->produk->nama ?? 'Produk' }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="border-top pb-1 mt-1 mb-1"></div>
        
        <!-- TOTALS -->
        <table class="mt-2">
            @php
                // Hitung pajak 11% dari total_harga
                $pajak = $transaksi->total_harga * 0.11;
                $subtotal = $transaksi->total_harga;
                $diskon = $transaksi->diskon ?? 0;
                $totalSetelahDiskon = $subtotal - $diskon;
                $totalBayar = $transaksi->total_bayar;
            @endphp
            
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            
            @if($diskon > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
            </tr>
            
            <tr>
                <td>Setelah Diskon</td>
                <td class="text-right">Rp {{ number_format($totalSetelahDiskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <tr>
                <td>Pajak (11%)</td>
                <td class="text-right">Rp {{ number_format($pajak, 0, ',', '.') }}</td>
            </tr>
            
            <tr class="border-top bold">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
            </tr>
            
            <tr>
                <td>Metode Bayar</td>
                <td class="text-right">{{ strtoupper(str_replace('_', ' ', $transaksi->metode_pembayaran ?? 'tunai')) }}</td>
            </tr>
            
            @if($transaksi->metode_pembayaran == 'tunai')
            <tr>
                <td>Status</td>
                <td class="text-right">LANGSUNG BAYAR</td>
            </tr>
            @endif
        </table>
        
        <div class="border-top pb-1 mt-1 mb-1"></div>
        
        <!-- PAYMENT & FOOTER -->
        <div class="text-center mt-2">
            <div style="font-size: 10px; margin-top: 10px;">Terima kasih telah berbelanja</div>
            <div style="font-size: 9px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
        </div>
        
        <!-- QR Code untuk digital receipt -->
        <div class="text-center mt-2" style="page-break-inside: avoid;">
            <div style="font-size: 8px;">ID Transaksi:</div>
            <div style="font-size: 7px; font-family: monospace;">{{ $transaksi->kode_transaksi }}</div>
            <div style="font-size: 7px; margin-top: 5px;">{{ $transaksi->created_at->format('d/m/Y H:i') }}</div>
        </div>
        
        <!-- Catatan jika ada -->
        @if($transaksi->catatan)
        <div class="mt-2 pt-2 border-top">
            <div style="font-size: 8px;"><strong>Catatan:</strong> {{ $transaksi->catatan }}</div>
        </div>
        @endif
    </div>
    
    <script>
        // Auto print jika parameter ?autoprint=1
        @if(request()->has('autoprint'))
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        @endif
        
        // Close window setelah print (opsional)
        window.onafterprint = function() {
            @if(request()->has('autoprint'))
            setTimeout(function() {
                window.close();
            }, 1000);
            @endif
        };
    </script>
</body>
</html>