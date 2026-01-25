@extends('layouts.app')

@section('title', 'Data Penilaian Saya')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Penilaian Saya</h1>
        <p class="fs-6 mb-4">Halaman bagi Anda untuk mengisi kuesioner penjurusan sesuai dengan minat dan preferensi
            pribadi.</p>

        <div class="card mb-4 bg-light-info shadow-none overflow-hidden border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="fw-semibold mb-1">Selamat datang, {{ Auth::user()->nama_user }}</h5>
                        <p class="mb-0">NIS: <strong>{{ Auth::user()->nis ?? '-' }}</strong></p>
                    </div>
                    <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                        <img src="{{ asset('assets/images/backgrounds/rocket.png') }}" alt="" class="img-fluid" width="100">
                    </div>
                </div>
            </div>
        </div>



        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('penilaian.store') }}" method="POST" id="penilaianForm">
            @csrf

            <!-- Sticky Progress Bar -->
            <div class="sticky-top bg-white py-3 border-bottom mb-4" style="z-index: 10;">
                <label class="form-label d-flex justify-content-between">
                    <span>Progres Pengisian Keseluruhan:</span>
                    <span id="progressText" class="fw-bold">0%</span>
                </label>
                <div class="progress" style="height: 20px;">
                    <div id="progressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @php $globalNum = 1; @endphp

                        @foreach($alternatifs as $alternatif)
                            {{-- Optional: Show Alternative Header if needed, but per screenshot it's continuous --}}
                            {{-- <h4 class="mt-4 mb-3 text-primary border-bottom pb-2">Program Studi: {{
                                $alternatif->nama_alternatif }}</h4> --}}

                            @foreach($alternatif->pertanyaan as $pertanyaan)
                                <div class="list-group-item py-4 {{ $loop->last ? '' : 'border-bottom' }}">
                                    <h5 class="mb-3 lh-base">
                                        <span class="badge bg-primary rounded-circle p-2 me-2"
                                            style="width: 32px; height: 32px; text-align: center;">{{ $globalNum++ }}</span>
                                        <span class="text-dark fw-bold">({{ $pertanyaan->kriteria->nama_kriteria }})</span>
                                        {{ $pertanyaan->teks_pertanyaan }}
                                    </h5>

                                    <div class="alert alert-secondary bg-light-secondary border-0 p-3">
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($pertanyaan->kriteria->subkriteria as $sub)
                                                @php
                                                    $isChecked = isset($existingPenilaians[$pertanyaan->id_pertanyaan]) &&
                                                        $existingPenilaians[$pertanyaan->id_pertanyaan] == $sub->id_subkriteria;
                                                @endphp
                                                <div class="form-check me-3">
                                                    <input class="form-check-input kuesioner-radio border-primary" type="radio"
                                                        name="jawaban[{{ $pertanyaan->id_pertanyaan }}]"
                                                        id="q{{ $pertanyaan->id_pertanyaan }}_sub{{ $sub->id_subkriteria }}"
                                                        value="{{ $sub->id_subkriteria }}" {{ $isChecked ? 'checked' : '' }} required>
                                                    <label class="form-check-label"
                                                        for="q{{ $pertanyaan->id_pertanyaan }}_sub{{ $sub->id_subkriteria }}">
                                                        {{ $sub->nama_subkriteria }} <span class="text-muted small">(Nilai:
                                                            {{ $sub->nilai }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="ti ti-device-floppy me-2"></i> Simpan Semua Jawaban
                    </button>
                </div>
            </div>
        </form>

        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Design and Developed by RAUF</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('penilaianForm');
            const radios = document.querySelectorAll('.kuesioner-radio');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            // Count total unique questions
            // Retrieve all names from radios to count logical question groups
            const uniqueNames = new Set(Array.from(radios).map(r => r.name));
            const totalQuestions = uniqueNames.size;

            function updateProgress() {
                // Count how many questions have at least one checked radio
                const checkedNames = new Set(
                    Array.from(document.querySelectorAll('.kuesioner-radio:checked')).map(r => r.name)
                );
                const answeredCount = checkedNames.size;

                const percentage = Math.round((answeredCount / totalQuestions) * 100);

                progressBar.style.width = percentage + '%';
                progressBar.setAttribute('aria-valuenow', percentage);
                progressText.innerText = percentage + '%';

                if (percentage === 100) {
                    progressBar.classList.remove('bg-success');
                    progressBar.classList.add('bg-primary');
                }
            }

            // Initialize progress
            updateProgress();

            // Add event listeners
            radios.forEach(radio => {
                radio.addEventListener('change', updateProgress);
            });

            // Prevent accidental sticky blocking by scrolling a bit if focused behind header
            // Optional: nice to have logic
        });
    </script>
@endpush