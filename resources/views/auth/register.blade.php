<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi - SPK Rekomendasi Program Studi</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/smk3.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="position-fixed top-0 start-0 w-100 h-100"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: -2;"></div>

    <div class="page-wrapper" id="main-wrapper">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center min-vh-100 p-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 p-4">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/images/smk3.png') }}" alt="Logo" width="60" class="mb-3">
                                <h3 class="fw-bold">Registrasi Akun Baru</h3>
                                <p class="text-muted">SMK Muhammadiyah 3 Tangerang Selatan</p>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="nama_user" class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_user" name="nama_user"
                                        value="{{ old('nama_user') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username') }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label fw-semibold">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi
                                            Password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin</label>
                                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nis" class="form-label fw-semibold">NIS (Opsional)</label>
                                        <input type="text" class="form-control" id="nis" name="nis"
                                            value="{{ old('nis') }}">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                                    </button>
                                </div>

                                <div class="text-center">
                                    <span class="text-muted">Sudah punya akun?</span>
                                    <a href="{{ route('login') }}" class="text-primary fw-semibold">Login di sini</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>