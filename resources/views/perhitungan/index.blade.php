@extends('layouts.app')

@section('title', 'Data Perhitungan - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Perhitungan</h1>
        <p class="fs-6 mb-4">Hasil perhitungan menggunakan metode SMART (Simple Multi-Attribute Rating Technique).</p>

        @if(count($hasil) > 0)
            <!-- Tabel Bobot Normalisasi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bobot Kriteria (Normalisasi)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kriteria</th>
                                    <th>Bobot (%)</th>
                                    <th>Normalisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kriterias as $kriteria)
                                    <tr>
                                        <td>{{ $kriteria->nama_kriteria }}</td>
                                        <td>{{ $kriteria->bobot }}%</td>
                                        <td>{{ number_format($kriteria->bobot / 100, 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Nilai Alternatif per Kriteria -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nilai Alternatif per Kriteria</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Alternatif</th>
                                    @foreach($kriterias as $kriteria)
                                        <th>{{ $kriteria->kode_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasil as $item)
                                    <tr>
                                        <td>{{ $item['nama_alternatif'] }}</td>
                                        @foreach($kriterias as $kriteria)
                                            <td>{{ $item['nilai_kriteria'][$kriteria->id_kriteria] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Utility per Kriteria -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nilai Utility per Kriteria</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Alternatif</th>
                                    @foreach($kriterias as $kriteria)
                                        <th>{{ $kriteria->kode_kriteria }} ({{ $kriteria->jenis }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasil as $item)
                                    <tr>
                                        <td>{{ $item['nama_alternatif'] }}</td>
                                        @foreach($kriterias as $kriteria)
                                            <td>{{ number_format($item['utility'][$kriteria->id_kriteria] ?? 0, 4) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Nilai Akhir -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nilai Akhir (SMART Score)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="hasilTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Alternatif</th>
                                    <th>Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasil as $item)
                                    <tr>
                                        <td>{{ $item['nama_alternatif'] }}</td>
                                        <td><strong>{{ number_format($item['nilai_akhir'], 4) }}</strong></td>
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
            $('#hasilTable').DataTable({
                paging: false,
                searching: false
            });
        });
    </script>
@endpush