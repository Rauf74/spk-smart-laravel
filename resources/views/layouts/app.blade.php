<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard - SPK SMART')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/smk3.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
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
    <script src="https://cdn.jsdelivr.net/npm/simplebar@6.2.5/dist/simplebar.min.js"></script>
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

            // Flash Messages via SweetAlert2
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: {!! json_encode(session('success')) !!},
                    timer: 1500,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: {!! json_encode(session('error')) !!},
                    showConfirmButton: true
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

    @stack('scripts')
</body>

</html>