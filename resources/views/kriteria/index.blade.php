@extends('layouts.app')

@section('title', 'Data Kriteria - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Kriteria</h1>
        <p class="fs-6 mb-4">Berisi aspek-aspek penilaian yang menjadi dasar untuk merekomendasikan program studi.</p>

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

        <button type="button" class="btn btn-primary m-1 mt-3" data-bs-toggle="modal" data-bs-target="#kriteriaModal"
            onclick="resetForm()">
            Tambah Kriteria
        </button>

        <div class="py-6 text-center">
            <table id="myTableKriteria" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Kriteria</th>
                        <th>Nama Kriteria</th>
                        <th>Jenis (Benefit/Cost)</th>
                        <th>Bobot (%)</th>
                        <th>Aksi (Ubah/Hapus)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kriterias as $index => $kriteria)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kriteria->kode_kriteria }}</td>
                            <td>{{ $kriteria->nama_kriteria }}</td>
                            <td>
                                <span class="badge {{ $kriteria->jenis == 'Benefit' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $kriteria->jenis }}
                                </span>
                            </td>
                            <td>{{ $kriteria->bobot }}%</td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick="editKriteria({{ $kriteria->id_kriteria }}, '{{ $kriteria->kode_kriteria }}', '{{ $kriteria->nama_kriteria }}', '{{ $kriteria->jenis }}', '{{ $kriteria->bobot }}')">
                                    <i class="ti ti-edit"></i> Ubah
                                </button>
                                <form action="{{ route('kriteria.destroy', $kriteria->id_kriteria) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kriteria ini?')">
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

        <!-- Modal for adding/editing kriteria -->
        <div class="modal fade" id="kriteriaModal" tabindex="-1" aria-labelledby="kriteriaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Kriteria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="kriteriaForm" method="POST" action="{{ route('kriteria.store') }}">
                            @csrf
                            <input type="hidden" id="formMethod" name="_method" value="POST">
                            <input type="hidden" id="id_kriteria" name="id_kriteria">

                            <div class="mb-3">
                                <label for="kode_kriteria" class="form-label">Kode Kriteria</label>
                                <input type="text" class="form-control" id="kode_kriteria" name="kode_kriteria"
                                    placeholder="contoh: K1, C01" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                                <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" required>
                            </div>

                            <div class="mb-3">
                                <label for="jenis" class="form-label">Tipe</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="Benefit">Benefit</option>
                                    <option value="Cost">Cost</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="bobot" class="form-label">Bobot</label>
                                <input type="text" class="form-control" id="bobot" name="bobot" required
                                    pattern="[0-9]+([.][0-9]{1,2})?" placeholder="contoh: 30, 40, 45.5">
                                <small class="text-muted">Gunakan titik (.) untuk desimal (contoh: 25.5)</small>
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
            $('#myTableKriteria').DataTable();
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Kriteria';
            document.getElementById('kriteriaForm').action = '{{ route("kriteria.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('id_kriteria').value = '';
            document.getElementById('kode_kriteria').value = '';
            document.getElementById('nama_kriteria').value = '';
            document.getElementById('jenis').value = '';
            document.getElementById('bobot').value = '';
        }

        function editKriteria(id, kode, nama, jenis, bobot) {
            document.getElementById('modalTitle').innerText = 'Ubah Kriteria';
            document.getElementById('kriteriaForm').action = '/kriteria/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('id_kriteria').value = id;
            document.getElementById('kode_kriteria').value = kode;
            document.getElementById('nama_kriteria').value = nama;
            document.getElementById('jenis').value = jenis;
            document.getElementById('bobot').value = bobot;

            var modal = new bootstrap.Modal(document.getElementById('kriteriaModal'));
            modal.show();
        }
    </script>
@endpush