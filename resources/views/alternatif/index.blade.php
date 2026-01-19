@extends('layouts.app')

@section('title', 'Data Alternatif - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Alternatif</h1>
        <p class="fs-6 mb-4">Berisi daftar program studi yang akan direkomendasikan kepada siswa.</p>

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

        <button type="button" class="btn btn-primary m-1 mt-3" data-bs-toggle="modal" data-bs-target="#alternatifModal"
            onclick="resetForm()">
            Tambah Alternatif
        </button>

        <div class="py-6 text-center">
            <table id="myTableAlternatif" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Alternatif</th>
                        <th>Nama Alternatif</th>
                        <th>Aksi (Ubah/Hapus)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alternatifs as $index => $alternatif)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $alternatif->kode_alternatif }}</td>
                            <td>{{ $alternatif->nama_alternatif }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick="editAlternatif({{ $alternatif->id_alternatif }}, '{{ $alternatif->kode_alternatif }}', '{{ $alternatif->nama_alternatif }}')">
                                    <i class="ti ti-edit"></i> Ubah
                                </button>
                                <form action="{{ route('alternatif.destroy', $alternatif->id_alternatif) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alternatif ini?')">
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
        <div class="modal fade" id="alternatifModal" tabindex="-1" aria-labelledby="alternatifModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Alternatif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="alternatifForm" method="POST" action="{{ route('alternatif.store') }}">
                            @csrf
                            <input type="hidden" id="formMethod" name="_method" value="POST">

                            <div class="mb-3">
                                <label for="kode_alternatif" class="form-label">Kode Alternatif</label>
                                <input type="text" class="form-control" id="kode_alternatif" name="kode_alternatif"
                                    placeholder="contoh: A1, A2" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama_alternatif" class="form-label">Nama Alternatif</label>
                                <input type="text" class="form-control" id="nama_alternatif" name="nama_alternatif"
                                    placeholder="contoh: Teknik Informatika" required>
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
            $('#myTableAlternatif').DataTable();
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Alternatif';
            document.getElementById('alternatifForm').action = '{{ route("alternatif.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('kode_alternatif').value = '';
            document.getElementById('nama_alternatif').value = '';
        }

        function editAlternatif(id, kode, nama) {
            document.getElementById('modalTitle').innerText = 'Ubah Alternatif';
            document.getElementById('alternatifForm').action = '/alternatif/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('kode_alternatif').value = kode;
            document.getElementById('nama_alternatif').value = nama;

            var modal = new bootstrap.Modal(document.getElementById('alternatifModal'));
            modal.show();
        }
    </script>
@endpush