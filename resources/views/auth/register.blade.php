<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi - SPK Rekomendasi Program Studi</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/smk3.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.36.0/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f9fc;
            padding: 20px;
        }
        .auth-card {
            width: 100%;
            max-width: 480px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .auth-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card card">
            <div class="card-body p-4 p-md-5">
                {{-- Logo & Header --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/smk3.png') }}" alt="Logo" class="auth-logo mb-3">
                    <h4 class="fw-bold mb-1">Daftar Akun Baru</h4>
                    <p class="text-muted small mb-0">SMK Muhammadiyah 3 Tangerang Selatan</p>
                </div>

                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center py-2 mb-3">
                        <i class="ti ti-alert-circle me-2"></i>
                        <small>{{ $errors->first() }}</small>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="nama_user" class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_user" name="nama_user"
                            value="{{ old('nama_user') }}" placeholder="Nama lengkap kamu" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="{{ old('username') }}" placeholder="Buat username" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Ulangi password" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">Pilih</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="nis" class="form-label fw-semibold">NIS <small class="text-muted">(Opsional)</small></label>
                            <input type="text" class="form-control" id="nis" name="nis"
                                value="{{ old('nis') }}" placeholder="Nomor Induk Siswa">
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold py-2">
                            <i class="ti ti-user-plus me-2"></i>Daftar
                        </button>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">Sudah punya akun?</small>
                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('form').on('submit', function (e) {
                var form = this;

                if (form.checkValidity()) {
                    e.preventDefault();

                    var nama = $('#nama_user').val();
                    var user = $('#username').val();
                    var jk = $('#jenis_kelamin').val();
                    var nis = $('#nis').val() || '-';

                    Swal.fire({
                        title: 'Konfirmasi Pendaftaran',
                        html: `
                            <div class="text-start small">
                                <p class="mb-1"><strong>Nama:</strong> ${nama}</p>
                                <p class="mb-1"><strong>Username:</strong> ${user}</p>
                                <p class="mb-1"><strong>Jenis Kelamin:</strong> ${jk}</p>
                                <p class="mb-0"><strong>NIS:</strong> ${nis}</p>
                            </div>
                            <p class="text-muted small mt-3 mb-0">Pastikan data sudah benar.</p>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Daftar!',
                        cancelButtonText: 'Periksa Kembali',
                        confirmButtonColor: '#5D87FF',
                        cancelButtonColor: '#6c757d',
                        customClass: { popup: 'rounded-4' }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: '<div class="spinner-border text-primary" role="status"></div>',
                                html: '<p class="mb-0 text-muted">Memproses pendaftaran...</p>',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-4' }
                            });
                            form.submit();
                        }
                    });
                } else {
                    form.reportValidity();
                }
            });
        });
    </script>
</body>

</html>
