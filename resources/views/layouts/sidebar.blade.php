<header class="main-nav">
    <div class="sidebar-user text-center">
        <a class="setting-primary" href="javascript:void(0)"><i data-feather="settings"></i></a><img
            class="img-90 rounded-circle" src="{{ asset('assets/images/dashboard/1.png') }}" alt="" />
        <a href="user-profile">
            <h6 class="mt-3 f-14 f-w-600">{{ auth()->user()->nopeg }}</h6>
        </a>
        <p class="mb-0 font-roboto">{{ auth()->user()->name }}</p>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
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
                            <a class="nav-link menu-title link-nav" href="{{ route('admin.admin_v') }}"><i
                                    data-feather="users"></i><span>Dashboard</span>
                                <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                            </a>

                        </li>
                        <li class="dropdown">
                            <a class="nav-link menu-title"><i data-feather="book-open"></i><span>Master Data
                                    Presensi</span>
                                <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content" style="display: none;">
                                <li><a href="{{ route('admin.datapresensi') }}" class="">Data Presensi</a>
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
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
