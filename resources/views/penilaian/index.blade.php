@extends('layouts.app')

@section('title', 'Data Penilaian - SPK SMART')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Penilaian</h1>
        <p class="fs-6 mb-4">Daftar alternatif yang dapat dinilai. Pilih alternatif untuk melakukan penilaian.</p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            @foreach($alternatifs as $alternatif)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-light-success p-3 d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="ti ti-building text-success fs-5"></i>
                            </div>
                            <h5 class="card-title">{{ $alternatif->nama_alternatif }}</h5>
                            <p class="card-text text-muted">{{ $alternatif->kode_alternatif }}</p>

                            @if(isset($alternatif->sudah_dinilai) && $alternatif->sudah_dinilai)
                                <span class="badge bg-success mb-2">Sudah Dinilai</span>
                            @else
                                <span class="badge bg-warning mb-2">Belum Dinilai</span>
                            @endif

                            <div class="mt-3">
                                <a href="{{ route('penilaian.create', $alternatif->id_alternatif) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="ti ti-pencil"></i>
                                    {{ isset($alternatif->sudah_dinilai) && $alternatif->sudah_dinilai ? 'Edit Penilaian' : 'Mulai Penilaian' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Design and Developed by RAUF</p>
    </div>
@endsection