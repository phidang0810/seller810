<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header hidden">
                <div class="dropdown profile-element">
                    <span>
                        <img alt="image" class="img-circle" src="{{asset('themes/inspinia/img/profile_small.jpg')}}" />
                     </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="plu#">
                        <span class="clear"> <span class="block m-t-xs">
                            <strong class="font-bold">{{Auth::user()->full_name}}</strong>
                         </span>
                            <span class="text-muted text-xs block">{{Auth::user()->role()->first()->name}} </span> </span>
                    </a>
                </div>
                <div class="logo-element">SELLER</div>
            </li>
            <li class="{{ set_active(['quan-ly']) }} nav-item">
                <a href="{{route('admin.dashboard')}}"><i class="fa fa-home"></i> <span class="nav-label">Dashboards</span></a>
            </li>
            @if(key_exists('user_manager', Auth::user()->permissions))
            <li class="{{ set_active(['quan-ly/thanh-vien*']) }} nav-item">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Quản Lý Tài Khoản</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/thanh-vien']) }}"><a href="{{route('admin.users.index')}}">Danh sách nhân viên</a></li>
                </ul>
            </li>
            @endif
            <li class="{{ set_active(['quan-ly/danh-muc-san-pham*']) }} nav-item">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Danh mục</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/danh-muc-san-pham']) }}"><a href="{{route('admin.categories.index')}}">Danh sách danh mục</a></li>
                    <li class="{{ set_active(['quan-ly/danh-muc-san-pham/them']) }}"><a href="{{route('admin.categories.create')}}">Thêm danh mục</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav>