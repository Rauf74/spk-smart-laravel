<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between pt-3">
            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img d-flex align-items-center gap-2">
                <img src="{{ asset('assets/images/smk3.png') }}" width="50" alt="" />
                <div>
                    <span class="d-block fs-4 fw-bold">SMK Muhammadiyah 3</span>
                    <span class="d-block fs-4 fw-bold">Tangerang Selatan</span>
                </div>
            </a>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" aria-expanded="false">
                        <span><i class="ti ti-home-2"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                        href="{{ route('profile') }}" aria-expanded="false">
                        <span><i class="ti ti-user"></i></span>
                        <span class="hide-menu">Data Profile</span>
                    </a>
                </li>

                @if(Auth::check() && Auth::user()->role === 'Guru BK')
                    @include('layouts.partials.sidebar-guru-bk')
                @elseif(Auth::check() && Auth::user()->role === 'Siswa')
                    @include('layouts.partials.sidebar-siswa')
                @endif
            </ul>
        </nav>
    </div>
</aside>