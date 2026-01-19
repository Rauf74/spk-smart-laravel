@extends('layouts.app')

@section('title', 'Profile - SPK SMART')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Data Profile</h1>
        <p class="fs-6 mb-4">Lihat dan perbarui informasi profil Anda.</p>

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

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        @php
                            $profileImage = asset('assets/images/profile/user-1.jpg');
                            if ($user->jenis_kelamin === 'Laki-laki') {
                                $profileImage = asset('assets/images/profile/user-male.png');
                            } elseif ($user->jenis_kelamin === 'Perempuan') {
                                $profileImage = asset('assets/images/profile/user-female.png');
                            }
                        @endphp
                        <img src="{{ $profileImage }}" alt="Profile" class="rounded-circle mb-3" width="120" height="120">
                        <h4 class="mb-1">{{ $user->nama_user }}</h4>
                        <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
                        <span
                            class="badge bg-{{ $user->role == 'Guru BK' ? 'primary' : 'success' }}">{{ $user->role }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nama_user" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_user" name="nama_user"
                                    value="{{ $user->nama_user }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" value="{{ $user->username }}"
                                    disabled>
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

                            <hr>
                            <h6>Ubah Password</h6>
                            <small class="text-muted d-block mb-3">Kosongkan jika tidak ingin mengubah password</small>

                            <div class="mb-3">
                                <label for="password_lama" class="form-label">Password Lama</label>
                                <input type="password" class="form-control" id="password_lama" name="password_lama">
                            </div>

                            <div class="mb-3">
                                <label for="password_baru" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password_baru" name="password_baru">
                            </div>

                            <div class="mb-3">
                                <label for="password_baru_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_baru_confirmation"
                                    name="password_baru_confirmation">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check"></i> Simpan Perubahan
                            </button>
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