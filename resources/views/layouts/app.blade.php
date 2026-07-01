<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - SPK SMART')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/smk3.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <style>
        /* Skeleton/Loading Shimmer */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .skeleton-line { height: 12px; margin: 8px 0; }
        .skeleton-line-sm { height: 8px; margin: 4px 0; }
        .skeleton-line-lg { height: 20px; margin: 8px 0; }
        .skeleton-block { min-height: 100px; }
        .skeleton-circle { width: 40px; height: 40px; border-radius: 50%; display: inline-block; }
        /* Empty state illustration */
        .empty-state-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            color: #5D87FF;
            font-size: 36px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.36.0/tabler-icons.min.css">
    <style>
        /* Make all DataTables headers center aligned */
        table.dataTable thead th,
        table.dataTable thead td {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        @include('layouts.sidebar')
        <!--  Sidebar End -->

        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('layouts.header')
            <!--  Header End -->

            <div class="container-fluid">


                @yield('content')
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page Loader (shown during navigation) -->
    <div id="pageLoader" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.85);z-index:9999;display:none;align-items:center;justify-content:center;backdrop-filter:blur(2px);">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="text-muted small">Memuat...</div>
        </div>
    </div>

    <script>
        // Tampilkan page loader saat navigasi (klik link internal) atau saat halaman unload
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="/"], a[href^="./"], a[href^="../"]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Skip jika external, target=_blank, hash, atau javascript:
                    var href = this.getAttribute('href');
                    if (!href || href.startsWith('#') || href.startsWith('javascript:') ||
                        this.target === '_blank' || this.hasAttribute('data-no-loader') ||
                        href.includes('export') || href.includes('pdf')) {
                        return;
                    }
                    // Tampilkan loader setelah delay kecil biar tidak flicker
                    setTimeout(function() {
                        document.getElementById('pageLoader').style.display = 'flex';
                    }, 100);
                });
            });

            // Hide loader saat halaman selesai load
            window.addEventListener('pageshow', function() {
                document.getElementById('pageLoader').style.display = 'none';
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Skip jika user sedang mengetik di input/textarea (kecuali Escape)
            var tag = (e.target.tagName || '').toLowerCase();
            var isTyping = ['input', 'textarea', 'select'].includes(tag) && e.key !== 'Escape';

            // Escape: tutup modal, drawer, atau focus ke body
            if (e.key === 'Escape') {
                // Bootstrap modal
                var openModal = document.querySelector('.modal.show');
                if (openModal && window.bootstrap) {
                    var modal = window.bootstrap.Modal.getInstance(openModal);
                    if (modal) modal.hide();
                    return;
                }
                // SweetAlert
                if (window.Swal && Swal.isVisible()) {
                    Swal.close();
                    return;
                }
            }

            if (isTyping) return;

            // Ctrl/Cmd + P: langsung print (skip browser default dialog kalau ada target)
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                // Biarkan browser default handle, kecuali ada class .auto-print di body
                if (document.body.classList.contains('auto-print')) {
                    e.preventDefault();
                    window.print();
                }
            }

            // Ctrl/Cmd + S: simpan wizard (form id #formKuesioner)
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                var form = document.getElementById('formKuesioner');
                if (form) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }

            // Ctrl/Cmd + K: fokus ke search input (kalau ada)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                var search = document.querySelector('input[name="q"], input[type="search"]');
                if (search) {
                    e.preventDefault();
                    search.focus();
                    search.select();
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('login_success'))
            Swal.fire({
                icon: 'success',
                title: '<i class="bi bi-check-circle-fill text-success animate-check"></i><br>Login Berhasil!',
                html: `
                        <div class="alert alert-success mt-3 text-center">
                            Selamat datang kembali, <strong>{{ Auth::user()->nama_user }}</strong>!
                        </div>
                        <div class="bg-light rounded-3 p-3 mt-3 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                <span class="fw-semibold">Role: {{ Auth::user()->role }}</span>
                            </div>
                        </div>
                    `,
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-4'
                },
                didOpen: function () {
                    // Animasi ceklis (Legacy Style)
                    const checkIcon = document.querySelector('.animate-check');
                    if (checkIcon) {
                        checkIcon.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                        checkIcon.style.transform = 'scale(1.2)';
                        checkIcon.style.opacity = '0.8';
                        setTimeout(() => {
                            checkIcon.style.transform = 'scale(1)';
                            checkIcon.style.opacity = '1';
                        }, 500);
                    }
                }
            });
        @endif
    </script>
    <!-- Custom Sidebar Logic -->
    <script>
        $(function () {
            // Sidebar Toggler
            $("#sidebarToggler").on("click", function () {
                $("#main-wrapper").toggleClass("show-sidebar");
            });

            $(".sidebartoggler").on("click", function () {
                $("#main-wrapper").toggleClass("mini-sidebar");
                if (window.innerWidth < 1199) {
                    $("#main-wrapper").toggleClass("show-sidebar");
                }
            });

            // Close Sidebar when clicking outside (Mobile)
            $(document).on('click', function (e) {
                var sidebar = $("aside.left-sidebar");
                var toggler = $(".sidebartoggler");

                // Cek jika sidebar sedang terbuka
                if ($("#main-wrapper").hasClass("show-sidebar")) {
                    // Jika yang diklik BUKAN sidebar DAN BUKAN tombol toggle
                    if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 &&
                        !toggler.is(e.target) && toggler.has(e.target).length === 0) {

                        $("#main-wrapper").removeClass("show-sidebar");
                    }
                }
            });

            // Flash Messages via SweetAlert2 Toast (kecil di pojok, auto-hide)
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ session('error') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: '{{ session('warning') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: '{{ session('info') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            // Global Delete Confirmation
            $('body').on('submit', 'form', function (e) {
                if ($(this).find('input[name="_method"][value="DELETE"]').length > 0) {
                    var confirmAttr = $(this).attr('onsubmit');
                    // Ignore if it already has inline confirm handling (legacy) or we want to override
                    // Ideally we should remove inline onsubmit from view files
                    e.preventDefault();
                    var form = this;
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>

    <script>
        // Global confirmDelete() — panggil dari tombol dengan data attributes
        // Contoh 1 (simple): onclick="confirmDelete('Hapus User?', 'User Andi akan dihapus', '/user/5')"
        // Contoh 2 (extra fields): onclick="confirmDelete('Hapus?', '...', '/penilaian', [{name:'id_user', value:5}, {name:'id_alternatif', value:3}])"
        function confirmDelete(title, preview, action, extraFields = []) {
            Swal.fire({
                title: title,
                html: preview,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA896B',
                cancelButtonColor: '#5A6A85',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form dinamis untuk DELETE request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action;
                    form.style.display = 'none';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]')?.content
                              || document.querySelector('input[name="_token"]')?.value;
                    form.appendChild(csrf);

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(method);

                    // Tambahkan field ekstra jika ada
                    extraFields.forEach(field => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = field.name;
                        input.value = field.value;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>

</html>