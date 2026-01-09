<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaksi->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .invoice-container { max-width: 800px; margin: 0 auto; }
        .header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info { float: left; width: 50%; }
        .invoice-info { float: right; width: 50%; text-align: right; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; }
        .signature { width: 50%; float: left; text-align: center; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ config('app.name') }}</h1>
                <p>Jl. Contoh No. 123, Kota Bandung</p>
                <p>Telp: (022) 123456 | Email: info@rentalsystem.com</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>No. Invoice:</strong> {{ $transaksi->invoice_number }}</p>
                <p><strong>Tanggal:</strong> {{ $transaksi->created_at->format('d/m/Y') }}</p>
                <p><strong>Status:</strong> {{ strtoupper($transaksi->status) }}</p>
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Customer Info -->
        <div style="margin-bottom: 30px;">
            <h3>Informasi Pelanggan</h3>
            <p><strong>Nama:</strong> {{ $transaksi->user->name ?? 'Guest' }}</p>
            <p><strong>Email:</strong> {{ $transaksi->user->email ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $transaksi->user->phone ?? '-' }}</p>
        </div>
        
        <!-- Items Table -->
        <h3>Detail Pesanan</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Tipe</th>
                    <th>Qty</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->detailTransaksis as $detail)
                <tr>
                    <td>{{ $detail->produk->nama }}</td>
                    <td>{{ strtoupper($detail->tipe) }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Subtotal:</td>
                    <td>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right;">Pajak ({{ $transaksi->tax_percentage }}%):</td>
                    <td>Rp {{ number_format($transaksi->tax_amount, 0, ',', '.') }}</td>
                </tr>
                @if($transaksi->discount_amount > 0)
                <tr>
                    <td colspan="4" style="text-align: right;">Diskon:</td>
                    <td>-Rp {{ number_format($transaksi->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td><strong>Rp {{ number_format($transaksi->total_amount, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Payment Info -->
        <div style="margin: 20px 0;">
            <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->payment_method) }}</p>
            <p><strong>Status Pembayaran:</strong> {{ strtoupper($transaksi->payment_status) }}</p>
            @if($transaksi->due_date)
            <p><strong>Jatuh Tempo:</strong> {{ $transaksi->due_date->format('d/m/Y') }}</p>
            @endif
        </div>
        
        <!-- Terms -->
        <div style="margin: 30px 0; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #ccc;">
            <p><strong>Syarat & Ketentuan:</strong></p>
            <ol style="margin-left: 20px;">
                <li>Pembayaran harus dilakukan dalam waktu 1x24 jam</li>
                <li>Barang yang sudah dibeli tidak dapat dikembalikan</li>
                <li>Invoice ini sah sebagai bukti transaksi</li>
            </ol>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="signature">
                <p>Hormat Kami,</p>
                <p style="margin-top: 50px;">___________________________</p>
                <p>{{ config('app.name') }}</p>
            </div>
            <div class="signature">
                <p>Pelanggan,</p>
                <p style="margin-top: 50px;">___________________________</p>
                <p>{{ $transaksi->user->name ?? 'Guest' }}</p>
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Print Info -->
        <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center;">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Halaman 1 dari 1</p>
        </div>
    </div>
</body>
</html>