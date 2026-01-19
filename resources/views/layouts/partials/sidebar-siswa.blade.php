{{-- Sidebar menu untuk Siswa --}}
<li class="nav-small-cap">
    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
    <span class="hide-menu">Input Data</span>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}"
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
    <a class="sidebar-link {{ request()->routeIs('perhitungan.*') ? 'active' : '' }}"
        href="{{ route('perhitungan.index') }}" aria-expanded="false">
        <span><i class="ti ti-calculator"></i></span>
        <span class="hide-menu">Data Perhitungan Saya</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('perangkingan.*') ? 'active' : '' }}"
        href="{{ route('perangkingan.index') }}" aria-expanded="false">
        <span><i class="ti ti-trophy"></i></span>
        <span class="hide-menu">Hasil Perangkingan Saya</span>
    </a>
</li>