@props([
    'type' => 'success',
    'message' => null,
    'dismissible' => true
])

@php
    $bgClass = match($type) {
        'success' => 'alert-success',
        'danger', 'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-primary',
    };
    $iconClass = match($type) {
        'success' => 'ti-check',
        'danger', 'error' => 'ti-alert-circle',
        'warning' => 'ti-alert-triangle',
        'info' => 'ti-info-circle',
        default => 'ti-info-circle',
    };
@endphp

@if($message || $slot->isNotEmpty())
    <div class="alert {{ $bgClass }} d-flex align-items-center {{ $dismissible ? 'alert-dismissible fade show' : '' }} py-2 px-3 mb-3 rounded-3" role="alert">
        <i class="ti {{ $iconClass }} fs-5 me-2"></i>
        <div>
            {{ $message ?? $slot }}
        </div>
        @if($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
    </div>
@endif
