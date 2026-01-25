@extends('layouts.app')

@section('title', 'Data Subkriteria - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Subkriteria</h1>
        <p class="fs-6 mb-4">Merinci setiap kriteria menjadi poin-poin terukur menggunakan skala 1-5 untuk penilaian yang
            lebih detail.</p>



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

        <!-- Dynamic Tables Container -->
        <div class="py-4">
            @foreach($kriterias as $kriteria)
                <div class="card mb-4 shadow-sm border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">Kriteria: {{ $kriteria->nama_kriteria }}</h5>
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="openAddModal({{ $kriteria->id_kriteria }})">
                            <i class="ti ti-plus"></i> Tambah Subkriteria
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle display datatable-sub"
                                style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Subkriteria</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kriteria->subkriteria as $index => $sub)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $sub->nama_subkriteria }}</td>
                                            <td>{{ $sub->nilai }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1"
                                                    onclick="editSubkriteria({{ $sub->id_subkriteria }}, {{ $sub->id_kriteria }}, '{{ $sub->nama_subkriteria }}', '{{ $sub->nilai }}')">
                                                    Edit
                                                </button>
                                                <form action="{{ route('subkriteria.destroy', $sub->id_subkriteria) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($kriteria->subkriteria->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada data subkriteria</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
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
                                    placeholder="contoh: Sangat Baik" required>
                            </div>

                            <div class="mb-3">
                                <label for="nilai" class="form-label">Nilai</label>
                                <input type="number" step="0.01" class="form-control" id="nilai" name="nilai" required
                                    placeholder="contoh: 5">
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
            // Inisialisasi DataTable untuk setiap tabel subkriteria yg memiliki rows
            $('.datatable-sub').each(function () {
                // Hanya init jika ada data row (selain empty placeholder)
                if ($(this).find('tbody tr td').length > 1) {
                    $(this).DataTable({
                        paging: false,
                        searching: false, // Matikan pencarian per tabel kecil
                        info: false
                    });
                }
            });
        });

        // Initialize Modal
        var subkriteriaModal = new bootstrap.Modal(document.getElementById('subkriteriaModal'));

        function openAddModal(idKriteria) {
            resetForm();
            if (idKriteria) {
                document.getElementById('id_kriteria').value = idKriteria;
            }
            subkriteriaModal.show();
        }

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Subkriteria';
            document.getElementById('subkriteriaForm').action = '{{ route("subkriteria.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('id_kriteria').value = '';
            document.getElementById('nama_subkriteria').value = '';
            document.getElementById('nilai').value = '';
        }

        function editSubkriteria(id, idKriteria, nama, nilai) {
            resetForm();
            document.getElementById('modalTitle').innerText = 'Ubah Subkriteria';
            document.getElementById('subkriteriaForm').action = '/subkriteria/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('id_kriteria').value = idKriteria;
            document.getElementById('nama_subkriteria').value = nama;
            document.getElementById('nilai').value = nilai;

            subkriteriaModal.show();
        }
    </script>
@endpush