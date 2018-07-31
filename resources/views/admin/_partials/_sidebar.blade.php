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

            @if(key_exists('partner_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/cong-tac-vien*']) }} nav-item">
                    <a href="{{route('admin.partners.index')}}"><i class="fa fa-users" aria-hidden="true"></i> <span class="nav-label">Cộng Tác Viên</span></a>
                </li>
            @endif
            
            @if(key_exists('supplier_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/nha-cung-cap*']) }} nav-item">
                    <a href="{{route('admin.suppliers.index')}}"><i class="fa fa-users" aria-hidden="true"></i> <span class="nav-label">Nhà Cung Cấp</span></a>
                </li>
            @endif

            @if(key_exists('customer_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/khach-hang*']) }} nav-item">
                    <a href="#"><i class="fa fa-users" aria-hidden="true"></i> <span class="nav-label">Quản Lý Khách Hàng</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/khach-hang/nhom*']) }}"><a href="{{route('admin.groupCustomer.index')}}">Nhóm khách hàng</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('user_manager', Auth::user()->permissions))
            <li class="{{ set_active(['quan-ly/thanh-vien*']) }} nav-item">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Quản Lý Tài Khoản</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/thanh-vien*']) }}"><a href="{{route('admin.users.index')}}">Danh sách nhân viên</a></li>
                </ul>
            </li>
            @endif

            @if(key_exists('product_manager', Auth::user()->permissions))
            <li class="{{ set_active(['quan-ly/danh-muc-san-pham*']) }} nav-item">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Quản Lý Danh mục</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/danh-muc-san-pham']) }}"><a href="{{route('admin.categories.index')}}">Danh sách danh mục</a></li>
                    <li class="{{ set_active(['quan-ly/danh-muc-san-pham/them']) }}"><a href="{{route('admin.categories.create')}}">Thêm danh mục</a></li>
                </ul>
            </li>
            @endif

            @if(key_exists('product_manager', Auth::user()->permissions))
            <li class="{{ set_active(['quan-ly/mau-sac', 'quan-ly/mau-sac/*']) }} nav-item">
                <a href="#"><i class="fa fa-paint-brush"></i> <span class="nav-label">Quản Lý Màu sắc</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/mau-sac']) }}"><a href="{{route('admin.colors.index')}}">Danh sách Màu sắc</a></li>
                    <li class="{{ set_active(['quan-ly/mau-sac/them']) }}"><a href="{{route('admin.colors.create')}}">Thêm màu sắc</a></li>
                </ul>
            </li>
            @endif
            
            @if(key_exists('product_manager', Auth::user()->permissions))
            <li class="{{ set_active(['quan-ly/san-pham', 'quan-ly/san-pham/*']) }} nav-item">
                <a href="#"><i class="fa fa-tasks "></i> <span class="nav-label">Quản Lý Sản Phẩm</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ set_active(['quan-ly/san-pham']) }}"><a href="{{route('admin.products.index')}}">Danh sách Sản Phẩm</a></li>
                    <li class="{{ set_active(['quan-ly/san-pham/them']) }}"><a href="{{route('admin.products.create')}}">Thêm Sản Phẩm</a></li>
                </ul>
            </li>
            @endif

        </ul>

    </div>
</nav>