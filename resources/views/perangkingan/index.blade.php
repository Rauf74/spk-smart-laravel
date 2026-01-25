@extends('layouts.app')

@section('title', 'Hasil Perangkingan - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Hasil Perangkingan</h1>
        <p class="fs-6 mb-4">Rekomendasi program studi berdasarkan hasil perhitungan metode SMART.</p>

        {{-- Stats Calculation --}}
        @php
            $totalAlternatif = count($hasil);
            $nilaiTertinggi = $totalAlternatif > 0 ? max(array_column($hasil, 'nilai_akhir')) : 0;
            $nilaiTerendah = $totalAlternatif > 0 ? min(array_column($hasil, 'nilai_akhir')) : 0;
            $rataRata = $totalAlternatif > 0 ? array_sum(array_column($hasil, 'nilai_akhir')) / $totalAlternatif : 0;
        @endphp

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
            <!-- Stats Cards Row -->
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
            <div class="row mb-4">
                <!-- Top 3 Cards -->
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
                                    <span
                                        class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') }} fs-6">
                                        Skor: {{ number_format($item['nilai_akhir'], 4) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Complete Ranking Table -->
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
                                            <span
                                                class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : ($index == 2 ? 'info' : 'light text-dark')) }}">
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
        @else
            <div class="alert alert-info">
                <i class="ti ti-info-circle me-2"></i>
                Belum ada data penilaian. Silakan lakukan penilaian terlebih dahulu.
            </div>
        @endif
    </div>

    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Design and Developed by RAUF</p>
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
    </script>
@endpush