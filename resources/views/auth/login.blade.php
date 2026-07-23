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
                        <button type="button" id="btnQuickLoginDemo"
                            class="btn btn-primary btn-sm w-100 fw-semibold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span class="demo-pulse"></span>
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

    {{-- =====================================================
         Quick Login Modal (Custom HTML, no SweetAlert deps)
         ===================================================== --}}
    <div class="ql-overlay" id="qlOverlay" role="dialog" aria-modal="true" aria-labelledby="qlTitle">
        <div class="ql-modal">

            {{-- Step 1: Pilih Peran --}}
            <div class="ql-step active" id="qlStepRole">
                <div class="ql-header">
                    <h5 id="qlTitle"><i class="ti ti-bolt me-1 text-warning"></i> Demo Quick Login</h5>
                    <p>Pilih peran akun demo yang ingin dibuat</p>
                </div>
                <div class="ql-role-grid">
                    <button class="ql-role-btn guru" id="qlBtnGuru" type="button">
                        <div class="ql-role-icon"><i class="ti ti-user-star"></i></div>
                        <div>
                            <div class="ql-role-label">Guru BK</div>
                            <div class="ql-role-desc">Akses penuh manajemen data</div>
                        </div>
                    </button>
                    <button class="ql-role-btn siswa" id="qlBtnSiswa" type="button">
                        <div class="ql-role-icon"><i class="ti ti-school"></i></div>
                        <div>
                            <div class="ql-role-label">Siswa</div>
                            <div class="ql-role-desc">Akses pengisian penilaian diri</div>
                        </div>
                    </button>
                </div>
                <button class="ql-cancel-btn" id="qlBtnCancel" type="button">Batal</button>
            </div>

            {{-- Step 2: Loading --}}
            <div class="ql-step" id="qlStepLoading">
                <div class="ql-loading">
                    <div class="ql-spinner"></div>
                    <h6>Membuat akun demo...</h6>
                    <p>Menyiapkan kredensial acak untukmu</p>
                </div>
            </div>

            {{-- Step 3: Sukses + Kredensial --}}
            <div class="ql-step" id="qlStepSuccess">
                <div class="ql-header">
                    <div class="ql-success-icon"><i class="ti ti-check"></i></div>
                    <h5>Akun Demo Siap!</h5>
                    <p>Kredensial di bawah sudah disalin otomatis. Langsung masuk kapan saja.</p>
                </div>

                {{-- Copy All strip --}}
                <div class="ql-copy-all-strip">
                    <span><i class="ti ti-clipboard me-1"></i>Salin semua sekaligus</span>
                    <button class="ql-copy-all-btn" id="qlCopyAll" type="button">Salin Semua</button>
                </div>

                {{-- Credential card --}}
                <div class="ql-cred-card">
                    <div class="ql-cred-row">
                        <span class="ql-cred-label">Nama</span>
                        <div class="ql-cred-val-wrap">
                            <span class="ql-cred-value" id="qlValNama">—</span>
                        </div>
                    </div>
                    <div class="ql-cred-row">
                        <span class="ql-cred-label">Role</span>
                        <div class="ql-cred-val-wrap">
                            <span class="ql-cred-badge" id="qlValRoleBadge">—</span>
                        </div>
                    </div>
                    <div class="ql-cred-row">
                        <span class="ql-cred-label">Username</span>
                        <div class="ql-cred-val-wrap">
                            <span class="ql-cred-value" id="qlValUsername">—</span>
                            <button class="ql-copy-btn" data-target="qlValUsername" type="button">Salin</button>
                        </div>
                    </div>
                    <div class="ql-cred-row">
                        <span class="ql-cred-label">Password</span>
                        <div class="ql-cred-val-wrap">
                            <span class="ql-cred-value" id="qlValPassword">—</span>
                            <button class="ql-copy-btn" data-target="qlValPassword" type="button">Salin</button>
                        </div>
                    </div>
                </div>

                <p class="ql-note">Akun ini bersifat sementara dan akan dihapus otomatis setelah sesi selesai.</p>

                <button class="btn-ql-enter" id="qlBtnEnter" type="button">
                    <i class="ti ti-layout-dashboard"></i> Masuk ke Dashboard
                </button>
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
