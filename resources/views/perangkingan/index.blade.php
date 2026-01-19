@extends('layouts.app')

@section('title', 'Hasil Perangkingan - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Hasil Perangkingan</h1>
        <p class="fs-6 mb-4">Rekomendasi program studi berdasarkan hasil perhitungan metode SMART.</p>

        @if(count($hasil) > 0)
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