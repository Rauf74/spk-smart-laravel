@extends('layouts.app')

@section('title', 'Dashboard - SPK SMART')

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
        <div class="col-lg-3 col-md-6">
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
    </div>

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
                    <div id="kriteriaChart" style="height: 300px;"></div>
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
                    <div id="bobotChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Row -->
    <div class="row">
        <!-- System Information -->
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

        <!-- Recent Activities -->
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

        <!-- Quick Actions & Latest Program Studi -->
        <div class="col-lg-4 d-flex align-items-strech">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h5 class="card-title fw-semibold">Program Studi Terbaru</h5>
                        </div>
                    </div>
                    @if($alternatif_terbaru->count() > 0)
                        @foreach($alternatif_terbaru->take(4) as $alternatif)
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
                        @endforeach
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
                                <a href="{{ route('profile') }}" class="btn btn-outline-primary w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-user-circle me-2"></i>Kelola Profile
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('kriteria.index') }}" class="btn btn-primary w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-list-check me-2"></i>Kelola Kriteria
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('alternatif.index') }}" class="btn btn-success w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-clipboard-list me-2"></i>Kelola Alternatif
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('pertanyaan.index') }}" class="btn btn-info w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-help me-2"></i>Kelola Pertanyaan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('penilaian.index') }}" class="btn btn-warning w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-star me-2"></i>Input Penilaian
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perhitungan.index') }}" class="btn btn-secondary w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-calculator me-2"></i>Lihat Perhitungan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perangkingan.index') }}" class="btn btn-dark w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-trophy me-2"></i>Hasil Perangkingan
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('user.index') }}" class="btn btn-danger w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-users me-2"></i>Kelola User
                                </a>
                            </div>
                        @elseif(Auth::user()->role === 'Siswa')
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('profile') }}" class="btn btn-outline-secondary w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-user-circle me-2"></i>Profile Saya
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('penilaian.index') }}" class="btn btn-primary w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-pencil me-2"></i>Mulai Penilaian
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perhitungan.index') }}" class="btn btn-info w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-calculator me-2"></i>Lihat Proses Hitung
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('perangkingan.index') }}" class="btn btn-success w-100 py-8 fs-4 rounded-2">
                                    <i class="ti ti-trophy me-2"></i>Lihat Hasil Akhir
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
                        height: 300
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
                        height: 300
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