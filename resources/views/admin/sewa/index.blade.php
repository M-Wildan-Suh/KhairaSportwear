@extends('admin.layouts.app')

@section('title', 'Manajemen Sewa')

@section('page-title', 'Manajemen Sewa')

@section('breadcrumbs')
    @php
        $breadcrumbs = [['url' => route('admin.dashboard'), 'label' => 'Sewa'], ['label' => 'Sewa']];
    @endphp
@endsection

@section('content')
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <p class="text-gray-800">Total {{ $sewas->total() }} transaksi sewa</p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Search -->
            <form action="{{ route('admin.sewa.index') }}" method="GET" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent w-64"
                        placeholder="Cari kode sewa/nama...">
                </div>
            </form>

            <!-- Filter Dropdown -->
            <div class="relative">
                <button id="filterDropdownButton"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center">
                    <i class="fas fa-filter mr-2 text-gray-600"></i>
                    <span>Filter</span>
                </button>

                <div id="filterDropdown"
                    class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 z-10">
                    <div class="p-4 space-y-4">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Sewa</label>
                            <select name="status"
                                onchange="window.location.href='{{ route('admin.sewa.index') }}?status='+this.value"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat
                                </option>
                                <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>
                                    Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div>
                            <a href="{{ route('admin.sewa.index') }}"
                                class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                Reset Filter
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Active Rentals -->
            <a href="{{ route('admin.sewa.aktif') }}"
                class="px-4 py-2 border border-purple-500 text-purple-600 hover:bg-purple-50 rounded-lg flex items-center transition-colors">
                <i class="fas fa-running mr-2"></i> Sewa Aktif
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sewa</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($totalSewa) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mr-3">
                    <i class="fas fa-play-circle text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Sewa Aktif</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($aktifCount) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Terlambat</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($terlambatCount) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendapatan</p>
                    <p class="text-2xl font-bold text-white">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">KODE
                            SEWA</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            PELANGGAN</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">PRODUK
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">PERIODE
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">TOTAL
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STATUS
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">AKSI
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sewas as $sewa)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                            <!-- Kode Sewa -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-alt text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 font-mono">{{ $sewa->kode_sewa }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $sewa->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Pelanggan -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                        {{ strtoupper(substr($sewa->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $sewa->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $sewa->user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Produk -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($sewa->produk->gambar)
                                        <img class="w-10 h-10 rounded-lg object-cover border border-gray-200 mr-3"
                                            src="{{ $sewa->produk->gambar_url }}" alt="{{ $sewa->produk->nama }}">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200 mr-3">
                                            <i class="fas fa-box text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $sewa->produk->nama }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $sewa->durasi }} hari • {{ $sewa->jumlah_hari }} unit
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Periode -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-play text-green-500 mr-2 text-xs"></i>
                                        <span class="text-gray-300">{{ $sewa->tanggal_mulai->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-flag-checkered text-blue-500 mr-2 text-xs"></i>
                                        <span
                                            class="text-gray-300">{{ $sewa->tanggal_kembali_rencana->format('d M Y') }}</span>
                                    </div>
                                    @if ($sewa->tanggal_kembali_aktual)
                                        <div class="flex items-center text-xs">
                                            <i class="fas fa-check-circle text-purple-500 mr-2"></i>
                                            <span class="text-gray-300">Kembali:
                                                {{ $sewa->tanggal_kembali_aktual->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}
                                </div>
                                @if ($sewa->denda > 0)
                                    <div class="text-xs text-rose-600 mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Denda: Rp {{ number_format($sewa->denda, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'selesai' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'terlambat' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'dibatalkan' => 'bg-gray-100 text-gray-700 border-gray-200',
                                    ];
                                    $statusIcons = [
                                        'aktif' => 'fas fa-play-circle',
                                        'selesai' => 'fas fa-check-circle',
                                        'terlambat' => 'fas fa-clock',
                                        'dibatalkan' => 'fas fa-times-circle',
                                    ];
                                @endphp
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColors[$sewa->status] ?? 'bg-gray-50 text-gray-700' }}">
                                    <i class="{{ $statusIcons[$sewa->status] ?? 'fas fa-question-circle' }} mr-1.5"></i>
                                    {{ ucfirst($sewa->status) }}
                                </span>

                                @if ($sewa->pengembalian)
                                    <br>
                                    <span
                                        class=" mt-1 inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColors[$sewa->status] ?? 'bg-gray-50 text-gray-700' }}">
                                        <i
                                            class="{{ $sewa->pengembalian->status == 'selesai' ? 'fas fa-question-circle' : 'fas fa-times-circle'}} mr-1.5 capitalize"></i>
                                            {{ $sewa->pengembalian->status == 'selesai' ? 'Sudah di Verif' : 'Belum di Verif' }}
                                    </span>
                                @endif

                                @if ($sewa->status == 'aktif')
                                    @php
                                        $daysLeft = $sewa->sisa_hari;
                                        $bgColor =
                                            $daysLeft <= 0
                                                ? 'bg-rose-100 text-rose-700'
                                                : ($daysLeft < 3
                                                    ? 'bg-amber-100 text-amber-700'
                                                    : 'bg-emerald-100 text-emerald-700');
                                        $icon =
                                            $daysLeft <= 0
                                                ? 'fas fa-exclamation-triangle'
                                                : ($daysLeft < 3
                                                    ? 'fas fa-clock'
                                                    : 'fas fa-calendar-check');
                                    @endphp
                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $bgColor }}">
                                            <i class="{{ $icon }} mr-1"></i>
                                            {{ $daysLeft <= 0 ? 'Hari ini' : ($daysLeft == 1 ? 'Besok' : $daysLeft . ' hari') }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.sewa.show', $sewa->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors"
                                        title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    {{-- @if ($sewa->denda > 0)
                                        <button onclick="showDendaInfo({{ $sewa->id }}, {{ $sewa->denda }})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 transition-colors"
                                            title="Lihat Denda">
                                            <i class="fas fa-money-bill-wave text-sm"></i>
                                        </button>
                                    @endif --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="text-center">
                                    <div
                                        class="mx-auto w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-calendar-alt text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-white mb-2">Belum ada transaksi sewa</h3>
                                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Belum ada produk yang disewa oleh
                                        pelanggan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($sewas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $sewas->firstItem() ?? 0 }}</span> -
                        <span class="font-semibold">{{ $sewas->lastItem() ?? 0 }}</span> dari
                        <span class="font-semibold">{{ $sewas->total() }}</span> sewa
                    </div>
                    <div>
                        {{ $sewas->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        /* Filter Dropdown Animation */
        #filterDropdown {
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Status Colors */
        .bg-emerald-50 {
            background-color: #ecfdf5;
        }

        .text-emerald-700 {
            color: #047857;
        }

        .border-emerald-200 {
            border-color: #a7f3d0;
        }

        .bg-rose-50 {
            background-color: #fff1f2;
        }

        .text-rose-700 {
            color: #be123c;
        }

        .border-rose-200 {
            border-color: #fecdd3;
        }

        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .text-amber-700 {
            color: #b45309;
        }

        .border-amber-200 {
            border-color: #fde68a;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .text-blue-700 {
            color: #1d4ed8;
        }

        .border-blue-200 {
            border-color: #bfdbfe;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Filter Dropdown Toggle
        document.getElementById('filterDropdownButton').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('filterDropdown').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterDropdown');
            const button = document.getElementById('filterDropdownButton');

            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Show denda info
        function showDendaInfo(sewaId, dendaAmount) {
            Swal.fire({
                title: 'Informasi Denda',
                html: `Denda sebesar: <b>Rp ${dendaAmount.toLocaleString('id-ID')}</b><br><br>
                  Denda dihitung berdasarkan keterlambatan pengembalian.`,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#1A365D'
            });
        }

        // Open Pengembalian Modal
        function openPengembalianModal(sewaId, kodeSewa) {
            Swal.fire({
                title: 'Pengembalian Sewa',
                html: `Konfirmasi pengembalian untuk:<br>
                  <b>${kodeSewa}</b><br><br>
                  <form id="pengembalianForm">
                      <div class="mb-3">
                          <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali Aktual</label>
                          <input type="date" name="tanggal_kembali_aktual" 
                                 class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                                 value="{{ date('Y-m-d') }}" required>
                      </div>
                      <div class="mb-3">
                          <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Produk</label>
                          <select name="kondisi" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                              <option value="baik">Baik</option>
                              <option value="rusak_ringan">Rusak Ringan</option>
                              <option value="rusak_berat">Rusak Berat</option>
                          </select>
                      </div>
                      <div class="mb-3">
                          <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                          <textarea name="catatan" rows="2" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    placeholder="Catatan pengembalian..."></textarea>
                      </div>
                  </form>`,
                showCancelButton: true,
                confirmButtonText: 'Konfirmasi Pengembalian',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const form = document.getElementById('pengembalianForm');
                    const formData = new FormData(form);

                    return fetch(`/admin/sewa/${sewaId}/pengembalian`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(Object.fromEntries(formData))
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Berhasil!',
                        'Pengembalian berhasil dicatat.',
                        'success'
                    ).then(() => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>
@endpush
