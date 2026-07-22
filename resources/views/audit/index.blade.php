@extends('layouts.app')

@section('title', 'Audit Log - SPK SMART')

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="Audit Log" 
        subtitle="Riwayat perubahan data master: tambah, ubah, hapus."
        icon="ti-history" 
    />

    {{-- Filter --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Action</label>
                    <select name="action" class="form-select">
                        <option value="">Semua</option>
                        @foreach($actions as $key => $label)
                            <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Model</label>
                    <select name="model_type" class="form-select">
                        <option value="">Semua Model</option>
                        @foreach($modelTypes as $type)
                            <option value="{{ $type }}" {{ request('model_type') === $type ? 'selected' : '' }}>
                                {{ class_basename($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                    @if(request('action') || request('model_type'))
                        <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Deskripsi</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <small>{{ $log->created_at->format('d M Y, H:i') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <small class="fw-semibold">{{ $log->user->nama_user }}</small>
                                        <br><small class="text-muted">{{ $log->user->role }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    @if($log->action === 'create')
                                        <span class="badge bg-success"><i class="ti ti-plus me-1"></i>Tambah</span>
                                    @elseif($log->action === 'update')
                                        <span class="badge bg-warning"><i class="ti ti-edit me-1"></i>Ubah</span>
                                    @else
                                        <span class="badge bg-danger"><i class="ti ti-trash me-1"></i>Hapus</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ class_basename($log->model_type) }}
                                    </span>
                                </td>
                                <td>{{ $log->description ?? '-' }}</td>
                                <td><small class="text-muted font-monospace">{{ $log->ip_address ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-0">
                                    <x-empty-state
                                        icon="ti-history"
                                        title="Belum ada aktivitas"
                                        message="Aktivitas CRUD pada data master akan tercatat di sini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }}
                    dari {{ $logs->total() }} aktivitas
                </small>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
