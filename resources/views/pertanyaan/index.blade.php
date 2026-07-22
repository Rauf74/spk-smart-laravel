@extends('layouts.app')

@section('title', 'Daftar Pertanyaan - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <x-page-header 
            title="Daftar Pertanyaan" 
            subtitle="Pertanyaan-pertanyaan yang akan dijawab siswa dalam kuesioner penilaian."
            icon="ti-help" 
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

        <!-- Dynamic Tables Container -->
        <div class="py-4">
            @foreach($alternatifs as $alternatif)
                <div class="card mb-4 shadow-sm border border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">Prodi: {{ $alternatif->nama_alternatif }}</h5>
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="openAddModal({{ $alternatif->id_alternatif }})">
                            <i class="ti ti-plus"></i> Tambah Pertanyaan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle display datatable-pert dt-responsive nowrap"
                                style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kriteria</th>
                                        <th>Teks Pertanyaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alternatif->pertanyaan as $index => $pertanyaan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $pertanyaan->kriteria->nama_kriteria ?? '-' }}</td>
                                            <td>{{ $pertanyaan->teks_pertanyaan }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1"
                                                    onclick="editPertanyaan({{ $pertanyaan->id_pertanyaan }}, {{ $pertanyaan->id_kriteria }}, {{ $alternatif->id_alternatif }}, `{{ addslashes($pertanyaan->teks_pertanyaan) }}`)">
                                                    <i class="ti ti-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(
                                                        'Hapus Pertanyaan?',
                                                        '<div class=\'text-start\'>Pertanyaan:<br><em>{{ addslashes($pertanyaan->teks_pertanyaan) }}</em><br><br>akan dihapus permanen.<br><br><small class=\'text-danger\'>⚠️ Penilaian siswa untuk pertanyaan ini akan ikut terhapus.</small></div>',
                                                        '{{ route('pertanyaan.destroy', $pertanyaan->id_pertanyaan) }}'
                                                    )">
                                                    <i class="ti ti-trash"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($alternatif->pertanyaan->isEmpty())
                                        <tr>
                                            <td colspan="4" class="p-0">
                                                <x-empty-state
                                                    icon="ti-help"
                                                    title="Belum ada pertanyaan"
                                                    :message="'Tambahkan pertanyaan untuk ' . $alternatif->nama_alternatif" />
                                            </td>
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
                            <input type="hidden" id="id_alternatif" name="id_alternatif">

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
                                <label for="teks_pertanyaan" class="form-label">Teks Pertanyaan</label>
                                <textarea class="form-control" id="teks_pertanyaan" name="teks_pertanyaan" rows="4" required
                                    placeholder="Contoh: Apakah Anda menyukai mata pelajaran Matematika?"></textarea>
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
        $(document).ready(function () {
            $('.datatable-pert').each(function () {
                if ($(this).find('tbody tr td').length > 1) {
                    $(this).DataTable({
                        paging: false,
                        searching: false,
                        info: false
                    });
                }
            });
        });

        var pertanyaanModal = new bootstrap.Modal(document.getElementById('pertanyaanModal'));

        function openAddModal(idAlternatif) {
            resetForm();
            if (idAlternatif) {
                document.getElementById('id_alternatif').value = idAlternatif;
            }
            pertanyaanModal.show();
        }

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Pertanyaan';
            document.getElementById('pertanyaanForm').action = '{{ route("pertanyaan.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('id_kriteria').value = '';
            document.getElementById('id_alternatif').value = '';
            document.getElementById('teks_pertanyaan').value = '';
        }

        function editPertanyaan(id, idKriteria, idAlternatif, teks) {
            resetForm();
            document.getElementById('modalTitle').innerText = 'Ubah Pertanyaan';
            document.getElementById('pertanyaanForm').action = '/pertanyaan/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('id_kriteria').value = idKriteria;
            document.getElementById('id_alternatif').value = idAlternatif;
            document.getElementById('teks_pertanyaan').value = teks;

            pertanyaanModal.show();
        }
    </script>
@endpush