@extends('layouts.app')

@section('title', 'Rekap Per Kelas - Guru BK')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="mb-1">Rekap Per Kelas</h1>
            <p class="text-muted mb-0">Ringkasan data penilaian per kelas (berdasarkan 2 digit pertama NIS).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('penilaian.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('penilaian.export') }}" class="btn btn-success">
                <i class="ti ti-file-spreadsheet me-1"></i>Export Semua
            </a>
        </div>
    </div>

    @if($rekapKelas->isEmpty())
        <x-empty-state
            icon="ti ti-school-off"
            title="Belum ada data kelas"
            message="Tambahkan siswa dengan NIS yang valid untuk melihat rekap per kelas." />
    @else
        <div class="row g-3">
            @foreach($rekapKelas as $rekap)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="ti ti-school me-2 text-primary"></i>Kelas {{ $rekap['kelas'] }}
                            </h5>
                            <a href="{{ route('penilaian.export', ['kelas' => $rekap['kelas']]) }}"
                               class="btn btn-sm btn-success"
                               title="Download Excel kelas ini">
                                <i class="ti ti-download"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="text-muted small">Total Siswa</div>
                                    <div class="fs-4 fw-bold">{{ $rekap['total_siswa'] }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Sudah Nilai</div>
                                    <div class="fs-4 fw-bold text-success">{{ $rekap['siswa_sudah_nilai'] }}</div>
                                </div>
                            </div>

                            <hr class="my-2">

                            <h6 class="text-uppercase text-muted small fw-semibold mb-2">Distribusi Rekomendasi</h6>
                            @if($rekap['distribusi']->isNotEmpty())
                                <ul class="list-unstyled mb-0">
                                    @foreach($rekap['distribusi'] as $namaProdi => $jumlah)
                                        <li class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-truncate me-2" style="max-width:160px;" title="{{ $namaProdi }}">
                                                <i class="ti ti-school text-primary me-1"></i>{{ $namaProdi }}
                                            </span>
                                            <span class="badge bg-primary rounded-pill">{{ $jumlah }} siswa</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted small mb-0">Belum ada siswa yang menyelesaikan kuesioner.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
