@extends('user.layouts.app')

@section('title', 'Histori Transaksi - SportWear')

@section('content')
<div class="py-8">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 lg:px-8">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-gray-800 transition-colors duration-200">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <span class="text-gray-800 font-medium">Histori Transaksi</span>
        </nav>
    </div>

    <div class="container mx-auto px-4 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Histori Transaksi</h1>
                <p class="text-gray-600">Lihat dan kelola semua transaksi Anda</p>
            </div>
            
            <!-- Filter Dropdown -->
            <div class="relative">
                <button id="filterButton" 
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-filter text-gray-600"></i>
                    <span class="font-medium">Filter</span>
                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                
                <div id="filterDropdown" 
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 hidden z-20">
                    <div class="p-2">
                        <a href="{{ route('user.transaksi.index') }}" 
                           class="flex items-center px-4 py-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 {{ !request('status') && !request('tipe') ? 'text-gray-800 bg-gray-800/5' : '' }}">
                            <i class="fas fa-receipt mr-3 text-gray-400"></i>
                            Semua Transaksi
                        </a>
                    </div>
                    <div class="py-2 border-t border-gray-100">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</div>
                        <a href="{{ route('user.transaksi.index', ['status' => 'pending']) }}" 
                           class="flex items-center px-4 py-2.5 mx-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 {{ request('status') == 'pending' ? 'text-gray-800 bg-gray-800/5' : '' }}">
                            <div class="w-2 h-2 rounded-full bg-yellow-400 mr-3"></div>
                            Pending
                        </a>
                        <a href="{{ route('user.transaksi.index', ['status' => 'diproses']) }}" 
                           class="flex items-center px-4 py-2.5 mx-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 {{ request('status') == 'diproses' ? 'text-gray-800 bg-gray-800/5' : '' }}">
                            <div class="w-2 h-2 rounded-full bg-blue-400 mr-3"></div>
                            Diproses
                        </a>
                        <a href="{{ route('user.transaksi.index', ['status' => 'selesai']) }}" 
                           class="flex items-center px-4 py-2.5 mx-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 {{ request('status') == 'selesai' ? 'text-gray-800 bg-gray-800/5' : '' }}">
                            <div class="w-2 h-2 rounded-full bg-green-400 mr-3"></div>
                            Selesai
                        </a>
                    </div>
                    <div class="py-2 border-t border-gray-100">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</div>
                        <a href="{{ route('user.transaksi.index', ['tipe' => 'penjualan']) }}" 
                           class="flex items-center px-4 py-2.5 mx-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 {{ request('tipe') == 'penjualan' ? 'text-gray-800 bg-gray-800/5' : '' }}">
                            <i class="fas fa-shopping-cart mr-3 text-green-400"></i>
                            Penjualan
                        </a>
                        <a href="{{ route('user.transaksi.index', ['tipe' => 'penyewaan']) }}" 
                           class="flex items-center px-4 py-2.5 mx-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 {{ request('tipe') == 'penyewaan' ? 'text-gray-800 bg-gray-800/5' : '' }}">
                            <i class="fas fa-calendar-alt mr-3 text-blue-400"></i>
                            Penyewaan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($transaksis->count() > 0)
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Transaksi -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:border-gray-800/30" data-aos="fade-up">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-2 font-medium">Total Transaksi</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $transaksis->total() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Semua transaksi</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-800/10 to-gray-800/5 rounded-xl flex items-center justify-center">
                        <i class="fas fa-receipt text-gray-800 text-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Berhasil:</span>
                        <span class="font-semibold text-green-600">{{ $transaksis->where('status', 'selesai')->count() }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Selesai -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:border-green-200" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-2 font-medium">Selesai</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $transaksis->where('status', 'selesai')->count() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Transaksi berhasil</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-xs text-green-600 bg-green-50 px-3 py-1 rounded-full inline-flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i>
                        <span>100% sukses</span>
                    </div>
                </div>
            </div>
            
            <!-- Dalam Proses -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:border-yellow-200" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-2 font-medium">Dalam Proses</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $transaksis->whereIn('status', ['pending', 'diproses'])->count() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Menunggu konfirmasi</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 60%"></div>
                        </div>
                        <span class="text-xs text-yellow-600 font-medium">60%</span>
                    </div>
                </div>
            </div>
            
            <!-- Dibatalkan -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:border-red-200" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-2 font-medium">Dibatalkan</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $transaksis->where('status', 'dibatalkan')->count() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Transaksi dibatalkan</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-lg"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-xs text-red-600 bg-red-50 px-3 py-1 rounded-full inline-flex items-center gap-1">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ $transaksis->total() > 0 ? round(($transaksis->where('status', 'dibatalkan')->count() / $transaksis->total()) * 100, 1) : 0 }}% dari total</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" data-aos="fade-up">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">Daftar Transaksi</h3>
                        <p class="text-sm text-gray-600 mt-1">Menampilkan {{ $transaksis->count() }} dari {{ $transaksis->total() }} transaksi</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ now()->format('d M Y') }}
                    </div>
                </div>
            </div>
            
            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/80">
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Kode Transaksi</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tanggal</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tipe</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Total</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Status</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transaksis as $transaksi)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-200 group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <!-- Kode Transaksi -->
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-800/10 flex items-center justify-center group-hover:bg-gray-800/20 transition-colors">
                                        <i class="fas fa-receipt text-gray-800 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 group-hover:text-gray-800 transition-colors">
                                            {{ $transaksi->kode_transaksi }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $transaksi->detailTransaksis->count() }} item</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Tanggal -->
                            <td class="py-5 px-6">
                                <div class="space-y-1">
                                    <div class="font-medium text-gray-900">{{ $transaksi->created_at->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-clock text-xs"></i>
                                        {{ $transaksi->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Tipe -->
                            <td class="py-5 px-6">
                                @if($transaksi->tipe === 'penjualan')
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-full border border-green-100">
                                    <i class="fas fa-shopping-cart text-xs"></i>
                                    <span class="text-sm font-medium">Penjualan</span>
                                </div>
                                @else
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                    <span class="text-sm font-medium">Penyewaan</span>
                                </div>
                                @endif
                            </td>
                            
                            <!-- Total -->
                            <td class="py-5 px-6">
                                <div class="space-y-1">
                                    <div class="font-semibold text-gray-900 text-lg">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-500 capitalize">
                                        @if($transaksi->metode_pembayaran === 'transfer_bank')
                                        <i class="fas fa-university mr-1"></i> Transfer Bank
                                        @else
                                        <i class="fas fa-money-bill-wave mr-1"></i> {{ str_replace('_', ' ', $transaksi->metode_pembayaran) }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td class="py-5 px-6">
                                <div class="space-y-2">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['color' => 'yellow', 'icon' => 'clock'],
                                            'diproses' => ['color' => 'blue', 'icon' => 'sync'],
                                            'selesai' => ['color' => 'green', 'icon' => 'check-circle'],
                                            'dibatalkan' => ['color' => 'red', 'icon' => 'times-circle']
                                        ];
                                        $config = $statusConfig[$transaksi->status] ?? $statusConfig['pending'];
                                    @endphp
                                    
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-{{ $config['color'] }}-50 text-{{ $config['color'] }}-700 rounded-lg border border-{{ $config['color'] }}-100">
                                        <i class="fas fa-{{ $config['icon'] }} text-xs"></i>
                                        <span class="text-sm font-medium capitalize">{{ $transaksi->status }}</span>
                                    </div>
                                    
                                    @if($transaksi->status === 'pending')
                                        <div class="flex items-center gap-1 text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span>{{$transaksi->metode_pembayaran == 'tunai' ? 'Lunasi produk di toko' : 'Upload bukti pembayaran'}}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-2">
                                    <!-- Detail -->
                                    <a href="{{ route('user.transaksi.show', $transaksi->id) }}" 
                                       class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-800 hover:bg-gray-800/10 rounded-lg border border-gray-200 hover:border-gray-800/30 transition-all duration-200 group/btn"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye group-hover/btn:scale-110 transition-transform"></i>
                                    </a>
                                    
                                    <!-- Upload Bukti (hanya untuk pending) -->
                                    @if($transaksi->status === 'pending' && $transaksi->metode_pembayaran != 'tunai')
                                    <button onclick="showUploadModal('{{ $transaksi->id }}')" 
                                            class="w-10 h-10 flex items-center justify-center text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg border border-gray-200 hover:border-green-300 transition-all duration-200 group/btn"
                                            title="Upload Bukti">
                                        <i class="fas fa-upload group-hover/btn:scale-110 transition-transform"></i>
                                    </button>
                                    @endif
                                    
                                    <!-- Batalkan (hanya untuk pending/diproses) -->
                                    @if(in_array($transaksi->status, ['pending', 'diproses']))
                                    <button onclick="cancelTransaction('{{ $transaksi->id }}')" 
                                            class="w-10 h-10 flex items-center justify-center text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg border border-gray-200 hover:border-red-300 transition-all duration-200 group/btn"
                                            title="Batalkan Transaksi">
                                        <i class="fas fa-times group-hover/btn:scale-110 transition-transform"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($transaksis->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50/50">
                {{ $transaksis->withQueryString()->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
            @endif
        </div>
        
        @else
        <!-- Empty State -->
        <div class="max-w-md mx-auto text-center py-16" data-aos="fade-up">
            <div class="relative mb-8">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-receipt text-gray-400 text-5xl"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-12 h-12 bg-gray-800/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-gray-800"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Transaksi</h3>
            <p class="text-gray-600 mb-8">Mulai berbelanja untuk melihat riwayat transaksi Anda. Transaksi Anda akan muncul di sini.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('produk.index') }}" 
                   class="group inline-flex items-center justify-center gap-3 px-8 py-3.5 bg-gray-800 text-white font-semibold rounded-xl hover:bg-gray-800-dark transition-all duration-300 hover:shadow-lg">
                    <i class="fas fa-store group-hover:scale-110 transition-transform"></i>
                    <span>Belanja Sekarang</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('sewa.index') }}" 
                   class="group inline-flex items-center justify-center gap-3 px-8 py-3.5 border-2 border-gray-800 text-gray-800 font-semibold rounded-xl hover:bg-gray-800/5 transition-all duration-300">
                    <i class="fas fa-calendar-alt group-hover:rotate-12 transition-transform"></i>
                    <span>Sewa Alat</span>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Upload Bukti Modal -->
<div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity duration-300" onclick="closeUploadModal()"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Header -->
            <div class="px-8 pt-8 pb-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-800/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-upload text-gray-800 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Upload Bukti Pembayaran</h3>
                            <p class="text-sm text-gray-600 mt-1">Lengkapi transaksi Anda</p>
                        </div>
                    </div>
                    <button onclick="closeUploadModal()" 
                            class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Form -->
            <form id="uploadForm" enctype="multipart/form-data" class="p-8 space-y-6">

                @csrf
                <input type="hidden" id="transaksi_id" name="transaksi_id">
                
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        Upload File Bukti
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <div id="uploadArea" 
                             class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-gray-800 hover:bg-gray-800/5 transition-all duration-300 cursor-pointer">
                            <div id="uploadContent">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                                <p class="font-medium text-gray-900 mb-1">Klik untuk upload</p>
                                <p class="text-sm text-gray-500">atau drag & drop file</p>
                                <p class="text-xs text-gray-400 mt-3">JPG, PNG, PDF (maks. 2MB)</p>
                            </div>
                            <input id="fileInput" 
                                   name="bukti_pembayaran" 
                                   type="file" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" 
                                   accept="image/*,.pdf"
                                   required>
                        </div>
                    </div>
                </div>
                
                <!-- Information -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">Pastikan bukti transfer jelas menunjukkan:</h4>
                            <ul class="space-y-1.5 text-sm text-blue-800">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-blue-600 text-xs"></i>
                                    <span>Nama bank pengirim & penerima</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-blue-600 text-xs"></i>
                                    <span>Nomor rekening lengkap</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-blue-600 text-xs"></i>
                                    <span>Jumlah transfer sesuai tagihan</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-blue-600 text-xs"></i>
                                    <span>Tanggal & waktu transfer</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        id="submitBtn"
                        class="w-full py-3.5 bg-gradient-to-r from-gray-800 to-gray-800-dark text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                    <i class="fas fa-upload"></i>
                    <span>Upload Bukti Pembayaran</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter dropdown toggle
const filterButton = document.getElementById('filterButton');
const filterDropdown = document.getElementById('filterDropdown');

filterButton.addEventListener('click', function(e) {
    e.stopPropagation();
    filterDropdown.classList.toggle('hidden');
    
    if (!filterDropdown.classList.contains('hidden')) {
        setTimeout(() => {
            const handleClickOutside = (e) => {
                if (!filterDropdown.contains(e.target) && !filterButton.contains(e.target)) {
                    filterDropdown.classList.add('hidden');
                    document.removeEventListener('click', handleClickOutside);
                }
            };
            document.addEventListener('click', handleClickOutside);
        }, 10);
    }
});

// Upload modal functions
function showUploadModal(transaksiId) {
    const modal = document.getElementById('uploadModal');
    const modalContent = document.getElementById('modalContent');
    const transaksiIdInput = document.getElementById('transaksi_id');
    
    transaksiIdInput.value = transaksiId;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset form
    document.getElementById('uploadForm').reset();
    document.getElementById('uploadContent').innerHTML = `
        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
        <p class="font-medium text-gray-900 mb-1">Klik untuk upload</p>
        <p class="text-sm text-gray-500">atau drag & drop file</p>
        <p class="text-xs text-gray-400 mt-3">JPG, PNG, PDF (maks. 2MB)</p>
    `;
    
    // Animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

// File upload handling
const fileInput = document.getElementById('fileInput');
const uploadArea = document.getElementById('uploadArea');
const uploadContent = document.getElementById('uploadContent');

fileInput.addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            showToast('error', 'File terlalu besar. Maksimal 2MB.');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadContent.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                        ${file.type.startsWith('image/') 
                            ? `<img src="${e.target.result}" class="w-full h-full object-cover">`
                            : `<i class="fas fa-file-pdf text-red-500 text-2xl"></i>`
                        }
                    </div>
                    <div class="text-left">
                        <p class="font-medium text-gray-900 truncate max-w-[200px]">${file.name}</p>
                        <p class="text-sm text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                        <button type="button" onclick="resetFileUpload()" 
                                class="text-xs text-red-600 hover:text-red-700 mt-2 inline-flex items-center gap-1">
                            <i class="fas fa-times"></i>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            `;
            uploadArea.classList.add('border-green-400', 'bg-green-50');
        };
        reader.readAsDataURL(file);
    }
});

function resetFileUpload() {
    fileInput.value = '';
    uploadContent.innerHTML = `
        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
        <p class="font-medium text-gray-900 mb-1">Klik untuk upload</p>
        <p class="text-sm text-gray-500">atau drag & drop file</p>
        <p class="text-xs text-gray-400 mt-3">JPG, PNG, PDF (maks. 2MB)</p>
    `;
    uploadArea.classList.remove('border-green-400', 'bg-green-50');
}

// Upload form submission
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = `
        <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        <span>Mengupload...</span>
    `;
    submitBtn.disabled = true;
    
    try {
        const response = await fetch(`/user/transaksi/${formData.get('transaksi_id')}/upload-bukti`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
        
        const data = await response.json();
        
        // Restore button
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
        
        if (data.success) {
            // Show success
            showToast('success', data.message, true);
            
            // Close modal after delay
            setTimeout(() => {
                closeUploadModal();
                setTimeout(() => window.location.reload(), 300);
            }, 1500);
        } else {
            showToast('error', data.message, false);
        }
    } catch (error) {
        // Restore button
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
        
        console.error('Upload error:', error);
        showToast('error', 'Terjadi kesalahan saat mengupload', false);
    }
});

// Cancel transaction
async function cancelTransaction(transaksiId) {
    if (!await showConfirmDialog('Batalkan Transaksi', 'Apakah Anda yakin ingin membatalkan transaksi ini? Stok akan dikembalikan.', 'warning')) {
        return;
    }
    
    try {
        const response = await fetch(`/user/transaksi/${transaksiId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('success', data.message, true);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message, false);
        }
    } catch (error) {
        console.error('Cancel error:', error);
        showToast('error', 'Terjadi kesalahan', false);
    }
}

// Toast notification
function showToast(type, message, autoClose = true) {
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const colors = {
        success: 'bg-green-500 border-green-400',
        error: 'bg-red-500 border-red-400',
        warning: 'bg-yellow-500 border-yellow-400',
        info: 'bg-blue-500 border-blue-400'
    };
    
    toast.id = toastId;
    toast.className = `fixed top-6 right-6 px-6 py-4 rounded-xl shadow-lg z-50 transform transition-all duration-300 translate-x-full ${colors[type]} text-white border`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-${icons[type]} text-xl"></i>
            <div>
                <p class="font-medium">${message}</p>
            </div>
            <button onclick="document.getElementById('${toastId}').remove()" 
                    class="ml-4 text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Slide in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 10);
    
    // Auto close
    if (autoClose) {
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
}

// Confirm dialog
function showConfirmDialog(title, message, type = 'warning') {
    return new Promise((resolve) => {
        const dialogId = 'confirm-' + Date.now();
        const dialog = document.createElement('div');
        
        const icons = {
            warning: 'exclamation-triangle',
            danger: 'exclamation-circle',
            info: 'info-circle'
        };
        
        const colors = {
            warning: 'text-yellow-600 bg-yellow-50 border-yellow-200',
            danger: 'text-red-600 bg-red-50 border-red-200',
            info: 'text-blue-600 bg-blue-50 border-blue-200'
        };
        
        dialog.id = dialogId;
        dialog.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50';
        dialog.innerHTML = `
            <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="${dialogId}-content">
                <div class="p-8">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full ${colors[type].split(' ')[1]} flex items-center justify-center mb-6">
                            <i class="fas fa-${icons[type]} text-2xl ${colors[type].split(' ')[0]}"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">${title}</h3>
                        <p class="text-gray-600 mb-8">${message}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="closeConfirmDialog('${dialogId}', false)" 
                                class="py-3 px-6 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="button" onclick="closeConfirmDialog('${dialogId}', true)" 
                                class="py-3 px-6 bg-gradient-to-r ${type === 'warning' ? 'from-yellow-500 to-yellow-600' : 'from-red-500 to-red-600'} text-white font-medium rounded-xl hover:shadow-lg transition-all">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(dialog);
        
        // Animate in
        setTimeout(() => {
            document.getElementById(`${dialogId}-content`).classList.remove('scale-95', 'opacity-0');
            document.getElementById(`${dialogId}-content`).classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Store resolve function
        window.confirmResolve = resolve;
    });
}

function closeConfirmDialog(dialogId, result) {
    const dialog = document.getElementById(dialogId);
    const content = document.getElementById(`${dialogId}-content`);
    
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        dialog.remove();
        window.confirmResolve?.(result);
        window.confirmResolve = null;
    }, 300);
}

// Initialize animations
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
            easing: 'ease-out-cubic'
        });
    }
    
    // Close modals on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUploadModal();
            filterDropdown.classList.add('hidden');
        }
    });
    
    // File upload drag & drop
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('border-gray-800', 'bg-gray-800/10');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('border-gray-800', 'bg-gray-800/10');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endpush