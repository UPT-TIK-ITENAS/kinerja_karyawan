<nav>
    <div class="main-navbar">
        <div id="mainnav">
            <ul class="nav-menu custom-scrollbar">
                <li class="back-btn">
                    <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                            aria-hidden="true"></i></div>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6>Penilaian Kinerja </h6>
                    </div>
                </li>
                @if (auth()->user()->role == 'admin')
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav active" href="{{ route('admin.admin_v') }}"><i
                                data-feather="users"></i><span>Dashboard</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title"><i data-feather="book-open"></i><span>Master Data Presensi</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: none;">
                            <li><a href="{{ route('admin.datapresensi') }}" class="">Monitor Kehadiran</a>
                            </li>
                            <li><a href="{{ route('admin.rekapitulasi') }}" class="">Rekapitulasi</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title"><i data-feather="edit-3"></i><span>Pengajuan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: none;">
                            <li><a href="{{ route('admin.dataizin') }}" class="">Izin</a>
                            </li>
                            <li><a href="{{ route('admin.datacuti') }}" class="">Cuti</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav" href="{{ route('admin.liburnasional') }}"><i
                                data-feather="calendar"></i><span>Pendataan Hari Libur </span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                @endif
                @if (auth()->user()->role == 'karyawan')
                <li class="dropdown">
                    <a id="karyawan_index"
                        class="nav-link menu-title link-nav {{ routeActive('karyawan.index') }}"
                        href="{{ route('karyawan.index') }}"><i data-feather="users"></i><span>Dashboard</span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                    </a>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6>Data dan Pengajuan</h6>
                    </div>
                </li>
                {{-- @if (request()->routeIs('karyawan.datapresensi') || request()->routeIs('karyawan.datarekapitulasi'))
                    <h6>Blok</h6>
                @else
                    <h6>none</h6>
                @endif --}}
                <li class="dropdown">
                    <a class="nav-link menu-title @if (request()->routeIs('karyawan.datapresensi') || request()->routeIs('karyawan.datarekapitulasi')) active @endif"><i
                            data-feather="book-open"></i><span>Data
                            Kehadiran</span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                    </a>
                    <ul class="nav-submenu menu-content"
                        style="display: @if (request()->routeIs('karyawan.datapresensi') || request()->routeIs('karyawan.datarekapitulasi')) block
                    @else
                        none @endif;">
                        <li><a id="a_active" href="{{ route('karyawan.datapresensi') }}"
                                class="{{ routeActive('karyawan.datapresensi') }}">Data Presensi</a>
                        </li>
                        <li><a href="{{ route('karyawan.datarekapitulasi') }}"
                                class="{{ routeActive('karyawan.datarekapitulasi') }}">Data
                                Rekapitulasi</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="nav-link menu-title @if (request()->routeIs('karyawan.izin') ||
                        request()->routeIs('karyawan.store_izin') ||
                        request()->routeIs('karyawan.cuti') ||
                        request()->routeIs('karyawan.store_cuti')) active @endif"><i
                            data-feather="edit-3"></i><span>Pengajuan</span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                    </a>
                    <ul class="nav-submenu menu-content"
                        style="display: @if (request()->routeIs('karyawan.izin') ||
                            request()->routeIs('karyawan.store_izin') ||
                            request()->routeIs('karyawan.cuti') ||
                            request()->routeIs('karyawan.store_cuti')) block
                        @else
                            none @endif;">
                        <li><a href="{{ route('karyawan.izin') }}"
                                class="{{ routeActive('karyawan.izin') }}">Izin</a>
                        </li>
                        <li><a href="{{ route('karyawan.cuti') }}"
                                class="{{ routeActive('karyawan.cuti') }}">Cuti</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role == 'kepalaunit')
            <li class="dropdown">
                <a class="nav-link menu-title link-nav active" href="{{ route('kepalaunit') }}"><i
                        data-feather="users"></i><span>Dashboard</span>
                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                </a>

            </li>
            {{-- <li class="dropdown">
                <a class="nav-link menu-title"><i data-feather="book-open"></i><span>Data Presensi</span>
                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                </a>
                <ul class="nav-submenu menu-content" style="display: none;">
                    <li><a href="" class="">Data Presensi</a>
                    </li>
                    <li><a href="" class="">Rekapitulasi</a>
                    </li>
                </ul>
            </li> --}}
            <li class="dropdown">
                <a class="nav-link menu-title"><i data-feather="bookmark"></i><span>Data Pengajuan</span>
                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                </a>
                <ul class="nav-submenu menu-content" style="display: none;">
                    <li><a href="{{ route('kepalaunit.dataizin') }}" class="">Pengajuan Izin</a>
                    </li>
                    <li><a href="{{ route('kepalaunit.datacuti') }}" class="">Pengajuan Cuti</a>
                    </li>
                </ul>
            </li>
            {{-- <li class="dropdown">
                <a class="nav-link menu-title"><i data-feather="edit-3"></i><span>Pengajuan</span>
                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                </a>
                <ul class="nav-submenu menu-content" style="display: none;">
                    <li><a href="{{ route('kepalaunit.dataizin') }}" class="">Izin</a>
                    </li>
                    <li><a href="{{ route('kepalaunit.datacuti') }}" class="">Cuti</a>
                    </li>
                </ul>
            </li> --}}
        @endif

            </ul>
        </div>
    </div>
</nav>
