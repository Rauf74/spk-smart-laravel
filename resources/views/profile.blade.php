@extends('layouts.app')

@section('title', 'Profile - SPK SMART')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb & Header -->
        <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Data Profile</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a class="text-muted"
                                        href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Data Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>



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

        <div class="row">
            <!-- Left Column: Avatar & Quick Links -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            @php
                                $profileImage = asset('assets/images/profile/user-1.jpg');
                                if ($user->jenis_kelamin === 'Laki-laki') {
                                    $profileImage = asset('assets/images/profile/user-male.png');
                                } elseif ($user->jenis_kelamin === 'Perempuan') {
                                    $profileImage = asset('assets/images/profile/user-female.png');
                                }
                            @endphp
                            <div class="mb-3">
                                <div class="d-inline-block position-relative">
                                    <img src="{{ $profileImage }}" alt="Profile" class="rounded-circle" width="120"
                                        height="120">
                                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px;">
                                        <i class="ti ti-check text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                            <h5 class="fw-semibold mb-0">{{ $user->nama_user }}</h5>
                            <p class="text-muted">{{ '@' . $user->username }}</p>

                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editProfileModal">
                                    <i class="ti ti-edit me-1"></i>Edit Profile
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">
                                    <i class="ti ti-lock me-1"></i>Ubah Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Tautan Cepat</h5>
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('dashboard') }}"
                                class="d-flex align-items-center text-decoration-none text-muted">
                                <div
                                    class="me-3 round-40 bg-light-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-home-2 text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">Dashboard</h6>
                                    <p class="mb-0 text-muted fs-3">Kembali ke halaman utama</p>
                                </div>
                            </a>

                            @if($user->role === 'Guru BK')
                                <a href="{{ route('penilaian.index') }}"
                                    class="d-flex align-items-center text-decoration-none text-muted">
                                    <div
                                        class="me-3 round-40 bg-light-info rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti ti-star text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark">Data Penilaian</h6>
                                        <p class="mb-0 text-muted fs-3">Lihat atau tambah penilaian</p>
                                    </div>
                                </a>
                                <a href="{{ route('perangkingan.index') }}"
                                    class="d-flex align-items-center text-decoration-none text-muted">
                                    <div
                                        class="me-3 round-40 bg-light-success rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti ti-trophy text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark">Data Perangkingan</h6>
                                        <p class="mb-0 text-muted fs-3">Lihat hasil perangkingan</p>
                                    </div>
                                </a>
                            @elseif($user->role === 'Siswa')
                                <a href="{{ route('penilaian.index') }}"
                                    class="d-flex align-items-center text-decoration-none text-muted">
                                    <div
                                        class="me-3 round-40 bg-light-info rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti ti-star text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark">Data Penilaian</h6>
                                        <p class="mb-0 text-muted fs-3">Lihat penilaian Anda</p>
                                    </div>
                                </a>
                                <a href="{{ route('perangkingan.index') }}"
                                    class="d-flex align-items-center text-decoration-none text-muted">
                                    <div
                                        class="me-3 round-40 bg-light-success rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti ti-trophy text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark">Data Perangkingan</h6>
                                        <p class="mb-0 text-muted fs-3">Lihat peringkat Anda</p>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="card-title fw-semibold mb-0">Informasi Profile</h5>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProfileModal">
                                <i class="ti ti-edit me-1"></i>Edit
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-user text-primary me-2"></i>
                                        <small class="text-muted">Nama Lengkap</small>
                                    </div>
                                    <h6 class="mb-0">{{ $user->nama_user }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-at text-info me-2"></i>
                                        <small class="text-muted">Username</small>
                                    </div>
                                    <h6 class="mb-0">{{ '@' . $user->username }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-id text-success me-2"></i>
                                        <small class="text-muted">User ID</small>
                                    </div>
                                    <h6 class="mb-0">#{{ str_pad($user->id_user, 4, '0', STR_PAD_LEFT) }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-shield-check text-warning me-2"></i>
                                        <small class="text-muted">Status Akun</small>
                                    </div>
                                    <span class="badge bg-success rounded-3 fw-semibold">Online</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-gender-flammables text-primary me-2"></i> {{-- Use ti icon instead
                                        of fa-venus-mars if not available, or ti-gender-male/female --}}
                                        <small class="text-muted">Jenis Kelamin</small>
                                    </div>
                                    <h6 class="mb-0">{{ $user->jenis_kelamin ?? '-' }}</h6>
                                </div>
                            </div>
                            @if($user->role == 'Siswa')
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded-2 p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ti ti-id-badge text-info me-2"></i>
                                            <small class="text-muted">NIS</small>
                                        </div>
                                        <h6 class="mb-0">{{ $user->nis ?? '-' }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Security Section -->
                        <div class="mt-4">
                            <h6 class="fw-semibold mb-3">Keamanan Akun</h6>
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-2">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-lock text-danger me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Password</h6>
                                        <small class="text-muted">********</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">
                                    Ubah Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_user" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_user" name="nama_user"
                                value="{{ $user->nama_user }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                            <small class="text-muted">Username tidak dapat diubah</small>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ $user->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ $user->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        @if($user->role == 'Siswa')
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nis" name="nis" value="{{ $user->nis }}">
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="password_lama" class="form-label">Password Lama</label>
                            <input type="password" class="form-control" id="password_lama" name="password_lama" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                            <div class="form-text">Minimal 6 karakter</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_baru_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_baru_confirmation"
                                name="password_baru_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Design and Developed by RAUF</p>
    </div>
@endsection