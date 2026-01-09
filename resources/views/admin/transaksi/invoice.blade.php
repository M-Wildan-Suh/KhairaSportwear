@extends('admin.layouts.report')

@section('title', 'Invoice #' . $transaksi->invoice_number)

@section('content')
<div class="invoice-container bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
    <!-- Invoice Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                    <p class="text-gray-600">Sistem Rental & Penjualan Alat</p>
                </div>
            </div>
            <div class="text-gray-600">
                <p><i class="fas fa-map-marker-alt mr-2"></i>Jl. Contoh No. 123, Kota Bandung</p>
                <p><i class="fas fa-phone mr-2"></i>(022) 123-4567</p>
                <p><i class="fas fa-envelope mr-2"></i>info@rentalsystem.com</p>
            </div>
        </div>
        
        <div class="text-right">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">INVOICE</h2>
            <div class="bg-cyan-600 text-white px-4 py-2 rounded-lg inline-block">
                <div class="text-sm">No. Invoice</div>
                <div class="text-xl font-bold">{{ $transaksi->invoice_number }}</div>
            </div>
            <div class="mt-4 text-gray-600">
                <p>Tanggal: {{ $transaksi->created_at->format('d/m/Y') }}</p>
                <p>Status: 
                    <span class="px-2 py-1 rounded text-sm font-bold 
                        @if($transaksi->status == 'completed') bg-green-100 text-green-800
                        @elseif($transaksi->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
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
                <p><strong>Nama:</strong> {{ $transaksi->user->name ?? 'Guest' }}</p>
                <p><strong>Email:</strong> {{ $transaksi->user->email ?? '-' }}</p>
                <p><strong>Telepon:</strong> {{ $transaksi->user->phone ?? '-' }}</p>
                <p><strong>Alamat:</strong> {{ $transaksi->user->address ?? '-' }}</p>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-file-invoice mr-2"></i>Informasi Pembayaran
            </h3>
            <div class="space-y-1">
                <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->payment_method) }}</p>
                <p><strong>Status Pembayaran:</strong> 
                    <span class="font-bold {{ $transaksi->payment_status == 'paid' ? 'text-green-600' : 'text-red-600' }}">
                        {{ strtoupper($transaksi->payment_status) }}
                    </span>
                </p>
                @if($transaksi->payment_proof)
                <p><strong>Bukti Bayar:</strong> 
                    <a href="{{ asset('storage/' . $transaksi->payment_proof) }}" target="_blank" class="text-cyan-600 hover:underline">
                        Lihat Bukti
                    </a>
                </p>
                @endif
                <p><strong>Tanggal Jatuh Tempo:</strong> {{ optional($transaksi->due_date)->format('d/m/Y') ?? '-' }}</p>
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
                    <th class="border border-gray-300 p-3 text-left">Durasi</th>
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
                            <img src="{{ $detail->produk->gambar_url }}" alt="{{ $detail->produk->nama }}" 
                                 class="h-10 w-10 rounded-lg object-cover mr-3">
                            @endif
                            <div>
                                <div class="font-medium">{{ $detail->produk->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $detail->produk->kategori->nama ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="border border-gray-300 p-3">
                        <span class="px-2 py-1 rounded text-xs font-bold 
                            @if($detail->tipe == 'jual') bg-green-100 text-green-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ strtoupper($detail->tipe) }}
                        </span>
                    </td>
                    <td class="border border-gray-300 p-3">{{ $detail->quantity }}</td>
                    <td class="border border-gray-300 p-3">
                        @if($detail->tipe == 'sewa')
                            {{ $detail->durasi }} hari
                        @else
                            -
                        @endif
                    </td>
                    <td class="border border-gray-300 p-3">
                        @if($detail->tipe == 'jual')
                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                        @else
                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
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
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pajak ({{ $transaksi->tax_percentage }}%):</span>
                        <span>Rp {{ number_format($transaksi->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Layanan:</span>
                        <span>Rp {{ number_format($transaksi->service_fee, 0, ',', '.') }}</span>
                    </div>
                    @if($transaksi->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Diskon:</span>
                        <span>-Rp {{ number_format($transaksi->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-300 pt-2 mt-2">
                        <div class="flex justify-between text-xl font-bold">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($transaksi->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Silahkan lakukan pembayaran sebelum 
                        <strong>{{ optional($transaksi->due_date)->format('d F Y') ?? '-' }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Terms & Conditions -->
    <div class="mt-8 pt-6 border-t border-gray-300">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Syarat & Ketentuan</h3>
        <ol class="list-decimal pl-5 text-sm text-gray-600 space-y-1">
            <li>Pembayaran harus dilakukan dalam waktu 1x24 jam setelah invoice diterima.</li>
            <li>Barang yang sudah dibeli tidak dapat dikembalikan.</li>
            <li>Untuk sewa, kerusakan barang menjadi tanggung jawab penyewa.</li>
            <li>Keterlambatan pengembalian barang sewa dikenakan denda 10% per hari.</li>
            <li>Invoice ini sah dan dapat digunakan sebagai bukti transaksi.</li>
        </ol>
    </div>
    
    <!-- Signature -->
    <div class="mt-12 flex justify-between">
        <div class="text-center">
            <div class="border-t border-gray-400 w-48 mt-12 pt-2">
                <p class="text-sm">Hormat Kami,</p>
                <p class="font-bold">{{ config('app.name') }}</p>
            </div>
        </div>
        <div class="text-center">
            <div class="border-t border-gray-400 w-48 mt-12 pt-2">
                <p class="text-sm">Pelanggan,</p>
                <p class="font-bold">{{ $transaksi->user->name ?? 'Guest' }}</p>
            </div>
        </div>
    </div>
    
    <!-- QR Code for Payment -->
    <div class="mt-8 text-center">
        <div class="inline-block p-4 border border-gray-300 rounded-lg">
            <p class="text-sm text-gray-600 mb-2">Scan untuk pembayaran:</p>
            <div id="qrcode" class="flex justify-center"></div>
            <p class="text-xs text-gray-500 mt-2">QR Code pembayaran {{ config('app.name') }}</p>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code
    document.addEventListener('DOMContentLoaded', function() {
        const qrData = `INVOICE:{{ $transaksi->invoice_number }}
AMOUNT:{{ $transaksi->total_amount }}
DATE:{{ $transaksi->created_at->format('Y-m-d') }}
COMPANY:{{ config('app.name') }}`;
        
        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
    
    // Auto print if parameter exists
    @if(request()->has('print'))
        window.print();
    @endif
</script>
@endpush
@endsection