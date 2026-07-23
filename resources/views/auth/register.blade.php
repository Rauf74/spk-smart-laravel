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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-split">

        {{-- LEFT: Hero Panel --}}
        <div class="auth-hero">
            <img src="{{ asset('assets/images/backgrounds/auth-hero.jpg') }}" alt="Siswa SMK Muhammadiyah 3" class="auth-hero-img">
            <div class="auth-hero-overlay"></div>
            <div class="auth-hero-content">
                <div class="auth-hero-brand">
                    <img src="{{ asset('assets/images/smk3.png') }}" alt="Logo SMK">
                    <span>SMK Muhammadiyah 3<br>Tangerang Selatan</span>
                </div>
                <div class="auth-hero-footer">
                    <h2>Bergabung dengan Sistem Rekomendasi Program Studi</h2>
                    <p>Daftarkan akunmu dan temukan rekomendasi program studi yang paling sesuai dengan minat dan potensimu.</p>
                </div>
            </div>
        </div>

        {{-- RIGHT: Form Panel --}}
        <div class="auth-panel">
            <div class="auth-form-inner">
                {{-- Logo & Header --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/smk3.png') }}" alt="Logo" class="auth-logo mb-3">
                    <h4 class="auth-title">Daftar Akun Baru</h4>
                    <p class="auth-subtitle">SMK Muhammadiyah 3 Tangerang Selatan</p>
                </div>

                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius: 10px; font-size: 0.84rem;">
                        <i class="ti ti-alert-circle me-2"></i>
                        <small>{{ $errors->first() }}</small>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="nama_user" class="form-label fw-semibold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-user"></i></span>
                            <input type="text" class="form-control" id="nama_user" name="nama_user"
                                value="{{ old('nama_user') }}" placeholder="Nama lengkap kamu" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-at"></i></span>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ old('username') }}" placeholder="Buat username" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-lock-check"></i></span>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Ulangi password" required>
                            </div>
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
                            <label for="nis" class="form-label fw-semibold">NIS <small class="text-muted">(Opsional)</small></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-id"></i></span>
                                <input type="text" class="form-control" id="nis" name="nis"
                                    value="{{ old('nis') }}" placeholder="Nomor Induk Siswa">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn-auth-submit">
                            <i class="ti ti-user-plus"></i> Daftar
                        </button>
                    </div>

                    <div class="text-center auth-footer-link">
                        <span>Sudah punya akun?</span>
                        <a href="{{ route('login') }}">Login</a>
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
