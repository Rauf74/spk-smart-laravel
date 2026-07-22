<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SPK Rekomendasi Program Studi</title>
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
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .auth-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
        .input-group-text {
            background: #fff;
            border-right: none;
            color: #5a6a85;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #5D87FF;
        }
        .btn-toggle-password {
            border-left: none;
            background: #fff;
            color: #5a6a85;
        }
        .btn-toggle-password:hover,
        .btn-toggle-password:focus {
            background: #fff;
            color: #5D87FF;
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
                    <h4 class="fw-bold mb-1">SPK Rekomendasi Program Studi</h4>
                    <p class="text-muted small mb-0">SMK Muhammadiyah 3 Tangerang Selatan</p>
                </div>

                {{-- Quick Demo Login Card --}}
                <div class="card border border-primary border-opacity-25 bg-light mb-4 rounded-3 shadow-none">
                    <div class="card-body p-3 text-center">
                        <p class="text-muted small mb-2" style="font-size: 0.825rem;">
                            Ingin mencoba aplikasi tanpa ribet mendaftar?
                        </p>
                        <button type="button" id="btnQuickLoginDemo" class="btn btn-primary btn-sm w-100 fw-semibold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="ti ti-bolt fs-5 text-warning"></i> Akses Cepat (Demo Quick Login)
                        </button>
                    </div>
                </div>

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center py-2 mb-3">
                        <i class="ti ti-alert-circle me-2"></i>
                        <small>{{ $errors->first() }}</small>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start"><i class="ti ti-user"></i></span>
                            <input type="text" class="form-control rounded-end" id="username" name="username"
                                placeholder="Masukkan username" value="{{ old('username') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start"><i class="ti ti-lock"></i></span>
                            <input type="password" class="form-control border-end-0" id="password" name="password"
                                placeholder="Masukkan password" required>
                            <button class="btn btn-toggle-password rounded-end border border-start-0" type="button"
                                id="togglePassword">
                                <i class="ti ti-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold py-2">
                            <i class="ti ti-login me-2"></i>Masuk
                        </button>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">Belum punya akun?</small>
                        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1">Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <script>
        // Toggle password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                password.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        });

        $(document).ready(function () {
            // Register success confetti
            @if(session('register_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil!',
                    html: '<p class="mb-0">Selamat datang, <strong>{{ session('register_success') }}</strong>!<br>Silakan login dengan akun baru.</p>',
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' },
                    didOpen: () => confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } })
                });
            @endif

            // Handler Quick Login Demo Interactive Popup (Centered SweetAlert2)
            $('#btnQuickLoginDemo').on('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pilih Peran Demo',
                    text: 'Silakan pilih peran akun demo yang ingin dibuat:',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: '<i class="ti ti-user-star me-1"></i> Guru BK',
                    confirmButtonColor: '#5D87FF',
                    denyButtonText: '<i class="ti ti-school me-1"></i> Siswa',
                    denyButtonColor: '#13DEB9',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'rounded-4' }
                }).then((result) => {
                    let chosenRole = null;
                    if (result.isConfirmed) {
                        chosenRole = 'guru';
                    } else if (result.isDenied) {
                        chosenRole = 'siswa';
                    }
                    if (chosenRole) {
                        // Show loading popup
                        Swal.fire({
                            title: '<div class="spinner-border text-primary" role="status"></div>',
                            html: '<p class="mb-0 text-muted">Membuat akun demo acak...</p>',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' }
                        });

                        // AJAX request ke backend
                        $.ajax({
                            url: '{{ route("login.quick-generate") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                role: chosenRole
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '🎉 Akun Demo Berhasil Dibuat!',
                                        html: `
                                            <p class="text-muted small mb-3">Sistem telah otomatis menghasilkan kredensial berikut:</p>
                                            <div class="card bg-light border p-3 mb-3 text-start">
                                                <div class="mb-2"><strong>Nama:</strong> ${response.nama_user}</div>
                                                <div class="mb-2"><strong>Role:</strong> <span class="badge ${response.role === 'Guru BK' ? 'bg-primary' : 'bg-success'}">${response.role}</span></div>
                                                <div class="mb-2"><strong>Username:</strong> <code class="fs-5 text-primary fw-bold">${response.username}</code></div>
                                                <div><strong>Password:</strong> <code class="fs-5 text-danger fw-bold">${response.password}</code></div>
                                            </div>
                                            <p class="text-muted small mb-0">Klik tombol di bawah ini untuk langsung menuju ke Dashboard.</p>
                                        `,
                                        confirmButtonText: '<i class="ti ti-login me-1"></i> Masuk ke Dashboard',
                                        confirmButtonColor: '#5D87FF',
                                        allowOutsideClick: false,
                                        customClass: { popup: 'rounded-4' }
                                    }).then(() => {
                                        window.location.href = response.redirect;
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat membuat akun demo. Silakan coba lagi.',
                                    customClass: { popup: 'rounded-4' }
                                });
                            }
                        });
                    }
                });
            });

            // Loading saat submit form manual
            $('form').on('submit', function (e) {
                if (this.checkValidity()) {
                    e.preventDefault();
                    Swal.fire({
                        title: '<div class="spinner-border text-primary" role="status"></div>',
                        html: '<p class="mb-0 text-muted">Memverifikasi login...</p>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });
                    this.submit();
                }
            });
        });
    </script>
</body>

</html>
