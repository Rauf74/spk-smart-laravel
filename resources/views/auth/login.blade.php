<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SPK Rekomendasi Program Studi</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/smk3.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.36.0/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <x-alert type="danger" :message="$errors->first()" />
                @endif

                {{-- Form Login --}}
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
        @if(session('register_success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil!',
                    html: '<p class="mb-0">Selamat datang, <strong>{{ session('register_success') }}</strong>!<br>Silakan login dengan akun baru.</p>',
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' },
                    didOpen: () => confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } })
                });
            });
        @endif
    </script>
</body>

</html>
