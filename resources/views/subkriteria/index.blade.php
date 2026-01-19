@extends('layouts.app')

@section('title', 'Data Subkriteria - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Subkriteria</h1>
        <p class="fs-6 mb-4">Berisi pilihan nilai untuk setiap kriteria penilaian.</p>

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

        <button type="button" class="btn btn-primary m-1 mt-3" data-bs-toggle="modal" data-bs-target="#subkriteriaModal"
            onclick="resetForm()">
            Tambah Subkriteria
        </button>

        <div class="py-6 text-center">
            <table id="myTableSubkriteria" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Nama Subkriteria</th>
                        <th>Nilai</th>
                        <th>Aksi (Ubah/Hapus)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subkriterias as $index => $subkriteria)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $subkriteria->kriteria->nama_kriteria ?? '-' }}</td>
                            <td>{{ $subkriteria->nama_subkriteria }}</td>
                            <td>{{ $subkriteria->nilai }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick="editSubkriteria({{ $subkriteria->id_subkriteria }}, {{ $subkriteria->id_kriteria }}, '{{ $subkriteria->nama_subkriteria }}', '{{ $subkriteria->nilai }}')">
                                    <i class="ti ti-edit"></i> Ubah
                                </button>
                                <form action="{{ route('subkriteria.destroy', $subkriteria->id_subkriteria) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus subkriteria ini?')">
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
        <div class="modal fade" id="subkriteriaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Subkriteria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="subkriteriaForm" method="POST" action="{{ route('subkriteria.store') }}">
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
                                <label for="nama_subkriteria" class="form-label">Nama Subkriteria</label>
                                <input type="text" class="form-control" id="nama_subkriteria" name="nama_subkriteria"
                                    placeholder="contoh: Sangat Baik, Baik, Cukup" required>
                            </div>

                            <div class="mb-3">
                                <label for="nilai" class="form-label">Nilai</label>
                                <input type="text" class="form-control" id="nilai" name="nilai" required
                                    pattern="[0-9]+([.][0-9]{1,2})?" placeholder="contoh: 1, 2, 3, 4, 5">
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
            $('#myTableSubkriteria').DataTable();
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Subkriteria';
            document.getElementById('subkriteriaForm').action = '{{ route("subkriteria.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('id_kriteria').value = '';
            document.getElementById('nama_subkriteria').value = '';
            document.getElementById('nilai').value = '';
        }

        function editSubkriteria(id, idKriteria, nama, nilai) {
            document.getElementById('modalTitle').innerText = 'Ubah Subkriteria';
            document.getElementById('subkriteriaForm').action = '/subkriteria/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('id_kriteria').value = idKriteria;
            document.getElementById('nama_subkriteria').value = nama;
            document.getElementById('nilai').value = nilai;

            var modal = new bootstrap.Modal(document.getElementById('subkriteriaModal'));
            modal.show();
        }
    </script>
@endpush