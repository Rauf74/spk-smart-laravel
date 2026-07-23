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
                    <h2>Sistem Pendukung Keputusan Rekomendasi Program Studi</h2>
                    <p>Membantu siswa menemukan program studi yang tepat berdasarkan minat, bakat, dan potensi akademik melalui metode SMART.</p>
                </div>
            </div>
        </div>

        {{-- RIGHT: Form Panel --}}
        <div class="auth-panel">
            <div class="auth-form-inner">
                {{-- Logo & Header --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/smk3.png') }}" alt="Logo" class="auth-logo mb-3">
                    <h4 class="auth-title">SPK Rekomendasi Program Studi</h4>
                    <p class="auth-subtitle">Selamat Datang!</p>
                </div>

                {{-- Quick Demo Login Button --}}
                <button type="button" id="btnQuickLoginDemo" class="quick-demo-banner">
                    <span class="demo-pulse"></span>
                    <i class="ti ti-bolt fs-5"></i> Akses Cepat (Demo Quick Login)
                </button>

                {{-- Divider --}}
                <div class="auth-divider"><span>atau login dengan akun</span></div>

                {{-- Error Alert --}}
                @if ($errors->any())
                    <x-alert type="danger" :message="$errors->first()" />
                @endif

                {{-- Form Login --}}
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Username" value="{{ old('username') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-lock"></i></span>
                            <input type="password" class="form-control border-end-0" id="password" name="password"
                                placeholder="Password" required>
                            <button class="btn btn-toggle-password" type="button" id="togglePassword">
                                <i class="ti ti-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn-auth-submit">
                            <i class="ti ti-login"></i> Masuk
                        </button>
                    </div>

                    <div class="text-center auth-footer-link">
                        <span>Belum punya akun?</span>
                        <a href="{{ route('register') }}">Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Quick Login Modal --}}
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
                    <p>Kredensial di bawah bisa disalin. Langsung masuk kapan saja.</p>
                </div>

                <div class="ql-copy-all-strip">
                    <span><i class="ti ti-clipboard me-1"></i>Salin semua sekaligus</span>
                    <button class="ql-copy-all-btn" id="qlCopyAll" type="button">Salin Semua</button>
                </div>

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
