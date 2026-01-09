@props(['type' => 'info', 'dismissible' => true])

@php
$alertClasses = [
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'danger' => 'bg-red-50 border-red-200 text-red-800',
    'dark' => 'bg-gray-800 border-gray-900 text-white'
];

$iconClasses = [
    'info' => 'fas fa-info-circle text-blue-500',
    'success' => 'fas fa-check-circle text-green-500',
    'warning' => 'fas fa-exclamation-triangle text-yellow-500',
    'danger' => 'fas fa-times-circle text-red-500',
    'dark' => 'fas fa-bell text-gray-300'
];
@endphp

<div class="alert-custom border-l-4 rounded-lg p-4 mb-4 {{ $alertClasses[$type] }}"
     role="alert"
     x-data="{ show: true }"
     x-show="show"
     x-transition>
    <div class="d-flex align-items-start">
        <i class="{{ $iconClasses[$type] }} mt-1 mr-3 fa-lg"></i>
        <div class="flex-grow-1">
            {{ $slot }}
        </div>
        @if($dismissible)
        <button type="button" 
                class="btn-close ms-3"
                @click="show = false"
                aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>
</div>

@push('styles')
<style>
.alert-custom {
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.alert-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.btn-close {
    background: none;
    border: none;
    opacity: 0.5;
    transition: opacity 0.3s ease;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close:hover {
    opacity: 1;
}
</style>
@endpush