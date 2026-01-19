@extends('layouts.app')

@section('title', 'Data Pertanyaan - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Pertanyaan</h1>
        <p class="fs-6 mb-4">Berisi pertanyaan yang akan diajukan kepada siswa untuk setiap kriteria dan alternatif.</p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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

        <button type="button" class="btn btn-primary m-1 mt-3" data-bs-toggle="modal" data-bs-target="#pertanyaanModal"
            onclick="resetForm()">
            Tambah Pertanyaan
        </button>

        <div class="py-6 text-center">
            <table id="myTablePertanyaan" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Alternatif</th>
                        <th>Teks Pertanyaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pertanyaans as $index => $pertanyaan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pertanyaan->kriteria->nama_kriteria ?? '-' }}</td>
                            <td>{{ $pertanyaan->alternatif->nama_alternatif ?? '-' }}</td>
                            <td>{{ Str::limit($pertanyaan->teks_pertanyaan, 50) }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick="editPertanyaan({{ $pertanyaan->id_pertanyaan }}, {{ $pertanyaan->id_kriteria }}, {{ $pertanyaan->id_alternatif }}, `{{ addslashes($pertanyaan->teks_pertanyaan) }}`)">
                                    <i class="ti ti-edit"></i> Ubah
                                </button>
                                <form action="{{ route('pertanyaan.destroy', $pertanyaan->id_pertanyaan) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="ti ti-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="pertanyaanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="pertanyaanForm" method="POST" action="{{ route('pertanyaan.store') }}">
                            @csrf
                            <input type="hidden" id="formMethod" name="_method" value="POST">

                            <div class="mb-3">
                                <label for="id_kriteria" class="form-label">Kriteria</label>
                                <select class="form-select" id="id_kriteria" name="id_kriteria" required>
                                    <option value="">Pilih Kriteria</option>
                                    @foreach($kriterias as $kriteria)
                                        <option value="{{ $kriteria->id_kriteria }}">{{ $kriteria->nama_kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="id_alternatif" class="form-label">Alternatif</label>
                                <select class="form-select" id="id_alternatif" name="id_alternatif" required>
                                    <option value="">Pilih Alternatif</option>
                                    @foreach($alternatifs as $alternatif)
                                        <option value="{{ $alternatif->id_alternatif }}">{{ $alternatif->nama_alternatif }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="teks_pertanyaan" class="form-label">Teks Pertanyaan</label>
                                <textarea class="form-control" id="teks_pertanyaan" name="teks_pertanyaan" rows="4"
                                    required></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Design and Developed by RAUF</p>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#myTablePertanyaan').DataTable();
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Pertanyaan';
            document.getElementById('pertanyaanForm').action = '{{ route("pertanyaan.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('id_kriteria').value = '';
            document.getElementById('id_alternatif').value = '';
            document.getElementById('teks_pertanyaan').value = '';
        }

        function editPertanyaan(id, idKriteria, idAlternatif, teks) {
            document.getElementById('modalTitle').innerText = 'Ubah Pertanyaan';
            document.getElementById('pertanyaanForm').action = '/pertanyaan/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('id_kriteria').value = idKriteria;
            document.getElementById('id_alternatif').value = idAlternatif;
            document.getElementById('teks_pertanyaan').value = teks;

            var modal = new bootstrap.Modal(document.getElementById('pertanyaanModal'));
            modal.show();
        }
    </script>
@endpush