{{-- Sidebar menu untuk Guru BK --}}
<li class="nav-small-cap">
    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
    <span class="hide-menu">Input Data</span>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="{{ route('kriteria.index') }}"
        aria-expanded="false">
        <span><i class="ti ti-list-check"></i></span>
        <span class="hide-menu">Data Kriteria</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('subkriteria.*') ? 'active' : '' }}"
        href="{{ route('subkriteria.index') }}" aria-expanded="false">
        <span><i class="ti ti-list-details"></i></span>
        <span class="hide-menu">Data Subkriteria</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('alternatif.*') ? 'active' : '' }}"
        href="{{ route('alternatif.index') }}" aria-expanded="false">
        <span><i class="ti ti-clipboard-list"></i></span>
        <span class="hide-menu">Data Alternatif</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('pertanyaan.*') ? 'active' : '' }}"
        href="{{ route('pertanyaan.index') }}" aria-expanded="false">
        <span><i class="ti ti-help"></i></span>
        <span class="hide-menu">Data Pertanyaan</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}"
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
    <a class="sidebar-link {{ request()->routeIs('perhitungan.*') ? 'active' : '' }}"
        href="{{ route('perhitungan.index') }}" aria-expanded="false">
        <span><i class="ti ti-calculator"></i></span>
        <span class="hide-menu">Data Perhitungan</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link {{ request()->routeIs('perangkingan.*') ? 'active' : '' }}"
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
    <a class="sidebar-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}"
        aria-expanded="false">
        <span><i class="ti ti-users"></i></span>
        <span class="hide-menu">Data User</span>
    </a>
</li>