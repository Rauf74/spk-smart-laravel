@extends('layouts.app')

@section('title', 'Kuesioner Penilaian - SPK SMART')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-2">Kuesioner Penilaian</h1>
        <p class="fs-6 mb-4 text-muted">
            Isi setiap faktor penilaian sesuai kondisi kamu. Tidak ada jawaban benar atau salah.
        </p>

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

            {{-- ======== TOP: Overall Progress Card ======== --}}
            <div class="card mb-4 border-0 shadow-sm bg-light-primary">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1 fw-bold">
                                Progres Pengisian
                            </h6>
                            <span class="fs-5 fw-bold text-primary" id="progressText">
                                {{ $answeredCount }} dari {{ $totalPertanyaan }} pertanyaan terjawab
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div class="progress" style="height: 14px;">
                                <div id="progressBar"
                                     class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                     role="progressbar"
                                     style="width: {{ $progressPersen }}%"
                                     data-total="{{ $totalPertanyaan }}">
                                </div>
                            </div>
                            <div class="text-end mt-1">
                                <small id="progressPersen" class="fw-bold text-muted">{{ $progressPersen }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======== STEPPER NAVIGATION ======== --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-3">
                    <nav class="stepper-nav d-flex justify-content-between flex-wrap gap-1" id="stepperNav">
                        @foreach($kriterias as $index => $kriteria)
                            @php
                                // Hitung progress per kriteria
                                $pertanyaanIds = $kriteria->pertanyaans->pluck('id_pertanyaan')->toArray();
                                $answeredPerKriteria = count(array_intersect_key(
                                    $existingPenilaians,
                                    array_flip($pertanyaanIds)
                                ));
                                $totalPerKriteria = count($pertanyaanIds);
                                $kriteriaDone = $answeredPerKriteria === $totalPerKriteria && $totalPerKriteria > 0;
                            @endphp
                            <button type="button"
                                    class="stepper-step btn {{ $index === $currentStep ? 'btn-primary' : ($kriteriaDone ? 'btn-outline-success' : 'btn-outline-secondary') }}"
                                    data-step="{{ $index }}"
                                    id="stepperBtn{{ $index }}"
                                    title="{{ $kriteria->nama_kriteria }} — {{ $answeredPerKriteria }}/{{ $totalPerKriteria }}">
                                <span class="stepper-number">{{ $index + 1 }}</span>
                                <span class="stepper-label d-none d-md-inline">{{ $kriteria->nama_kriteria }}</span>
                                <span class="stepper-check ms-1">
                                    @if($kriteriaDone)
                                        <i class="ti ti-check"></i>
                                    @else
                                        <small class="text-muted">({{ $answeredPerKriteria }}/{{ $totalPerKriteria }})</small>
                                    @endif
                                </span>
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- ======== QUESTION STEPS ======== --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @foreach($kriterias as $stepIndex => $kriteria)
                        <div class="step-panel {{ $stepIndex === $currentStep ? 'active' : '' }}"
                             id="step{{ $stepIndex }}"
                             data-total="{{ $kriteria->pertanyaans->count() }}">

                            <h4 class="mb-1">
                                <span class="badge bg-primary me-2">Faktor {{ $stepIndex + 1 }}</span>
                                {{ $kriteria->nama_kriteria }}
                            </h4>
                            <p class="text-muted mb-4">
                                {{ $kriteria->pertanyaans->count() }} pertanyaan —
                                <span class="step-answered-count fw-bold text-success"
                                      id="stepAnswered{{ $stepIndex }}">0</span> sudah dijawab
                            </p>

                            <div class="list-group list-group-flush">
                                @php $localNum = 1; @endphp
                                @foreach($kriteria->pertanyaans as $pertanyaan)
                                    <div class="list-group-item py-3 {{ $loop->last ? '' : 'border-bottom' }}">
                                        <div class="d-flex mb-2">
                                            <span class="badge bg-light text-dark rounded-circle p-2 me-3"
                                                  style="min-width: 32px; height: 32px; line-height: 16px;">
                                                {{ $localNum++ }}
                                            </span>
                                            <label class="form-label mb-0 lh-base">
                                                <strong>({{ $kriteria->nama_kriteria }})</strong>
                                                {{ $pertanyaan->teks_pertanyaan }}
                                            </label>
                                        </div>

                                        <div class="ms-5 ps-1">
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach($kriteria->subkriteria as $sub)
                                                    @php
                                                        $isChecked = isset($existingPenilaians[$pertanyaan->id_pertanyaan])
                                                            && $existingPenilaians[$pertanyaan->id_pertanyaan] == $sub->id_subkriteria;
                                                    @endphp
                                                    <div class="form-check">
                                                        <input class="form-check-input kuesioner-radio"
                                                               type="radio"
                                                               name="jawaban[{{ $pertanyaan->id_pertanyaan }}]"
                                                               id="q{{ $pertanyaan->id_pertanyaan }}_sub{{ $sub->id_subkriteria }}"
                                                               value="{{ $sub->id_subkriteria }}"
                                                               {{ $isChecked ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                               for="q{{ $pertanyaan->id_pertanyaan }}_sub{{ $sub->id_subkriteria }}">
                                                            {{ $sub->nama_subkriteria }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($kriteria->pertanyaans->isEmpty())
                                    <div class="text-center py-4 text-muted">
                                        <i class="ti ti-mood-empty fs-1 mb-2 d-block"></i>
                                        Belum ada pertanyaan untuk faktor ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ======== BOTTOM NAVIGATION ======== --}}
                <div class="card-footer bg-white border-top p-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4"
                            id="prevBtn" disabled>
                        <i class="ti ti-arrow-left me-2"></i>Sebelumnya
                    </button>

                    <button type="submit" class="btn btn-primary btn-lg px-5"
                            id="nextBtn">
                        Selanjutnya<i class="ti ti-arrow-right ms-2"></i>
                    </button>

                    {{-- Hidden: flag partial save agar backend redirect balik ke penilaian --}}
                    <input type="hidden" name="_partial" id="partialFlag" value="1">
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .stepper-nav {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 8px;
            padding-bottom: 4px;
        }
        .stepper-nav::-webkit-scrollbar {
            height: 4px;
        }
        .stepper-nav::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .stepper-step {
            flex-shrink: 0;
            min-width: 140px;
            text-align: center;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .stepper-step:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .stepper-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            font-weight: bold;
            margin-right: 4px;
            flex-shrink: 0;
        }
        .stepper-check {
            font-size: 0.75rem;
        }
        .btn-outline-success .stepper-number {
            background: rgba(25,135,84,0.15);
        }
        .step-panel {
            display: none;
        }
        .step-panel.active {
            display: block;
            animation: fadeSlideIn 0.3s ease;
        }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        /* klik stepper yang sudah selesai */
        .stepper-step.btn-outline-success {
            cursor: pointer;
        }
        /* Mobile: bigger radio tap targets */
        .kuesioner-radio {
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem;
            min-height: 1.25rem;
            margin-top: 0.15rem;
        }
        .kuesioner-radio + .form-check-label {
            padding-left: 0.5rem;
        }
        @media (max-width: 576px) {
            .kuesioner-radio {
                width: 1.5rem !important;
                height: 1.5rem !important;
                min-width: 1.5rem;
                min-height: 1.5rem;
            }
            .kuesioner-radio + .form-check-label {
                font-size: 0.95rem;
                padding-left: 0.75rem;
            }
            .stepper-step { min-width: 100px; padding: 6px 8px; font-size: 0.78rem; }
            .stepper-number { width: 22px; height: 22px; }
            .card-body { padding: 1rem !important; }
            .gap-3 { gap: 0.5rem !important; }
            .d-flex.flex-wrap.gap-3 { gap: 0.75rem !important; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalSteps = {{ $kriterias->count() }};
            let currentStep = 0;

            // Elements
            const stepPanels = document.querySelectorAll('.step-panel');
            const stepperBtns = document.querySelectorAll('.stepper-step');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const form = document.getElementById('penilaianForm');
            const partialFlag = document.getElementById('partialFlag');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const progressPersenEl = document.getElementById('progressPersen');
            const allRadios = document.querySelectorAll('.kuesioner-radio');

            // ========== STEP NAVIGATION ==========

            function showStep(stepIndex) {
                stepPanels.forEach((panel, i) => {
                    panel.classList.toggle('active', i === stepIndex);
                });
                // Hapus semua class warna Bootstrap, lalu tambah btn-primary ke yang aktif
                stepperBtns.forEach(btn => {
                    btn.classList.remove('btn-primary', 'btn-outline-success', 'btn-outline-secondary');
                });
                const activeBtn = document.getElementById('stepperBtn' + stepIndex);
                if (activeBtn) activeBtn.classList.add('btn-primary');

                prevBtn.disabled = (stepIndex === 0);

                if (stepIndex >= totalSteps - 1) {
                    nextBtn.innerHTML = '<i class="ti ti-check me-2"></i>Selesai';
                    nextBtn.classList.add('btn-success');
                    nextBtn.classList.remove('btn-primary');
                } else {
                    nextBtn.innerHTML = 'Selanjutnya<i class="ti ti-arrow-right ms-2"></i>';
                    nextBtn.classList.add('btn-primary');
                    nextBtn.classList.remove('btn-success');
                }

                currentStep = stepIndex;
                // Scroll to top of form
                document.querySelector('.card-body.p-4').scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Refresh stepper state biar outline hijau balik ke step yang sudah selesai
                updateAllProgress();
            }

            stepperBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetStep = parseInt(this.dataset.step);
                    showStep(targetStep);
                });
            });

            prevBtn.addEventListener('click', function () {
                if (currentStep > 0) showStep(currentStep - 1);
            });

            nextBtn.addEventListener('click', function (e) {
                if (currentStep >= totalSteps - 1) {
                    e.preventDefault();

                    // Validasi: semua pertanyaan terjawab?
                    const checkedCount = document.querySelectorAll('.kuesioner-radio:checked').length;
                    if (checkedCount < totalPertanyaan) {
                        const sisa = totalPertanyaan - checkedCount;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Belum Selesai',
                            html: 'Masih ada <strong>' + sisa + '</strong> pertanyaan yang belum dijawab.<br>Lanjutkan mengisi dulu ya.',
                            confirmButtonText: 'Oke',
                            customClass: { popup: 'rounded-4' }
                        });
                        return;
                    }

                    // SweetAlert konfirmasi final submit
                    Swal.fire({
                        title: 'Selesai?',
                        text: 'Kamu yakin sudah selesai? Jawaban bisa diubah lagi nanti.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lihat Hasil!',
                        cancelButtonText: 'Lanjutkan Mengisi',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        customClass: { popup: 'rounded-4' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            partialFlag.remove();
                            form.submit();
                        }
                    });
                }
                // Intermediate steps: biarkan form submit (auto-save partial)
            });

            // ========== PROGRESS TRACKING ==========
            const totalPertanyaan = parseInt(progressBar.dataset.total);

            function updateAllProgress() {
                // Hitung semua jawaban
                const checked = document.querySelectorAll('.kuesioner-radio:checked');
                const answeredSet = new Set(Array.from(checked).map(r => r.name));
                const answeredCount = answeredSet.size;

                // Update overall progress
                const persen = totalPertanyaan > 0 ? Math.round((answeredCount / totalPertanyaan) * 100) : 0;
                progressBar.style.width = persen + '%';
                progressText.textContent = answeredCount + ' dari ' + totalPertanyaan + ' pertanyaan terjawab';
                progressPersenEl.textContent = persen + '%';

                // Update per-step progress
                stepPanels.forEach((panel, stepIdx) => {
                    const totalInStep = parseInt(panel.dataset.total);
                    const radiosInStep = panel.querySelectorAll('.kuesioner-radio:checked');
                    const answeredInStep = new Set(Array.from(radiosInStep).map(r => r.name)).size;
                    const stepAnsweredEl = document.getElementById('stepAnswered' + stepIdx);
                    if (stepAnsweredEl) stepAnsweredEl.textContent = answeredInStep;

                    // Update stepper button status
                    const stepperBtn = document.getElementById('stepperBtn' + stepIdx);
                    if (stepperBtn && stepIdx !== currentStep) {
                        if (answeredInStep === totalInStep && totalInStep > 0) {
                            stepperBtn.classList.remove('btn-outline-secondary');
                            stepperBtn.classList.add('btn-outline-success');
                            const checkSpan = stepperBtn.querySelector('.stepper-check');
                            if (checkSpan) checkSpan.innerHTML = '<i class="ti ti-check"></i>';
                        } else {
                            stepperBtn.classList.remove('btn-outline-success');
                            stepperBtn.classList.add('btn-outline-secondary');
                            const checkSpan = stepperBtn.querySelector('.stepper-check');
                            if (checkSpan) checkSpan.innerHTML = '<small class="text-muted">(' + answeredInStep + '/' + totalInStep + ')</small>';
                        }
                    }
                });
            }

            // Event listeners
            allRadios.forEach(radio => {
                radio.addEventListener('change', updateAllProgress);
            });

            // Init
            showStep({{ $currentStep }});
            updateAllProgress();

            // ========== KEYBOARD NAVIGATION ==========
            document.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowRight' && e.ctrlKey && currentStep < totalSteps - 1) {
                    e.preventDefault();
                    showStep(currentStep + 1);
                }
                if (e.key === 'ArrowLeft' && e.ctrlKey && currentStep > 0) {
                    e.preventDefault();
                    showStep(currentStep - 1);
                }
            });
        });
    </script>
@endpush
