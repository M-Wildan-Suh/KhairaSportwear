@extends('admin.layouts.report')

@section('title', 'Invoice #' . $transaksi->kode_transaksi)

@section('content')
<div class="invoice-container bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
    <!-- Invoice Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12">
                @else
                    <div class="h-12 w-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">{{ substr(config('app.name'), 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ config('app.name', 'Sistem Rental') }}</h1>
                    <p class="text-gray-600">Invoice Transaksi</p>
                </div>
            </div>
            <div class="text-gray-600">
                <p><i class="fas fa-map-marker-alt mr-2"></i>{{ config('app.address', 'Jl. Contoh No. 123, Kota') }}</p>
                <p><i class="fas fa-phone mr-2"></i>{{ config('app.phone', '(022) 123-4567') }}</p>
                <p><i class="fas fa-envelope mr-2"></i>{{ config('app.email', 'info@example.com') }}</p>
            </div>
        </div>
        
        <div class="text-right">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">INVOICE</h2>
            <div class="bg-cyan-600 text-white px-4 py-2 rounded-lg inline-block">
                <div class="text-sm">No. Transaksi</div>
                <div class="text-xl font-bold">{{ $transaksi->kode_transaksi }}</div>
            </div>
            <div class="mt-4 text-gray-600">
                <p>Tanggal: {{ $transaksi->created_at->format('d/m/Y H:i') }}</p>
                <p>Status: 
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'diproses' => 'bg-blue-100 text-blue-800',
                            'dibayar' => 'bg-green-100 text-green-800',
                            'dikirim' => 'bg-purple-100 text-purple-800',
                            'selesai' => 'bg-green-100 text-green-800',
                            'dibatalkan' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    <span class="px-2 py-1 rounded text-sm font-bold {{ $statusColors[$transaksi->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ strtoupper($transaksi->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Customer Info -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-user mr-2"></i>Informasi Pelanggan
            </h3>
            <div class="space-y-1">
                <p><strong>Nama:</strong> {{ $transaksi->customer_name ?? $transaksi->user->name ?? 'Customer' }}</p>
                <p><strong>Email:</strong> {{ $transaksi->customer_email ?? $transaksi->user->email ?? '-' }}</p>
                <p><strong>Telepon:</strong> {{ $transaksi->customer_phone ?? $transaksi->user->phone ?? '-' }}</p>
                @if($transaksi->customer_address)
                <p><strong>Alamat:</strong> {{ $transaksi->customer_address }}</p>
                @endif
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-file-invoice mr-2"></i>Informasi Pembayaran
            </h3>
            <div class="space-y-1">
                <p><strong>Metode Pembayaran:</strong> {{ ucfirst(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}</p>
                @if($transaksi->tanggal_pembayaran)
                <p><strong>Tanggal Bayar:</strong> {{ $transaksi->tanggal_pembayaran->format('d/m/Y H:i') }}</p>
                @endif
                @if($transaksi->bukti_pembayaran)
                <p><strong>Bukti Bayar:</strong> 
                    <a href="{{ Storage::url($transaksi->bukti_pembayaran) }}" target="_blank" class="text-cyan-600 hover:underline">
                        Lihat Bukti
                    </a>
                </p>
                @endif
                @if($transaksi->nama_bank || $transaksi->no_rekening)
                <div class="mt-2 pt-2 border-t border-gray-200">
                    <p class="font-semibold text-sm">Transfer ke:</p>
                    @if($transaksi->nama_bank)
                    <p><strong>Bank:</strong> {{ $transaksi->nama_bank }}</p>
                    @endif
                    @if($transaksi->no_rekening)
                    <p><strong>No. Rekening:</strong> {{ $transaksi->no_rekening }}</p>
                    @endif
                    @if($transaksi->atas_nama)
                    <p><strong>Atas Nama:</strong> {{ $transaksi->atas_nama }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Items Table -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-shopping-cart mr-2"></i>Detail Pesanan
        </h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 p-3 text-left">Produk</th>
                    <th class="border border-gray-300 p-3 text-left">Tipe</th>
                    <th class="border border-gray-300 p-3 text-left">Qty</th>
                    <th class="border border-gray-300 p-3 text-left">Harga Satuan</th>
                    <th class="border border-gray-300 p-3 text-left">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->detailTransaksis as $detail)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 p-3">
                        <div class="flex items-center">
                            @if($detail->produk->gambar)
                            <img src="{{ Storage::url($detail->produk->gambar) }}" alt="{{ $detail->produk->nama }}" 
                                 class="h-10 w-10 rounded-lg object-cover mr-3">
                            @endif
                            <div>
                                <div class="font-medium">{{ $detail->produk->nama }}</div>
                                @if($detail->produk->sku)
                                <div class="text-sm text-gray-500">{{ $detail->produk->sku }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="border border-gray-300 p-3">
                        @if($transaksi->tipe == 'penjualan')
                        <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-800">
                            PENJUALAN
                        </span>
                        @else
                        <span class="px-2 py-1 rounded text-xs font-bold bg-purple-100 text-purple-800">
                            PENYEWAAN
                        </span>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-3">{{ $detail->quantity }}</td>
                    <td class="border border-gray-300 p-3">
                        Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                        @if($detail->opsi_sewa && is_string($detail->opsi_sewa))
                            @php
                                $opsi = json_decode($detail->opsi_sewa, true);
                            @endphp
                            @if($opsi && isset($opsi['durasi']))
                                <div class="text-xs text-gray-500">{{ $opsi['durasi'] }} hari</div>
                            @endif
                        @endif
                    </td>
                    <td class="border border-gray-300 p-3 font-semibold">
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Summary -->
    <div class="flex justify-end">
        <div class="w-80">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-2 mb-4">
                    @php
                        // Hitung subtotal dari detail transaksi
                        $subtotal = $transaksi->detailTransaksis->sum('subtotal');
                        
                        // Gunakan data dari database jika ada, atau hitung dari detail
                        $totalHarga = $transaksi->total_harga ?? $subtotal;
                        $diskon = $transaksi->diskon ?? 0;
                        $totalBayar = $transaksi->total_bayar ?? $totalHarga;
                        
                        // Hitung pajak 11% dari total harga
                        $pajak = $totalHarga * 0.11;
                        $totalSetelahDiskon = $totalHarga - $diskon;
                        $totalSetelahPajak = $totalSetelahDiskon + $pajak;
                    @endphp
                    
                    <div class="flex justify-between">
                        <span>Total Harga:</span>
                        <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($diskon > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Diskon:</span>
                        <span>- Rp {{ number_format($diskon, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span>Setelah Diskon:</span>
                        <span>Rp {{ number_format($totalSetelahDiskon, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span>Pajak (11%):</span>
                        <span>Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="border-t border-gray-300 pt-2 mt-2">
                        <div class="flex justify-between text-xl font-bold">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($transaksi->status == 'pending')
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Silahkan lakukan pembayaran segera
                    </p>
                </div>
                @endif
                
                @if($transaksi->catatan)
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-sticky-note mr-2"></i>
                        <strong>Catatan:</strong> {{ $transaksi->catatan }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Terms & Conditions -->
    <div class="mt-8 pt-6 border-t border-gray-300">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Syarat & Ketentuan</h3>
        <ol class="list-decimal pl-5 text-sm text-gray-600 space-y-1">
            <li>Pembayaran harus dilakukan sesuai dengan metode yang dipilih.</li>
            <li>Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kerusakan saat diterima.</li>
            <li>Untuk penyewaan, keterlambatan pengembalian dikenakan denda sesuai ketentuan.</li>
            <li>Invoice ini sah dan dapat digunakan sebagai bukti transaksi.</li>
            <li>Transaksi yang sudah dibayar tidak dapat dibatalkan.</li>
        </ol>
    </div>
    
    <!-- Signature -->
    <div class="mt-12 flex justify-between">
        <div class="text-center">
            <div class="border-t border-gray-400 w-48 mt-12 pt-2">
                <p class="text-sm">Hormat Kami,</p>
                <p class="font-bold">{{ config('app.name', 'Sistem Rental') }}</p>
            </div>
        </div>
        <div class="text-center">
            <div class="border-t border-gray-400 w-48 mt-12 pt-2">
                <p class="text-sm">Pelanggan,</p>
                <p class="font-bold">{{ $transaksi->customer_name ?? $transaksi->user->name ?? 'Customer' }}</p>
            </div>
        </div>
    </div>
    
    <!-- QR Code for Payment -->
    @if($transaksi->status == 'pending')
    <div class="mt-8 text-center">
        <div class="inline-block p-4 border border-gray-300 rounded-lg">
            <p class="text-sm text-gray-600 mb-2">Scan untuk informasi transaksi:</p>
            <div id="qrcode" class="flex justify-center"></div>
            <p class="text-xs text-gray-500 mt-2">QR Code Invoice {{ $transaksi->kode_transaksi }}</p>
        </div>
    </div>
    @endif
    
    <!-- Print Info -->
    <div class="mt-8 text-center text-sm text-gray-500">
        <p>Invoice ini dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Oleh: {{ auth()->user()->name ?? 'Admin' }}</p>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .invoice-container {
            box-shadow: none !important;
            margin: 0 !important;
            padding: 20px !important;
            max-width: 100% !important;
        }
        
        .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code
    document.addEventListener('DOMContentLoaded', function() {
        @if($transaksi->status == 'pending')
        const qrData = `TRANSAKSI:{{ $transaksi->kode_transaksi }}
TOTAL:{{ number_format($transaksi->total_bayar, 0, '', '') }}
TANGGAL:{{ $transaksi->created_at->format('Y-m-d') }}
METODE:{{ $transaksi->metode_pembayaran }}
STATUS:{{ $transaksi->status }}
TOKO:{{ config('app.name', 'Sistem Rental') }}`;
        
        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
        @endif
        
        // Auto print if parameter exists
        @if(request()->has('print'))
            setTimeout(function() {
                window.print();
            }, 1000);
        @endif
    });
    
    // Close window after print (optional)
    window.onafterprint = function() {
        @if(request()->has('autoclose'))
            setTimeout(function() {
                window.close();
            }, 500);
        @endif
    };
</script>
@endpush
@endsection