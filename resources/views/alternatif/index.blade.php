@extends('layouts.app')

@section('title', 'Program Studi - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <x-page-header 
            title="Program Studi" 
            subtitle="Daftar program studi yang tersedia sebagai alternatif rekomendasi."
            icon="ti-clipboard-list" 
        />

        @if($errors->any())
            <x-alert type="danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <button type="button" class="btn btn-primary m-1 mt-3" data-bs-toggle="modal" data-bs-target="#alternatifModal"
            onclick="resetForm()">
            <i class="ti ti-plus me-1"></i> Tambah Alternatif
        </button>

        <div class="py-6 text-center">
            <div class="table-responsive">
                <table id="myTableAlternatif" class="display dt-responsive nowrap js-datatable" style="width:100%">
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
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(
                                            'Hapus Program Studi?',
                                            '<div class=\'text-start\'>Program studi <strong>{{ $alternatif->kode_alternatif }} - {{ $alternatif->nama_alternatif }}</strong> akan dihapus permanen.<br><br><small class=\'text-danger\'>⚠️ Pertanyaan dan penilaian terkait akan ikut terhapus.</small></div>',
                                            '{{ route('alternatif.destroy', $alternatif->id_alternatif) }}'
                                        )">
                                        <i class="ti ti-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="alternatifModal" tabindex="-1" aria-labelledby="alternatifModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Program Studi</h5>
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
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
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