@extends('layouts.app')

@section('title', 'Form Penilaian - SPK SMART')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Form Penilaian: {{ $alternatif->nama_alternatif }}</h1>
    <p class="fs-6 mb-4">Jawab pertanyaan berikut untuk memberikan penilaian terhadap program studi ini.</p>
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('penilaian.store') }}">
        @csrf
        <input type="hidden" name="id_alternatif" value="{{ $alternatif->id_alternatif }}">
        
        @foreach($pertanyaans as $pertanyaan)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>{{ $pertanyaan->kriteria->nama_kriteria ?? 'Kriteria' }}</strong>
            </div>
            <div class="card-body">
                <p class="card-text">{{ $pertanyaan->teks_pertanyaan }}</p>
                
                <input type="hidden" name="jawaban[{{ $pertanyaan->id_pertanyaan }}][id_kriteria]" value="{{ $pertanyaan->id_kriteria }}">
                <input type="hidden" name="jawaban[{{ $pertanyaan->id_pertanyaan }}][id_pertanyaan]" value="{{ $pertanyaan->id_pertanyaan }}">
                
                <div class="row">
                    @php
                        $subkriterias = $pertanyaan->kriteria->subkriteria ?? collect([]);
                        $existingAnswer = $existingAnswers[$pertanyaan->id_pertanyaan] ?? null;
                    @endphp
                    
                    @foreach($subkriterias as $subkriteria)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                name="jawaban[{{ $pertanyaan->id_pertanyaan }}][id_subkriteria]" 
                                value="{{ $subkriteria->id_subkriteria }}"
                                id="sub_{{ $pertanyaan->id_pertanyaan }}_{{ $subkriteria->id_subkriteria }}"
                                {{ $existingAnswer && $existingAnswer->id_subkriteria == $subkriteria->id_subkriteria ? 'checked' : '' }}
                                required>
                            <label class="form-check-label" for="sub_{{ $pertanyaan->id_pertanyaan }}_{{ $subkriteria->id_subkriteria }}">
                                {{ $subkriteria->nama_subkriteria }} ({{ $subkriteria->nilai }})
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check"></i> Simpan Penilaian
            </button>
            <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<div class="py-6 px-6 text-center">
    <p class="mb-0 fs-4">Design and Developed by RAUF</p>
</div>
@endsection
