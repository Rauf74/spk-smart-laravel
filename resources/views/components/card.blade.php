@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => 'p-4',
    'cardClass' => 'border-0 shadow-sm mb-4'
])

<div class="card {{ $cardClass }}">
    @if($title || isset($headerActions))
        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between {{ $headerClass }}">
            <div>
                @if($title)
                    <h5 class="card-title fw-bold mb-0">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <p class="card-subtitle text-muted small mt-1 mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($headerActions))
                <div>{{ $headerActions }}</div>
            @endif
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="card-footer bg-transparent border-top-0 pb-4 px-4 pt-0">
            {{ $footer }}
        </div>
    @endif
</div>
