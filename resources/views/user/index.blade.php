@extends('layouts.app')

@section('title', 'Data User - SPK SMART')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data User</h1>
        <p class="fs-6 mb-4">Berisi daftar pengguna sistem (Guru BK dan Siswa).</p>

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

        <button type="button" class="btn btn-primary m-1 mt-3" data-bs-toggle="modal" data-bs-target="#userModal"
            onclick="resetForm()">
            Tambah User
        </button>

        <div class="py-6 text-center">
            <table id="myTableUser" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>NIS</th>
                        <th>Jenis Kelamin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->nama_user }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'Guru BK' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>{{ $user->nis ?? '-' }}</td>
                            <td>{{ $user->jenis_kelamin ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick="editUser({{ $user->id_user }}, '{{ $user->nama_user }}', '{{ $user->username }}', '{{ $user->role }}', '{{ $user->nis }}', '{{ $user->jenis_kelamin }}')">
                                    <i class="ti ti-edit"></i> Ubah
                                </button>
                                <form action="{{ route('user.destroy', $user->id_user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
        <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm" method="POST" action="{{ route('user.store') }}">
                            @csrf
                            <input type="hidden" id="formMethod" name="_method" value="POST">

                            <div class="mb-3">
                                <label for="nama_user" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_user" name="nama_user" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="Guru BK">Guru BK</option>
                                    <option value="Siswa">Siswa</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS (Khusus Siswa)</label>
                                <input type="text" class="form-control" id="nis" name="nis">
                            </div>

                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
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
            $('#myTableUser').DataTable();
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah User';
            document.getElementById('userForm').action = '{{ route("user.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('nama_user').value = '';
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
            document.getElementById('role').value = '';
            document.getElementById('nis').value = '';
            document.getElementById('jenis_kelamin').value = '';
        }

        function editUser(id, nama, username, role, nis, jenisKelamin) {
            document.getElementById('modalTitle').innerText = 'Ubah User';
            document.getElementById('userForm').action = '/user/' + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('nama_user').value = nama;
            document.getElementById('username').value = username;
            document.getElementById('password').value = '';
            document.getElementById('role').value = role;
            document.getElementById('nis').value = nis;
            document.getElementById('jenis_kelamin').value = jenisKelamin;

            var modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }
    </script>
@endpush