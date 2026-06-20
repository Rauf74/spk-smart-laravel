@extends('layouts.app')

@section('title', 'Hasil Rekomendasi - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <style>
        .rekomendasi-hero {
            background: linear-gradient(135deg, #5D87FF 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
        }
        .rekomendasi-hero .persen-besar {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
        }
        .rekomendasi-hero .label-persen {
            font-size: 1rem;
            opacity: 0.9;
        }
        .top3-card {
            border-radius: 12px;
            transition: transform 0.2s ease;
        }
        .top3-card:hover {
            transform: translateY(-4px);
        }
        .detail-section {
            display: none;
        }
        .detail-section.show {
            display: block;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        /* Print-friendly styles */
        @media print {
            .left-sidebar, .app-header, .btn, .detail-toggle, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info { display: none !important; }
            .body-wrapper { margin-left: 0 !important; }
            .rekomendasi-hero { background: #5D87FF !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
            .detail-section { display: block !important; }
            body { font-size: 12pt; }
            h1 { font-size: 18pt; }
            table { font-size: 10pt; }
            .top3-card { transform: none !important; }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Hasil Rekomendasi</h1>
        <p class="fs-6 mb-4 text-muted">
            Berdasarkan jawaban kuesioner kamu, sistem memberikan rekomendasi program studi yang paling sesuai.
        </p>

        {{-- Dropdown Pilih Siswa (Hanya untuk Guru BK) --}}
        @if(Auth::user()->role === 'Guru BK')
            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <form action="{{ route('perangkingan.index') }}" method="GET" class="d-flex align-items-center flex-grow-1"
                        style="max-width: 600px;">
                        <label for="id_user" class="form-label fw-bold me-3 mb-0 text-nowrap">Target Siswa:</label>
                        <select name="id_user" id="id_user" class="form-select border-primary" onchange="this.form.submit()">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id_user }}" {{ $userId == $user->id_user ? 'selected' : '' }}>
                                    {{ $user->nama_user }} ({{ $user->nis }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <div>
                        <a href="{{ request()->fullUrl() }}" class="btn btn-success">
                            <i class="ti ti-refresh"></i> Refresh Data
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if(count($hasil) > 0)
            {{-- ======== PRIMARY LAYER: Hasil Awam ======== --}}

            {{-- Hero Card Rekomendasi Utama --}}
            <div class="card rekomendasi-hero mb-4 border-0 shadow-lg overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-8 text-center text-md-start">
                            <div class="d-inline-flex align-items-center gap-2 mb-3 bg-white bg-opacity-25 rounded-pill px-3 py-1">
                                <i class="ti ti-trophy"></i>
                                <span class="fw-semibold">Rekomendasi Utama</span>
                            </div>
                            <h2 class="fw-bold mb-2 text-white">{{ $topAlternatif['nama_alternatif'] }}</h2>
                            <p class="mb-3 opacity-75 text-white">
                                Program studi ini paling sesuai berdasarkan penilaian kamu saat ini.
                            </p>

                            @if(count($insightKriterias) > 0)
                                <div class="bg-white bg-opacity-90 rounded-3 p-3 mt-3">
                                    <p class="mb-1 fw-semibold text-dark"><i class="ti ti-bulb me-2 text-warning"></i>Kenapa direkomendasikan?</p>
                                    <p class="mb-0 text-muted">
                                        Hasil ini didasarkan pada skor kuat di
                                        <strong class="text-dark">{{ $insightKriterias[0]['nama'] }}</strong>
                                        @if(count($insightKriterias) > 1)
                                            dan <strong class="text-dark">{{ $insightKriterias[1]['nama'] }}</strong>
                                        @endif.
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-center mt-4 mt-md-0">
                            <div class="persen-besar">{{ $persenKecocokan }}<small>%</small></div>
                            <div class="label-persen">Skor Kecocokan</div>
                            <div class="progress mt-3 bg-white bg-opacity-25" style="height: 10px;">
                                <div class="progress-bar bg-white" style="width: {{ $persenKecocokan }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top 3 Mini --}}
            <div class="row mb-4">
                @foreach(array_slice($hasil, 0, 3) as $index => $item)
                    @php
                        $persen = min(100, round($item['nilai_akhir'] * 100));
                        $badgeClass = $index === 0 ? 'bg-warning text-dark' : ($index === 1 ? 'bg-secondary' : 'bg-info');
                        $icon = $index === 0 ? 'ti-trophy' : ($index === 1 ? 'ti-medal' : 'ti-award');
                    @endphp
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card top3-card h-100 shadow-sm {{ $index === 0 ? 'border-warning border-2' : '' }}">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge {{ $badgeClass }} rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                          style="width: 40px; height: 40px;">
                                        <i class="ti {{ $icon }} fs-5"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Peringkat {{ $index + 1 }}</h6>
                                        <small class="text-muted">{{ $item['nama_alternatif'] }}</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between">
                                    <div>
                                        <span class="fs-3 fw-bold text-primary">{{ $persen }}%</span>
                                        <small class="text-muted d-block">Kecocokan</small>
                                    </div>
                                    @if($index === 0)
                                        <span class="badge bg-success">Sangat Direkomendasikan</span>
                                    @elseif($index === 1)
                                        <span class="badge bg-primary">Direkomendasikan</span>
                                    @else
                                        <span class="badge bg-info text-dark">Cukup</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Toggle Detail + Print --}}
            <div class="text-center mb-4 d-flex flex-wrap justify-content-center gap-3">
                <button type="button" class="btn btn-outline-secondary btn-lg px-5 detail-toggle" id="btnToggleDetail"
                        onclick="document.getElementById('detailSection').classList.toggle('show'); this.classList.toggle('active');">
                    <i class="ti ti-calculator me-2"></i>
                    <span class="label-show">Lihat Detail Perhitungan SMART</span>
                    <span class="label-hide d-none">Sembunyikan Detail</span>
                </button>
                <button type="button" class="btn btn-outline-primary btn-lg px-5" onclick="window.print()">
                    <i class="ti ti-printer me-2"></i>Cetak Hasil
                </button>
                <a href="{{ route('perangkingan.pdf', ['id_user' => $userId]) }}" class="btn btn-primary btn-lg px-5">
                    <i class="ti ti-file-type-pdf me-2"></i>Download PDF
                </a>
            </div>

            {{-- ======== SECONDARY LAYER: Detail Perhitungan SMART ======== --}}
            <div class="detail-section" id="detailSection">
                {{-- Stats Cards Row --}}
                @php
                    $totalAlternatif = count($hasil);
                    $nilaiTertinggi = max(array_column($hasil, 'nilai_akhir'));
                    $nilaiTerendah = min(array_column($hasil, 'nilai_akhir'));
                    $rataRata = array_sum(array_column($hasil, 'nilai_akhir')) / $totalAlternatif;
                @endphp
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50 mb-1">Total Alternatif</h6>
                                        <h2 class="text-white mb-0">{{ $totalAlternatif }}</h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                        <i class="ti ti-list-numbers fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50 mb-1">Nilai Tertinggi</h6>
                                        <h2 class="text-white mb-0">{{ number_format($nilaiTertinggi, 4) }}</h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                        <i class="ti ti-arrow-up fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50 mb-1">Nilai Terendah</h6>
                                        <h2 class="text-white mb-0">{{ number_format($nilaiTerendah, 4) }}</h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                        <i class="ti ti-arrow-down fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50 mb-1">Rata-rata</h6>
                                        <h2 class="text-white mb-0">{{ number_format($rataRata, 4) }}</h2>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                        <i class="ti ti-chart-dots fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top 3 Cards Detail --}}
                <div class="row mb-4">
                    @foreach(array_slice($hasil, 0, 3) as $index => $item)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card h-100 {{ $index == 0 ? 'border-warning' : '' }}">
                                <div class="card-body text-center">
                                    @if($index == 0)
                                        <div class="mb-3">
                                            <i class="ti ti-trophy text-warning" style="font-size: 48px;"></i>
                                        </div>
                                    @elseif($index == 1)
                                        <div class="mb-3">
                                            <i class="ti ti-medal text-secondary" style="font-size: 40px;"></i>
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <i class="ti ti-award text-info" style="font-size: 36px;"></i>
                                        </div>
                                    @endif

                                    <h3 class="mb-2">Peringkat {{ $index + 1 }}</h3>
                                    <h4 class="text-primary mb-2">{{ $item['nama_alternatif'] }}</h4>
                                    <p class="fs-5 mb-0">
                                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') }} fs-6">
                                            Skor: {{ number_format($item['nilai_akhir'], 4) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Complete Ranking Table --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tabel Perangkingan Lengkap</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="rankingTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Kode</th>
                                        <th>Nama Alternatif</th>
                                        <th>Nilai Akhir</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hasil as $index => $item)
                                        <tr class="{{ $index == 0 ? 'table-warning' : '' }}">
                                            <td>
                                                <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : ($index == 2 ? 'info' : 'light text-dark')) }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>{{ $item['kode_alternatif'] }}</td>
                                            <td><strong>{{ $item['nama_alternatif'] }}</strong></td>
                                            <td>{{ number_format($item['nilai_akhir'], 4) }}</td>
                                            <td>
                                                @if($index == 0)
                                                    <span class="badge bg-success">Sangat Direkomendasikan</span>
                                                @elseif($index == 1)
                                                    <span class="badge bg-primary">Direkomendasikan</span>
                                                @elseif($index == 2)
                                                    <span class="badge bg-info">Cukup Direkomendasikan</span>
                                                @else
                                                    <span class="badge bg-secondary">Alternatif Lain</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- Empty State --}}
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="ti ti-clipboard-text text-muted" style="font-size: 80px;"></i>
                    </div>
                    <h4 class="mb-2">Belum Ada Hasil Rekomendasi</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                        Silakan lengkapi kuesioner penilaian terlebih dahulu agar sistem dapat memberikan rekomendasi program studi yang sesuai.
                    </p>
                    <a href="{{ route('penilaian.index') }}" class="btn btn-primary btn-lg px-5">
                        <i class="ti ti-pencil me-2"></i>Mulai Penilaian
                    </a>
                </div>
            </div>
        @endif

        {{-- Catatan Konseling (Guru BK Only) --}}
        @if(Auth::user()->role === 'Guru BK' && isset($userId))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-3">
                                <i class="ti ti-notes me-2 text-primary"></i>Catatan Konseling
                            </h5>
                            <p class="text-muted small mb-3">Tulis catatan hasil konseling untuk siswa ini. Catatan hanya terlihat oleh Guru BK yang membuatnya.</p>
                            <form method="POST" action="{{ route('perangkingan.catatan.store') }}">
                                @csrf
                                <input type="hidden" name="id_user" value="{{ $userId }}">
                                <div class="mb-3">
                                    <textarea class="form-control" id="catatan" name="catatan" rows="4"
                                        placeholder="Contoh: Siswa menunjukkan minat tinggi di bidang teknologi. Direkomendasikan untuk diskusi lebih lanjut dengan orang tua...">{{ $catatanKonseling->catatan ?? '' }}</textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    @if($catatanKonseling)
                                        <small class="text-muted">Terakhir diperbarui: {{ $catatanKonseling->updated_at->format('d M Y H:i') }}</small>
                                    @else
                                        <span></span>
                                    @endif
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-2"></i>Simpan Catatan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#rankingTable').DataTable({
                paging: false,
                searching: false,
                ordering: false
            });
        });

        // Toggle label button detail
        document.getElementById('btnToggleDetail').addEventListener('click', function () {
            const showLabel = this.querySelector('.label-show');
            const hideLabel = this.querySelector('.label-hide');
            showLabel.classList.toggle('d-none');
            hideLabel.classList.toggle('d-none');
        });
    </script>
@endpush
