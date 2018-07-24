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
            <li class="{{ set_active(['admin']) }} nav-item">
                <a href="{{route('admin.dashboard')}}"><i class="fa fa-home"></i> <span class="nav-label">Dashboards</span></a>
            </li>
            <li class="{{ set_active(['admin/users', 'admin/users/*']) }} nav-item">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Tài Khoản</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/thanh-vien']) }}"><a href="{{route('admin.users.index')}}">Danh sách tài khoản</a></li>
                    <li class="{{ set_active(['quan-ly/thanh-vien/them']) }}"><a href="{{route('admin.users.create')}}">Thêm tài khoản</a></li>
                </ul>
            </li>
            <li class="{{ set_active(['admin/categories', 'admin/categories/*']) }} nav-item">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Danh mục</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/thanh-vien']) }}"><a href="{{route('admin.categories.index')}}">Danh sách danh mục</a></li>
                    <li class="{{ set_active(['quan-ly/thanh-vien/them']) }}"><a href="{{route('admin.categories.create')}}">Thêm danh mục</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav>