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
                @if(auth()->user()->role == 'admin')
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav" href="{{ route('admin.admin_v') }}"><i
                            data-feather="users"></i><span>Dashboard</span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div></a>
                        
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>
