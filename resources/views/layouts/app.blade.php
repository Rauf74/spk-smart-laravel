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

    <!-- Custom Sidebar Logic -->
    <script>
        $(function () {
            // Sidebar Toggler
            $("#sidebarToggler").on("click", function () {
                $("#main-wrapper").toggleClass("show-sidebar");
            });

            $(".sidebartoggler").on("click", function () {
                $("#main-wrapper").toggleClass("mini-sidebar");
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