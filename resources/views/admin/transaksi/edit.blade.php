{{-- resources/views/admin/transaksi/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Transaksi Penjualan')

@section('page-title', 'Edit Transaksi Penjualan')
@section('page-subtitle', 'Ubah status dan informasi transaksi penjualan')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.transaksi.index'), 'label' => 'Transaksi Penjualan'],
            ['label' => 'Edit Transaksi']
        ];
    @endphp
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-black">Edit Transaksi Penjualan</h1>
            <p class="text-black text-sm mt-1">Perbarui status transaksi {{ $transaction->kode_transaksi }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-white bg-white hover:bg-gray-50">
                <i class="fas fa-eye mr-2"></i> Detail
            </a>
            <a href="{{ route('admin.transaksi.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-white bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Transaction Summary -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <p class="text-xs text-gray-500 mb-1">Kode Transaksi</p>
                <p class="text-sm font-semibold text-gray-900 font-mono">{{ $transaction->kode_transaksi }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal Pesanan</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $transaction->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Status</p>
                @php
                    $statusConfig = [
                        'pending' => ['color' => 'bg-yellow-50 text-yellow-700 border-yellow-200','label'=>'Pending'],
                        'diproses'=> ['color'=>'bg-blue-50 text-blue-700 border-blue-200','label'=>'Diproses'],
                        'dibayar'=> ['color'=>'bg-indigo-50 text-indigo-700 border-indigo-200','label'=>'Dibayar'],
                        'dikirim'=> ['color'=>'bg-purple-50 text-purple-700 border-purple-200','label'=>'Dikirim'],
                        'selesai'=> ['color'=>'bg-green-50 text-green-700 border-green-200','label'=>'Selesai'],
                        'dibatalkan'=> ['color'=>'bg-red-50 text-red-700 border-red-200','label'=>'Dibatalkan']
                    ];
                    $config = $statusConfig[$transaction->status] ?? ['color'=>'bg-gray-50 text-gray-100 dark:text-gray-700 border-gray-200','label'=>$transaction->status];
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                    <i class="fas fa-circle text-[6px] mr-1"></i>
                    {{ $config['label'] }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Total</p>
                <p class="text-sm font-semibold text-gray-900">
                    Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-sm font-medium text-blue-900 mb-3">Informasi Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-blue-600 mb-1">Metode Pembayaran</p>
                    <p class="text-sm font-semibold text-blue-900">
                        @php
                            $metodeLabels = [
                                'tunai' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                                'debit' => 'Kartu Debit',
                                'kartu_kredit' => 'Kartu Kredit',
                            ];
                        @endphp
                        {{ $metodeLabels[$transaction->metode_pembayaran] ?? ucfirst($transaction->metode_pembayaran) }}
                    </p>
                </div>
                
                <div>
                    <p class="text-xs text-blue-600 mb-1">Status Pembayaran</p>
                    <p class="text-sm font-semibold text-blue-900">
                        @if ($transaction->bukti_pembayaran)
                            @if ($transaction->status == 'pending' || $transaction->status == 'diproses')
                                <span class=" text-yellow-600">Menunggu Konfirmasi</span>
                            @else
                                <span class=" text-green-600">Selesai Dibayar</span>
                            @endif
                        @else
                            @if($transaction->tanggal_pembayaran)
                                <span class=" text-green-600">Selesai Dibayar</span>
                            @else
                                <span class="text-red-600">Belum dibayar</span>
                            @endif
                        @endif
                    </p>
                </div>
                
                <div>
                    <p class="text-xs text-blue-600 mb-1">Tanggal Pembayaran</p>
                    <p class="text-sm font-semibold text-blue-900">
                        {{ $transaction->tanggal_pembayaran ? $transaction->tanggal_pembayaran->format('d M Y H:i') : '' }}
                    </p>
                </div>
                
                <!-- <div>
                    <p class="text-xs text-blue-600 mb-1">Diverifikasi Oleh</p>
                    <p class="text-sm font-semibold text-blue-900">
                        @if($transaction->verifikasi_oleh && $transaction->verifikator)
                            {{ $transaction->verifikator->name }}
                            @if($transaction->tanggal_verifikasi)
                                <span class="text-xs text-blue-600 block mt-1">
                                    {{ $transaction->tanggal_verifikasi->format('d M Y H:i') }}
                                </span>
                            @endif
                        @else
                            <span class="text-yellow-600">Belum diverifikasi</span>
                        @endif
                    </p>
                </div> -->
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Form Section -->
            <div>
                <form action="{{ route('admin.transaksi.update', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <!-- Bukti Pembayaran -->
                        @if ($transaction->bukti_pembayaran)
                            <div class=" flex flex-col">
                                <label class="block text-sm font-medium text-gray-100 dark:text-gray-700 mb-1">Bukti Pembayaran</label>
                                <a href="{{asset('storage/bukti-pembayaran/'. $transaction->bukti_pembayaran)}}" target="_blank" class=" flex justify-between items-center w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-left">
                                    {{$transaction->bukti_pembayaran}}
                                    <i class="fas fa-external-link-alt transform group-hover:rotate-12 transition-transform text-black"></i>
                                </a>
                            </div>
                        @endif

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-100 dark:text-gray-700 mb-1">Status Transaksi *</label>
                            <select name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $transaction->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shipping Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-100 dark:text-gray-700 mb-1">Tanggal Pengiriman</label>
                            <input type="datetime-local" name="tanggal_pengiriman" 
                                   value="{{ old('tanggal_pengiriman', optional($transaction->tanggal_pengiriman)->format('Y-m-d\TH:i') ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">
                                Diisi otomatis saat status berubah menjadi "Dikirim"
                            </p>
                        </div>

                        <!-- Shipping Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-100 dark:text-gray-700 mb-1">Alamat Pengiriman</label>
                            <textarea name="alamat_pengiriman" rows="2" placeholder="Alamat pengiriman produk"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">{{ old('alamat_pengiriman', $transaction->alamat_pengiriman ?? '') }}</textarea>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-100 dark:text-gray-700 mb-1">Catatan Admin</label>
                            <textarea name="catatan" rows="3" placeholder="Catatan internal untuk transaksi ini"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">{{ old('catatan', $transaction->catatan ?? '') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                Catatan hanya visible untuk admin
                            </p>
                        </div>

                        <!-- Warning for stock changes -->
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800">Perhatian!</p>
                                    <p class="text-xs text-yellow-700 mt-1">
                                        Mengubah status akan mempengaruhi stok produk:
                                        <ul class="list-disc list-inside mt-1 ml-2">
                                            <li><strong>Pending → Dibayar:</strong> Stok berkurang</li>
                                            <li><strong>Dibayar → Dibatalkan:</strong> Stok bertambah</li>
                                            <li><strong>Dibatalkan → Dibayar:</strong> Stok berkurang lagi</li>
                                        </ul>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center gap-3 pt-4">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
                               class="px-4 py-2 border border-gray-300 text-gray-100 dark:text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                <!-- Customer Info -->
                <div class="p-3 bg-white rounded border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Informasi Customer</h3>
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium">
                            {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->user->email }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <p><span class="font-medium">Telepon:</span> {{ $transaction->user->phone ?? '-' }}</p>
                        <p><span class="font-medium">Alamat:</span> {{ $transaction->user->address ?? '-' }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div>
                    <h3 class="text-sm font-medium text-black mb-3">Produk Dipesan</h3>
                    <div class="space-y-2">
                        @foreach($transaction->detailTransaksis as $item)
                            <div class="flex items-center justify-between text-xs bg-white p-2 rounded border border-gray-200">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $item->produk->nama }}</p>
                                    <p class="text-gray-500">
                                        {{ $item->quantity }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-gray-900 font-medium">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-black mb-3">Ringkasan Pembayaran</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-black">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
                        </div>
                        @if($transaction->diskon > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-black">Diskon</span>
                            <span class="font-medium text-green-600">-Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200 font-bold text-gray-900">
                            <span class="text-black">Total</span>
                            <span class="text-lg text-black">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Current Status Info -->
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-black mb-3">Informasi Status</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-black">Status Saat Ini:</span>
                            @php
                                $config = $statusConfig[$transaction->status] ?? ['color'=>'bg-gray-50 text-gray-100 dark:text-gray-700 border-gray-200','label'=>$transaction->status];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                                <i class="fas fa-circle text-[6px] mr-1"></i>
                                {{ $config['label'] }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-black">Terakhir Update:</span>
                            <span class="text-black">{{ $transaction->updated_at->format('d M Y H:i') }}</span>
                        </div>
                        
                        <!-- Stock Info -->
                        @if($transaction->status == 'dibatalkan')
                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                            <p class="text-xs font-medium text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Stok produk sudah dikembalikan
                            </p>
                        </div>
                        @elseif(in_array($transaction->status, ['dibayar', 'diproses', 'dikirim']))
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded">
                            <p class="text-xs font-medium text-blue-800">
                                <i class="fas fa-box mr-1"></i>
                                Stok produk sudah dikurangi
                            </p>
                        </div>
                        @elseif($transaction->status == 'pending')
                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-xs font-medium text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Stok belum dikurangi (menunggu pembayaran)
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    
    if(statusSelect){
        const originalStatus = statusSelect.value;
        
        statusSelect.addEventListener('change', function(){
            const newStatus = this.value;
            
            // Konfirmasi untuk perubahan status yang penting
            if(originalStatus === 'selesai' && newStatus !== 'selesai'){
                if(!confirm('Transaksi sudah selesai. Yakin ingin mengubah status?')){ 
                    this.value = originalStatus; 
                    return; 
                }
            }
            
            if(originalStatus === 'dibatalkan' && newStatus !== 'dibatalkan') {
                if(!confirm('Transaksi dibatalkan akan diaktifkan kembali. Stok akan dikurangi. Lanjutkan?')) {
                    this.value = originalStatus;
                    return;
                }
            }
            
            if(newStatus === 'dibatalkan') {
                if(!confirm('Membatalkan transaksi akan mengembalikan stok produk. Lanjutkan?')) {
                    this.value = originalStatus;
                    return;
                }
            }
            
            if(newStatus === 'dibayar' && originalStatus === 'pending') {
                if(!confirm('Mengubah status ke DIBAYAR akan mengurangi stok produk. Lanjutkan?')) {
                    this.value = originalStatus;
                    return;
                }
            }
            
            if(newStatus === 'dikirim' && originalStatus !== 'dikirim') {
                // Otomatis set tanggal pengiriman jika kosong
                const tanggalPengirimanInput = document.querySelector('input[name="tanggal_pengiriman"]');
                if(tanggalPengirimanInput && !tanggalPengirimanInput.value) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    
                    tanggalPengirimanInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                }
            }
        });
    }
});
</script>
@endpush