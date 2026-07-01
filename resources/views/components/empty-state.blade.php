{{--
    Komponen empty state reusable.
    Pemakaian:
        <x-empty-state
            icon="ti ti-database"
            title="Belum ada data"
            message="Tambahkan data terlebih dahulu"
            action-url="/user/create"
            action-label="Tambah User"
        />
--}}
@props([
    'icon' => 'ti ti-inbox',
    'title' => 'Belum ada data',
    'message' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div class="text-center py-5">
    <div class="empty-state-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <h6 class="fw-semibold mb-1">{{ $title }}</h6>
    @if($message)
        <p class="text-muted small mb-3">{{ $message }}</p>
    @endif
    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i>{{ $actionLabel }}
        </a>
    @endif
</div>
