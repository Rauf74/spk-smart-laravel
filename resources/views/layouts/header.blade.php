<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        @php
                            $profileImage = asset('assets/images/profile/user-1.jpg');
                            if (Auth::user()->jenis_kelamin === 'Laki-laki') {
                                $profileImage = asset('assets/images/profile/user-male.png');
                            } elseif (Auth::user()->jenis_kelamin === 'Perempuan') {
                                $profileImage = asset('assets/images/profile/user-female.png');
                            }
                        @endphp
                        <img src="{{ $profileImage }}" alt="" width="35" height="35" class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body text-center">
                            <p class="mb-1 fw-bold">Halo,</p>
                            <p class="mb-2">{{ Auth::user()->nama_user }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-outline-primary mx-3 mt-2 d-block w-75 mx-auto">Logout</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>