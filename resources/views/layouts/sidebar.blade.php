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
                        <a class="nav-link menu-title link-nav" href="{{ route('admin.admin_v') }}"><i
                                data-feather="users"></i><span>Dashboard</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title"><i data-feather="book-open"></i><span>Master Data Presensi</span>
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
                            <li><a href="{{ route('admin.dataizin') }}" class="">Pengajuan Izin</a>
                            </li>
                            <li><a href="{{ route('admin.datacuti') }}" class="">Pengajuan Cuti</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->role == 'karyawan')
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav" href="{{ route('karyawan.index') }}"><i
                                data-feather="users"></i><span>Dashboard</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Data dan Pengajuan</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title"><i data-feather="book-open"></i><span>Data
                                Kehadiran</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: none;">
                            <li><a href="{{ route('karyawan.datapresensi') }}" class="">Data Presensi</a>
                            </li>
                            <li><a href="{{ route('karyawan.datarekapitulasi') }}" class="">Data Rekapitulasi</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title"><i data-feather="edit-3"></i><span>Pengajuan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: none;">
                            <li><a href="{{ route('karyawan.izin') }}" class="">Izin</a>
                            </li>
                            <li><a href="{{ route('karyawan.cuti') }}" class="">Cuti</a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
