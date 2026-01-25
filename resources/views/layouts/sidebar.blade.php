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

                {{-- Menu Guru BK --}}
                @if(Auth::user()->role === 'Guru BK')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Input Data</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('kriteria.index') ? 'active' : '' }}"
                            href="{{ route('kriteria.index') }}" aria-expanded="false">
                            <span><i class="ti ti-list-check"></i></span>
                            <span class="hide-menu">Data Kriteria</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('subkriteria.index') ? 'active' : '' }}"
                            href="{{ route('subkriteria.index') }}" aria-expanded="false">
                            <span><i class="ti ti-list-details"></i></span>
                            <span class="hide-menu">Data Subkriteria</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('alternatif.index') ? 'active' : '' }}"
                            href="{{ route('alternatif.index') }}" aria-expanded="false">
                            <span><i class="ti ti-clipboard-list"></i></span>
                            <span class="hide-menu">Data Alternatif</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('pertanyaan.index') ? 'active' : '' }}"
                            href="{{ route('pertanyaan.index') }}" aria-expanded="false">
                            <span><i class="ti ti-help"></i></span>
                            <span class="hide-menu">Data Pertanyaan</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('penilaian.index') ? 'active' : '' }}"
                            href="{{ route('penilaian.index') }}" aria-expanded="false">
                            <span><i class="ti ti-star"></i></span>
                            <span class="hide-menu">Data Penilaian</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Perhitungan</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('perhitungan.index') ? 'active' : '' }}"
                            href="{{ route('perhitungan.index') }}" aria-expanded="false">
                            <span><i class="ti ti-calculator"></i></span>
                            <span class="hide-menu">Data Perhitungan</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('perangkingan.index') ? 'active' : '' }}"
                            href="{{ route('perangkingan.index') }}" aria-expanded="false">
                            <span><i class="ti ti-trophy"></i></span>
                            <span class="hide-menu">Hasil Perangkingan</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Manajemen User</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('user.index') ? 'active' : '' }}"
                            href="{{ route('user.index') }}" aria-expanded="false">
                            <span><i class="ti ti-users"></i></span>
                            <span class="hide-menu">Data User</span>
                        </a>
                    </li>

                    {{-- Menu Siswa --}}
                @elseif(Auth::user()->role === 'Siswa')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Input Data</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('penilaian.index') ? 'active' : '' }}"
                            href="{{ route('penilaian.index') }}" aria-expanded="false">
                            <span><i class="ti ti-star"></i></span>
                            <span class="hide-menu">Data Penilaian Saya</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Perhitungan</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('perhitungan.index') ? 'active' : '' }}"
                            href="{{ route('perhitungan.index') }}" aria-expanded="false">
                            <span><i class="ti ti-calculator"></i></span>
                            <span class="hide-menu">Data Perhitungan Saya</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('perangkingan.index') ? 'active' : '' }}"
                            href="{{ route('perangkingan.index') }}" aria-expanded="false">
                            <span><i class="ti ti-trophy"></i></span>
                            <span class="hide-menu">Hasil Perangkingan Saya</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>