@props([
    'title' => '',
    'subtitle' => null,
    'icon' => 'ti-article'
])

<div class="card bg-light-primary shadow-none position-relative overflow-hidden mb-4 rounded-3">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="fw-semibold mb-1 text-primary">{{ $title }}</h4>
                @if($subtitle)
                    <p class="text-muted small mb-0">{{ $subtitle }}</p>
                @else
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                @endif
            </div>
            <div class="col-3 text-end">
                <i class="ti {{ $icon }} text-primary" style="font-size: 56px; opacity: 0.15;"></i>
            </div>
        </div>
    </div>
</div>
