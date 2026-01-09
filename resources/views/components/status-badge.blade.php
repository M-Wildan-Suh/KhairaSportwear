@props(['status', 'type' => 'default'])

@php
$badgeClasses = [
    'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    'diproses' => 'bg-blue-100 text-blue-800 border border-blue-200',
    'dibayar' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
    'dikirim' => 'bg-purple-100 text-purple-800 border border-purple-200',
    'selesai' => 'bg-green-100 text-green-800 border border-green-200',
    'dibatalkan' => 'bg-red-100 text-red-800 border border-red-200',
    'aktif' => 'bg-green-100 text-green-800 border border-green-200',
    'terlambat' => 'bg-red-100 text-red-800 border border-red-200',
    'default' => 'bg-gray-100 text-gray-800 border border-gray-200'
];

$statusText = [
    'pending' => 'Menunggu',
    'diproses' => 'Diproses',
    'dibayar' => 'Dibayar',
    'dikirim' => 'Dikirim',
    'selesai' => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
    'aktif' => 'Aktif',
    'terlambat' => 'Terlambat'
];

$class = $badgeClasses[$status] ?? $badgeClasses[$type];
$text = $statusText[$status] ?? ucfirst($status);
@endphp

<span class="badge-status px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-2 {{ $class }}">
    @if($status === 'selesai' || $status === 'aktif')
    <i class="fas fa-check-circle"></i>
    @elseif($status === 'pending' || $status === 'diproses')
    <i class="fas fa-clock"></i>
    @elseif($status === 'terlambat' || $status === 'dibatalkan')
    <i class="fas fa-exclamation-circle"></i>
    @endif
    {{ $text }}
</span>

@push('styles')
<style>
.badge-status {
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
}

.badge-status:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endpush