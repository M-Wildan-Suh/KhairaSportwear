<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog {{ $size ?? 'modal-dialog-centered' }}">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            @if(isset($title))
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @endif
            
            <!-- Modal Body -->
            <div class="modal-body py-4">
                {{ $slot }}
            </div>
            
            <!-- Modal Footer -->
            @if(isset($footer))
            <div class="modal-footer border-0 pt-0">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.modal-header .btn-close {
    padding: 0.5rem;
    margin: -0.5rem -0.5rem -0.5rem auto;
    background-size: 1.2rem;
}

.modal-body {
    font-size: 0.95rem;
}

.modal-footer {
    background: #f8f9fa;
    border-radius: 0 0 16px 16px;
}
</style>
@endpush