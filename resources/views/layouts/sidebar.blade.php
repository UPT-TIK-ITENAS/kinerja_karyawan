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
                        <a class="nav-link menu-title link-nav @if (request()->routeIs('admin.admin_v')) active @endif"
                            href="{{ route('admin.admin_v') }}"><i data-feather="home"></i><span>Dashboard</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('admin.presensi.master') ||
                            request()->routeIs('admin.rekapitulasi.rekap') ||
                            request()->routeIs('admin.jadwal-satpam.index')) active @endif"><i
                                data-feather="monitor"></i><span>Master Data Presensi</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: @if (request()->routeIs('admin.presensi.master') || request()->routeIs('admin.rekapitulasi.rekap') || request()->routeIs('admin.jadwal-satpam.index')) block
                    @else
                        none @endif;">
                            <li><a href="{{ route('admin.presensi.master') }}" class="">Monitor Kehadiran</a>
                            </li>
                            <li><a href="{{ route('admin.rekapitulasi.rekap') }}" class="">Rekapitulasi</a>
                            <li><a href="{{ route('admin.jadwal-satpam.index') }}" class="">Jadwal Satpam</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav @if (request()->routeIs('admin.mesin-sidikjari.mesin')) active @endif"
                            href="{{ route('admin.mesin-sidikjari.mesin') }}"><i data-feather="tablet"></i><span>Mesin
                                Sidik
                                Jari</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav @if (request()->routeIs('admin.karyawan.list')) active @endif"
                            href="{{ route('admin.karyawan.list') }}"><i data-feather="users"></i><span>Data
                                Karyawan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('admin.kuesioner.pertanyaanPeriode') ||
                            request()->routeIs('admin.kuesioner.admHasilKuesioner') ||
                            request()->routeIs('admin.kuesioner.pertanyaan')) active @endif "><i
                                data-feather="file"></i><span>Kuesioner</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display:  @if (request()->routeIs('admin.indexKuesioner') || request()->routeIs('admin.kuesioner.pertanyaan') || request()->routeIs('admin.kuesioner.pertanyaanPeriode')) block
                    @else
                        none @endif;">
                            {{-- <li><a href="{{ route('admin.indexKuesioner') }}" class="">Penilaian</a></li> --}}
                            <li><a href="{{ route('admin.kuesioner.pertanyaan') }}" class="">Pertanyaan</a></li>
                            <li><a href="{{ route('admin.kuesioner.pertanyaanPeriode') }}" class="">Daftar
                                    Periode</a>
                            </li>
                            <li><a href="{{ route('admin.kuesioner.admHasilKuesioner') }}" class="">Hasil
                                    Kuesioner</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('admin.izin-resmi.dataizin') || request()->routeIs('admin.cuti.datacuti')) active @endif"><i
                                data-feather="edit-3"></i><span>Pengajuan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: @if (request()->routeIs('admin.izin-resmi.dataizin') || request()->routeIs('admin.cuti.datacuti')) block
                    @else
                        none @endif;">
                            <li><a href="{{ route('admin.izin-resmi.dataizin') }}" class="">Izin</a>
                            </li>
                            <li><a href="{{ route('admin.cuti.datacuti') }}" class="">Cuti</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav @if (request()->routeIs('admin.libur-nasional.libur')) active @endif"
                            href="{{ route('admin.libur-nasional.libur') }}"><i
                                data-feather="calendar"></i><span>Pendataan
                                Hari Libur </span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                @endif
                @if (auth()->user()->role == 'admin_bsdm')
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav @if (request()->routeIs('admin_bsdm.admin_v')) active @endif"
                            href="{{ route('admin_bsdm.admin_v') }}"><i data-feather="home"></i><span>Dashboard</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>

                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('admin_bsdm.presensi.master') || request()->routeIs('admin_bsdm.rekapitulasi.rekap')) active @endif"><i
                                data-feather="monitor"></i><span>Master Data Presensi</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: @if (request()->routeIs('admin_bsdm.presensi.master') || request()->routeIs('admin_bsdm.rekapitulasi.rekap')) block
                    @else
                        none @endif;">
                            <li><a href="{{ route('admin_bsdm.presensi.master') }}" class="">Monitor
                                    Kehadiran</a>
                            </li>
                            <li><a href="{{ route('admin_bsdm.rekapitulasi.rekap') }}" class="">Rekapitulasi</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('admin_bsdm.izin-resmi.dataizin') || request()->routeIs('admin_bsdm.cuti.datacuti')) active @endif"><i
                                data-feather="edit-3"></i><span>Pengajuan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: @if (request()->routeIs('admin_bsdm.izin-resmi.dataizin') || request()->routeIs('admin_bsdm.cuti.datacuti')) block
                    @else
                        none @endif;">
                            <li><a href="{{ route('admin_bsdm.izin-resmi.dataizin') }}" class="">Izin</a>
                            </li>
                            <li><a href="{{ route('admin_bsdm.cuti.datacuti') }}" class="">Cuti</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav @if (request()->routeIs('admin_bsdm.libur-nasional.libur')) active @endif"
                            href="{{ route('admin_bsdm.libur-nasional.libur') }}"><i
                                data-feather="calendar"></i><span>Pendataan
                                Hari Libur </span>
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
                        <a id="karyawan_index"
                            class="nav-link menu-title link-nav {{ routeActive('kepalaunit.kepalaunit') }}"
                            href="{{ route('kepalaunit.kepalaunit') }}"><i
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
                        <a class="nav-link menu-title @if (request()->routeIs('kepalaunit.datapresensi') || request()->routeIs('kepalaunit.datarekapitulasi')) active @endif"><i
                                data-feather="book-open"></i><span>Data
                                Kehadiran</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content"
                            style="display: @if (request()->routeIs('kepalaunit.datapresensi') || request()->routeIs('kepalaunit.datarekapitulasi')) active @endif;">
                            <li><a id="a_active" href="{{ route('kepalaunit.datapresensi') }}"
                                    class="{{ routeActive('kepalaunit.datapresensi') }}">Data Presensi</a>
                            </li>
                            <li><a href="{{ route('kepalaunit.datarekapitulasi') }}"
                                    class="{{ routeActive('kepalaunit.datarekapitulasi') }}">Data
                                    Rekapitulasi</a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('kepalaunit.izin') ||
                            request()->routeIs('kepalaunit.store_izin') ||
                            request()->routeIs('kepalaunit.cuti') ||
                            request()->routeIs('kepalaunit.store_cuti')) active @endif"><i
                                data-feather="edit-3"></i><span>Pengajuan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content"
                            style="display: @if (request()->routeIs('kepalaunit.izin'))  @endif;">
                            <li><a href="{{ route('kepalaunit.approval') }}"
                                    class="{{ routeActive('kepalaunit.approval') }}">Approval Cuti</a>
                            </li>
                            <li><a href="{{ route('kepalaunit.approvalIzin') }}"
                                    class="{{ routeActive('kepalaunit.approvalIzin') }}">Approval Izin</a>
                            </li>
                            <li><a href="{{ route('kepalaunit.approvalIzinTelat') }}"
                                    class="{{ routeActive('kepalaunit.approvalIzinTelat') }}">Approval Izin
                                    Perhari</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('kepalaunit.pertanyaanPeriode')) active @endif "><i
                                data-feather="file"></i><span>Kuesioner</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content" style="display: none;">
                            <li><a href="{{ route('kepalaunit.indexKuesioner') }}" class="">Penilaian</a>
                            </li>
                            <li><a href="{{ route('kepalaunit.pertanyaanPeriode') }}" class="">Daftar
                                    Pertanyaan</a>
                            </li>
                            <li><a href="{{ route('kepalaunit.hasilKuesioner') }}" class="">Hasil Kuesioner</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->role == 'pejabat')
                    <li class="dropdown">
                        <a id="pejabat_index" class="nav-link menu-title link-nav {{ routeActive('pejabat.index') }}"
                            href="{{ route('pejabat.index') }}"><i data-feather="users"></i><span>Dashboard</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Data dan Pengajuan</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('pejabat.datapresensi') || request()->routeIs('pejabat.datarekapitulasi')) active @endif"><i
                                data-feather="book-open"></i><span>Data
                                Kehadiran</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content"
                            style="display: @if (request()->routeIs('pejabat.datapresensi') || request()->routeIs('pejabat.datarekapitulasi')) block @else none @endif;">
                            <li><a id="a_active" href="{{ route('pejabat.datapresensi') }}"
                                    class="{{ routeActive('pejabat.datapresensi') }}">Data Presensi</a>
                            </li>
                            <li><a href="{{ route('pejabat.datarekapitulasi') }}"
                                    class="{{ routeActive('pejabat.datarekapitulasi') }}">Data
                                    Rekapitulasi</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title @if (request()->routeIs('pejabat.izin') ||
                            request()->routeIs('pejabat.store_izin') ||
                            request()->routeIs('pejabat.cuti') ||
                            request()->routeIs('pejabat.store_cuti')) active @endif"><i
                                data-feather="edit-3"></i><span>Pengajuan</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                        <ul class="nav-submenu menu-content"
                            style="display: @if (request()->routeIs('pejabat.izin') ||
                                request()->routeIs('pejabat.store_izin') ||
                                request()->routeIs('pejabat.cuti') ||
                                request()->routeIs('pejabat.store_cuti')) block @else none @endif;">
                            <li><a href="{{ route('pejabat.izin') }}"
                                    class="{{ routeActive('pejabat.izin') }}">Izin</a>
                            </li>
                            <li><a href="{{ route('pejabat.cuti') }}"
                                    class="{{ routeActive('pejabat.cuti') }}">Cuti</a>
                            </li>
                            <li><a href="{{ route('pejabat.approval') }}"
                                    class="{{ routeActive('pejabat.approval') }}">Approval</a>
                            </li>
                        </ul>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>
