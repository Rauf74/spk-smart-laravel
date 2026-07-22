@props([
    'icon' => 'ti-folder-off',
    'title' => 'Belum Ada Data',
    'message' => 'Data yang Anda cari saat ini belum tersedia.'
])

<div class="text-center py-5">
    <div class="empty-state-icon">
        <i class="ti {{ $icon }}"></i>
    </div>
    <h5 class="fw-bold mb-1">{{ $title }}</h5>
    <p class="text-muted small mb-3" style="max-width: 400px; margin: 0 auto;">{{ $message }}</p>
    @if($slot->isNotEmpty())
        <div>{{ $slot }}</div>
    @endif
</div>
