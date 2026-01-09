{{-- resources/views/admin/transaksi/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Transaksi')

@section('page-title', 'Edit Transaksi')
@section('page-subtitle', 'Ubah data transaksi')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
            ['url' => route('admin.transaksi.index'), 'label' => 'Transaksi'],
            ['label' => 'Edit Transaksi']
        ];
    @endphp
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Edit Transaksi</h1>
            <p class="text-gray-600 text-sm mt-1">Perbarui data transaksi {{ $transaction->kode_transaksi ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transaksi.show', $transaction->id) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-eye mr-2"></i> Detail
            </a>
            <a href="{{ route('admin.transaksi.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Transaction Summary -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-xs text-gray-500 mb-1">Kode Transaksi</p>
                <p class="text-sm font-semibold text-gray-900 font-mono">{{ $transaction->kode_transaksi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ optional($transaction->created_at)->format('d M Y H:i') ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tipe</p>
                <p class="text-sm font-semibold text-gray-900">
                    @if(($transaction->tipe ?? '') === 'penjualan')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            <i class="fas fa-shopping-cart mr-1 text-[8px]"></i> Penjualan
                        </span>
                    @elseif(($transaction->tipe ?? '') === 'penyewaan')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                            <i class="fas fa-calendar-alt mr-1 text-[8px]"></i> Penyewaan
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                            -
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Form Section -->
            <div>
                <form action="{{ route('admin.transaksi.update', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Transaksi</label>
                            <select name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ ($transaction->status ?? '') === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <input type="text" name="metode_pembayaran" 
                                   value="{{ old('metode_pembayaran', $transaction->metode_pembayaran ?? '') }}"
                                   placeholder="Contoh: Transfer Bank, COD, dll"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Bank Transfer Details (if applicable) -->
                        @if(in_array($transaction->metode_pembayaran, ['Transfer Bank', 'Bank Transfer']))
                        <div class="space-y-3 p-3 bg-gray-50 rounded-lg">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                                <input type="text" name="nama_bank" 
                                       value="{{ old('nama_bank', $transaction->nama_bank ?? '') }}"
                                       placeholder="Nama bank pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                                <input type="text" name="no_rekening" 
                                       value="{{ old('no_rekening', $transaction->no_rekening ?? '') }}"
                                       placeholder="Nomor rekening pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                                <input type="text" name="atas_nama" 
                                       value="{{ old('atas_nama', $transaction->atas_nama ?? '') }}"
                                       placeholder="Nama pemilik rekening"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        @endif

                        @if(($transaction->tipe ?? '') === 'penyewaan')
                        <!-- Rental Dates from Sewa model -->
                        @if($transaction->sewa)
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Sewa</label>
                                <input type="date" name="tanggal_mulai" 
                                       value="{{ optional($transaction->sewa->tanggal_mulai)->format('Y-m-d') ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selesai Sewa</label>
                                <input type="date" name="tanggal_selesai" 
                                       value="{{ optional($transaction->sewa->tanggal_selesai)->format('Y-m-d') ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        @endif
                        @endif

                        <!-- Shipping Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengiriman</label>
                            <input type="datetime-local" name="tanggal_pengiriman" 
                                   value="{{ optional($transaction->tanggal_pengiriman)->format('Y-m-d\TH:i') ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                            <input type="datetime-local" name="tanggal_pembayaran" 
                                   value="{{ optional($transaction->tanggal_pembayaran)->format('Y-m-d\TH:i') ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="catatan" rows="3" placeholder="Tambahkan catatan untuk transaksi ini"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">{{ old('catatan', $transaction->catatan ?? '') }}</textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center gap-3 pt-4">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.transaksi.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                <!-- User Info -->
                <div class="p-3 bg-white rounded border border-gray-200">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium">
                            {{ strtoupper(substr($transaction->user->name ?? '-', 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->user->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 space-y-1">
                        <p><span class="font-medium">Telepon:</span> {{ $transaction->user->phone ?? '-' }}</p>
                        <p><span class="font-medium">Alamat:</span> {{ $transaction->alamat_pengiriman ?? 'Tidak ada alamat' }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="space-y-2">
                    @foreach($transaction->detailTransaksis ?? [] as $item)
                        <div class="flex items-center justify-between text-xs bg-white p-2 rounded border border-gray-200">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $item->produk->nama ?? '-' }}</p>
                                <p class="text-gray-500">Qty: {{ $item->quantity ?? 0 }} × Rp {{ number_format($item->harga_satuan ?? 0,0,',','.') }}</p>
                                @if($item->opsi_sewa)
                                    <p class="text-gray-500 text-xs mt-1">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        {{ $item->durasi_sewa ?? '-' }} hari
                                    </p>
                                @endif
                            </div>
                            <div class="text-gray-900 font-medium">
                                Rp {{ number_format($item->subtotal ?? 0,0,',','.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Payment Summary -->
                <div class="pt-4 border-t border-gray-200 text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($transaction->total_harga ?? 0,0,',','.') }}</span>
                    </div>
                    @if(($transaction->diskon ?? 0) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span class="font-medium text-green-600">-Rp {{ number_format($transaction->diskon,0,',','.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-200 font-bold text-gray-900">
                        <span>Total</span>
                        <span class="text-lg">Rp {{ number_format($transaction->total_bayar ?? 0,0,',','.') }}</span>
                    </div>
                </div>

                <!-- Payment Proof -->
                @if($transaction->bukti_pembayaran)
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="text-xs font-medium text-gray-700 mb-2">Bukti Pembayaran</h4>
                    <a href="{{ $transaction->bukti_pembayaran_url }}" target="_blank" 
                       class="block p-2 bg-white rounded border border-gray-200 hover:bg-gray-50">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-invoice text-blue-500"></i>
                            <span class="text-xs text-gray-700">Lihat bukti pembayaran</span>
                        </div>
                    </a>
                </div>
                @endif

                <!-- Current Status -->
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="text-xs font-medium text-gray-700 mb-2">Status Saat Ini</h4>
                    @php
                        $statusConfig = [
                            'pending' => ['color' => 'bg-yellow-50 text-yellow-700 border-yellow-200','icon'=>'fas fa-clock'],
                            'diproses'=> ['color'=>'bg-blue-50 text-blue-700 border-blue-200','icon'=>'fas fa-cog'],
                            'dibayar'=> ['color'=>'bg-indigo-50 text-indigo-700 border-indigo-200','icon'=>'fas fa-check-circle'],
                            'dikirim'=> ['color'=>'bg-purple-50 text-purple-700 border-purple-200','icon'=>'fas fa-truck'],
                            'selesai'=> ['color'=>'bg-green-50 text-green-700 border-green-200','icon'=>'fas fa-check-double'],
                            'dibatalkan'=> ['color'=>'bg-red-50 text-red-700 border-red-200','icon'=>'fas fa-times-circle']
                        ];
                        $config = $statusConfig[$transaction->status ?? ''] ?? ['color'=>'bg-gray-50 text-gray-700 border-gray-200','icon'=>'fas fa-question-circle'];
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                            <i class="{{ $config['icon'] }} mr-1 text-[8px]"></i>
                            {{ ucfirst($transaction->status ?? '-') }}
                        </span>
                        <span class="text-xs text-gray-500">{{ optional($transaction->updated_at)->format('d M Y H:i') ?? '-' }}</span>
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
    // Toggle bank details based on payment method
    const paymentMethodInput = document.querySelector('input[name="metode_pembayaran"]');
    const bankDetails = document.querySelector('.bg-gray-50.rounded-lg');
    
    if(paymentMethodInput && bankDetails) {
        paymentMethodInput.addEventListener('change', function() {
            const method = this.value.toLowerCase();
            if(method.includes('transfer') || method.includes('bank')) {
                bankDetails.style.display = 'block';
            } else {
                bankDetails.style.display = 'none';
            }
        });
    }

    // Date validation for rental
    const startDate = document.querySelector('input[name="tanggal_mulai"]');
    const endDate = document.querySelector('input[name="tanggal_selesai"]');
    if(startDate && endDate) {
        startDate.addEventListener('change', ()=>{ endDate.min = startDate.value; });
        endDate.addEventListener('change', ()=>{ 
            if(startDate.value && endDate.value < startDate.value) {
                alert('Tanggal selesai tidak boleh sebelum tanggal mulai'); endDate.value = '';
            }
        });
    }

    // Status change confirmation
    const statusSelect = document.querySelector('select[name="status"]');
    if(statusSelect){
        const originalStatus = statusSelect.value;
        statusSelect.addEventListener('change', function(){
            if(originalStatus==='selesai' && this.value!=='selesai'){
                if(!confirm('Transaksi sudah selesai. Yakin ingin mengubah status?')){ this.value=originalStatus; return; }
            }
            if(this.value==='dibatalkan'){
                if(!confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')){ this.value=originalStatus; return; }
            }
        });
    }
});
</script>
@endpush