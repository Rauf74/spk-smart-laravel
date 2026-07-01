@extends('layouts.app')

@section('title', 'Dashboard - SPK SMART')

@push('styles')
    <style>
        /* Quick action buttons: proporsional, gak kekecilan/kebesaran */
        .btn-aksi-cepat {
            padding: 1rem 0.75rem;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.3;
            min-height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-aksi-cepat i {
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        @media (max-width: 768px) {
            .btn-aksi-cepat { font-size: 0.875rem; min-height: 56px; padding: 0.75rem; }
        }
        @media (min-width: 769px) {
            .fs-md-4 { font-size: 1.5rem !important; }
        }
        /* Chart responsive height */
        .chart-responsive {
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <!-- Welcome Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg-light-info shadow-none position-relative overflow-hidden">
                <div class="card-body px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h4 class="fw-semibold mb-8">Selamat Datang,
                                {{ Auth::user()->nama_user }}!
                            </h4>
                            <p class="mb-0">Sistem Pendukung Keputusan Rekomendasi Program Studi</p>
                            <p class="mb-0 text-muted">SMK Muhammadiyah 3 Tangerang Selatan</p>
                        </div>
                        <div class="col-3">
                            <div class="text-center mb-n5">
                                <img src="{{ asset('assets/images/backgrounds/rocket.png') }}" alt=""
                                    class="img-fluid mb-n4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Common Stats -->
        <div class="{{ Auth::user()->role === 'Siswa' ? 'col-lg-6' : 'col-lg-3' }} col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-8">
                            <h5 class="card-title mb-9 fw-semibold">Total Kriteria</h5>
                            <h4 class="fw-semibold mb-3">{{ $total_kriteria }}</h4>
                        </div>
                        <div class="col-4">
                            <div class="d-flex justify-content-end">
                                <div
                                    class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-article fs-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="{{ Auth::user()->role === 'Siswa' ? 'col-lg-6' : 'col-lg-3' }} col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-8">
                            <h5 class="card-title mb-9 fw-semibold">Program Studi</h5>
                            <h4 class="fw-semibold mb-3">{{ $total_alternatif }}</h4>
                        </div>
                        <div class="col-4">
                            <div class="d-flex justify-content-end">
                                <div
                                    class="text-white bg-success rounded-circle p-6 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-building fs-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Only Stats (Guru BK) -->
        @if(Auth::user()->role === 'Guru BK')
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-start">
                            <div class="col-8">
                                <h5 class="card-title mb-9 fw-semibold">Sub Kriteria</h5>
                                <h4 class="fw-semibold mb-3">{{ $total_subkriteria }}</h4>
                            </div>
                            <div class="col-4">
                                <div class="d-flex justify-content-end">
                                    <div
                                        class="text-white bg-warning rounded-circle p-6 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-list fs-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-start">
                            <div class="col-8">
                                <h5 class="card-title mb-9 fw-semibold">Siswa Menilai</h5>
                                <h4 class="fw-semibold mb-3">{{ $total_penilaian }} dari {{ $total_siswa }}</h4>
                            </div>
                            <div class="col-4">
                                <div class="d-flex justify-content-end">
                                    <div
                                        class="text-white bg-info rounded-circle p-6 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-check fs-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- STATUS SISWA (Guru BK Only) --}}
    @if(Auth::user()->role === 'Guru BK')
        {{-- ============================================ --}}
        {{-- STATISTIK AGREGAT (B1)                       --}}
        {{-- ============================================ --}}
        <div class="row mb-4">
            {{-- Progress Rata-rata --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-semibold">Progress Rata-rata</small>
                        <h2 class="fw-bold mb-1 mt-1">{{ $avgProgressPercent }}%</h2>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-primary" style="width:{{ $avgProgressPercent }}%"></div>
                        </div>
                        <small class="text-muted">Total siswa: {{ $total_siswa }}</small>
                    </div>
                </div>
            </div>

            {{-- Top Rekomendasi --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-semibold">Top Rekomendasi</small>
                        @if($topRekomendasi)
                            <h5 class="fw-bold mb-1 mt-1 text-truncate" title="{{ $topRekomendasi }}">
                                <i class="ti ti-trophy text-warning me-1"></i>{{ $topRekomendasi }}
                            </h5>
                            <small class="text-muted">Paling banyak dipilih siswa</small>
                        @else
                            <h5 class="fw-bold mb-1 mt-1 text-muted">-</h5>
                            <small class="text-muted">Belum ada siswa yang menyelesaikan kuesioner</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kriteria Terbesar --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-semibold">Kriteria Terbesar</small>
                        @if($topKriterias->isNotEmpty())
                            @php $top1 = $topKriterias->first(); @endphp
                            <h5 class="fw-bold mb-1 mt-1 text-truncate" title="{{ $top1->nama_kriteria }}">
                                {{ $top1->kode_kriteria }}
                            </h5>
                            <small class="text-muted">{{ $top1->bobot }}% bobot</small>
                        @else
                            <h5 class="fw-bold mb-1 mt-1 text-muted">-</h5>
                            <small class="text-muted">Belum ada kriteria</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Aktivitas 7 Hari --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-semibold">Aktivitas 7 Hari</small>
                        <div class="d-flex justify-content-between mt-2">
                            <div class="text-center flex-grow-1">
                                <div class="fw-bold fs-5">{{ $activities7d['user_baru'] }}</div>
                                <small class="text-muted d-block">User</small>
                            </div>
                            <div class="text-center flex-grow-1">
                                <div class="fw-bold fs-5">{{ $activities7d['penilaian_baru'] }}</div>
                                <small class="text-muted d-block">Nilai</small>
                            </div>
                            <div class="text-center flex-grow-1">
                                <div class="fw-bold fs-5">{{ $activities7d['kriteria_baru'] + $activities7d['alternatif_baru'] }}</div>
                                <small class="text-muted d-block">Data</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribusi Rekomendasi --}}
        @if($rekomendasiDistribusi->isNotEmpty())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-3">
                                <i class="ti ti-chart-pie me-2 text-primary"></i>Distribusi Rekomendasi Program Studi
                            </h5>
                            <p class="text-muted small mb-3">Siswa yang sudah menyelesaikan kuesioner dan mendapat rekomendasi program studi.</p>
                            <div class="row g-3">
                                @foreach($rekomendasiDistribusi as $namaProdi => $jumlahSiswa)
                                    <div class="col-md-3 col-6">
                                        <div class="d-flex align-items-center p-3 rounded-3 bg-light">
                                            <i class="ti ti-school text-primary fs-3 me-2"></i>
                                            <div>
                                                <div class="fw-semibold text-truncate" style="max-width:150px;" title="{{ $namaProdi }}">{{ $namaProdi }}</div>
                                                <small class="text-muted">{{ $jumlahSiswa }} siswa</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h5 class="card-title fw-semibold mb-1">Status Penilaian Siswa</h5>
                                <p class="text-muted small mb-0">Total {{ $total_siswa }} siswa &middot; {{ $total_pertanyaan }} pertanyaan per siswa</p>
                            </div>
                            <a href="{{ route('penilaian.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-list me-1"></i>Detail Penilaian
                            </a>
                        </div>

                        {{-- Summary badges --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 rounded-3 bg-light-danger">
                                    <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="ti ti-circle-x fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $belum_isi }}</h5>
                                        <small class="text-muted">Belum Mengisi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 rounded-3 bg-light-warning">
                                    <div class="bg-warning text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="ti ti-loader fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $sedang_isi }}</h5>
                                        <small class="text-muted">Sedang Mengisi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 rounded-3 bg-light-success">
                                    <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="ti ti-circle-check fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $selesai_isi }}</h5>
                                        <small class="text-muted">Selesai</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Search & Filter --}}
                        <form method="GET" action="/" class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="text" name="q" class="form-control" placeholder="Cari nama, username, atau NIS..." value="{{ $search }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="belum"   {{ $statusFilter === 'belum'   ? 'selected' : '' }}>Belum Mengisi</option>
                                    <option value="sedang"  {{ $statusFilter === 'sedang'  ? 'selected' : '' }}>Sedang Mengisi</option>
                                    <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="ti ti-filter me-1"></i>Filter
                                </button>
                                @if($search !== '' || $statusFilter !== '')
                                    <a href="/" class="btn btn-outline-secondary">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                        </form>

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Siswa</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa_status as $index => $siswa)
                                        <tr>
                                            <td>{{ $siswa_status->firstItem() + $index }}</td>
                                            <td class="fw-semibold">{{ $siswa->nama_user }}</td>
                                            <td><small class="text-muted">{{ $siswa->username }}</small></td>
                                            <td>
                                                <span class="badge {{ $siswa->status_class }} d-inline-flex align-items-center gap-1">
                                                    <i class="{{ $siswa->status_icon }}"></i>
                                                    {{ $siswa->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($siswa->status === 'sedang')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height:6px;min-width:60px;">
                                                            <div class="progress-bar bg-warning" style="width:{{ $siswa->progress }}%"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $siswa->progress }}%</small>
                                                    </div>
                                                @elseif($siswa->status === 'selesai')
                                                    <div class="progress" style="height:6px;min-width:60px;">
                                                        <div class="progress-bar bg-success" style="width:100%"></div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($siswa->status === 'selesai')
                                                    <a href="{{ route('perangkingan.index', ['id_user' => $siswa->id_user]) }}" class="btn btn-sm btn-success">
                                                        <i class="ti ti-eye me-1"></i>Lihat Hasil
                                                    </a>
                                                @elseif($siswa->status === 'sedang')
                                                    <a href="{{ route('penilaian.index', ['id_user' => $siswa->id_user]) }}" class="btn btn-sm btn-warning">
                                                        <i class="ti ti-pencil me-1"></i>Lanjutkan
                                                    </a>
                                                @else
                                                    <span class="text-muted small">Menunggu</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="ti ti-users fs-3 mb-2 d-block"></i>
                                                Belum ada data siswa
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($siswa_status->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    Menampilkan {{ $siswa_status->firstItem() }} - {{ $siswa_status->lastItem() }}
                                    dari {{ $siswa_status->total() }} siswa
                                </small>
                                {{ $siswa_status->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Charts Section -->
    <div class="row">
        <!-- Kriteria Distribution Chart -->
        <div class="col-lg-6 d-flex align-items-strech">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h5 class="card-title fw-semibold">Distribusi Jenis Kriteria</h5>
                            <p class="card-subtitle mb-0">Perbandingan kriteria Benefit vs Cost</p>
                        </div>
                    </div>
                    <div id="kriteriaChart" class="chart-responsive" style="min-height:250px;"></div>
                </div>
            </div>
        </div>

        <!-- Bobot Kriteria Chart -->
        <div class="col-lg-6 d-flex align-items-strech">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h5 class="card-title fw-semibold">Bobot Kriteria</h5>
                            <p class="card-subtitle mb-0">Distribusi bobot setiap kriteria</p>
                        </div>
                    </div>
                    <div id="bobotChart" class="chart-responsive" style="min-height:250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Row -->
    <div class="row">
        <!-- Admin Only Info (System Info & Recent Activities) -->
        @if(Auth::user()->role === 'Guru BK')
            <div class="col-lg-4 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                            <div class="mb-3 mb-sm-0">
                                <h5 class="card-title fw-semibold">Informasi Sistem</h5>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="fw-semibold mb-3">{{ $total_users }}</h4>
                                <div class="d-flex align-items-center mb-3">
                                    <span
                                        class="me-1 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-users text-danger"></i>
                                    </span>
                                    <p class="text-dark me-1 fs-3 mb-0">Total Users</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex justify-content-center">
                                    <div id="breakup"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-2">
                                <span
                                    class="me-2 rounded-circle bg-light-primary round-20 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-calculator text-primary"></i>
                                </span>
                                <p class="mb-0 fs-3">Metode SMART</p>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span
                                    class="me-2 rounded-circle bg-light-warning round-20 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-database text-warning"></i>
                                </span>
                                <p class="mb-0 fs-3">Database Connected</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span
                                    class="me-2 rounded-circle bg-light-info round-20 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-chart-bar text-info"></i>
                                </span>
                                <p class="mb-0 fs-3">Rata-rata Bobot: {{ $avg_bobot }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                            <div class="mb-3 mb-sm-0">
                                <h5 class="card-title fw-semibold">Aktivitas Terbaru</h5>
                            </div>
                        </div>
                        @foreach($recent_activities as $activity)
                            <div class="d-flex align-items-center pb-9">
                                <span
                                    class="me-3 round-48 bg-light-{{ $activity['color'] }} rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1 fw-semibold fs-3">{!! $activity['action'] !!}</h6>
                                    <p class="mb-0 text-muted">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                        <div class="py-6 px-6 text-center">
                            <p class="mb-0 fs-4">Menampilkan 4 aktivitas terbaru</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions & Latest Program Studi -->
        <div class="{{ Auth::user()->role === 'Siswa' ? 'col-lg-12' : 'col-lg-4' }} d-flex align-items-strech">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h5 class="card-title fw-semibold">Program Studi Terbaru</h5>
                        </div>
                    </div>
                    @if(count($alternatif_terbaru) > 0)
                        <div class="row">
                            @foreach($alternatif_terbaru as $alternatif)
                                <div class="{{ Auth::user()->role === 'Siswa' ? 'col-md-3' : 'col-12' }}">
                                    <div class="d-flex align-items-center pb-9">
                                        <span
                                            class="me-3 round-48 bg-light-success rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ti ti-building text-success"></i>
                                        </span>
                                        <div>
                                            <h6 class="mb-1 fw-semibold fs-3">
                                                {{ $alternatif->nama_alternatif }}
                                            </h6>
                                            <p class="mb-0 text-muted">Program Studi</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-building-store fs-4 text-muted"></i>
                            <p class="mb-0 text-muted">Belum ada program studi</p>
                        </div>
                    @endif

                    <div class="py-6 px-6 text-center">
                        <a href="{{ route('alternatif.index') }}" class="btn btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-9">Aksi Cepat</h5>
                    <div class="row">
                        @if(Auth::user()->role === 'Guru BK')
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('profile') }}" class="btn btn-outline-primary w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-user-circle"></i>Kelola Profile
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('kriteria.index') }}" class="btn btn-primary w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-list"></i>Kelola Kriteria
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('alternatif.index') }}" class="btn btn-success w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-building"></i>Kelola Alternatif
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('pertanyaan.index') }}" class="btn btn-info w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-help"></i>Kelola Pertanyaan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('penilaian.index') }}" class="btn btn-warning w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-star"></i>Input Penilaian
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perhitungan.index') }}" class="btn btn-secondary w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-calculator"></i>Lihat Perhitungan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perangkingan.index') }}" class="btn btn-dark w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-trophy"></i>Hasil Perangkingan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('user.index') }}" class="btn btn-danger w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-users"></i>Kelola User
                                </a>
                            </div>
                        @else
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('profile') }}" class="btn btn-outline-secondary w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-user-circle"></i>Profile Saya
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('penilaian.index') }}" class="btn btn-primary w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-edit"></i>Isi Kuesioner
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perhitungan.index') }}" class="btn btn-info w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-calculator"></i>Detail Perhitungan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perangkingan.index') }}" class="btn btn-success w-100 rounded-2 btn-aksi-cepat">
                                    <i class="ti ti-trophy"></i>Hasil Rekomendasi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        // Data dari Laravel untuk grafik
        var kriteriaJenisData = @json($kriteria_jenis_data);
        var bobotKriteriaData = @json($bobot_kriteria_data);
        var totalUsers = {{ $total_users }};

        // Inisialisasi dashboard charts
        document.addEventListener('DOMContentLoaded', function () {
            // Kriteria Distribution Chart (Pie Chart)
            if (document.getElementById('kriteriaChart')) {
                var kriteriaLabels = kriteriaJenisData.map(function (item) { return item.jenis; });
                var kriteriaValues = kriteriaJenisData.map(function (item) { return parseInt(item.jumlah); });

                var kriteriaOptions = {
                    series: kriteriaValues,
                    chart: {
                        type: 'pie',
                        height: 'auto',
                        minHeight: 250
                    },
                    labels: kriteriaLabels,
                    colors: ['#5D87FF', '#FA896B'],
                    legend: {
                        position: 'bottom'
                    }
                };

                var kriteriaChart = new ApexCharts(document.getElementById('kriteriaChart'), kriteriaOptions);
                kriteriaChart.render();
            }

            // Bobot Kriteria Chart (Bar Chart)
            if (document.getElementById('bobotChart')) {
                var bobotLabels = bobotKriteriaData.map(function (item) { return item.nama_kriteria; });
                var bobotValues = bobotKriteriaData.map(function (item) { return parseFloat(item.bobot); });

                var bobotOptions = {
                    series: [{
                        name: 'Bobot (%)',
                        data: bobotValues
                    }],
                    chart: {
                        type: 'bar',
                        height: 'auto',
                        minHeight: 250
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 4
                        }
                    },
                    xaxis: {
                        categories: bobotLabels
                    },
                    colors: ['#5D87FF']
                };

                var bobotChart = new ApexCharts(document.getElementById('bobotChart'), bobotOptions);
                bobotChart.render();
            }
        });
    </script>
@endpush