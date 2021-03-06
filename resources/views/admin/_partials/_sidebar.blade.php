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

            <li class="{{ set_active(['bai-viet', 'bai-viet/*']) }} nav-item">
                <a href="{{route('admin.posts.index')}}"><i class="fa fa-home"></i> <span class="nav-label">Bài Viết</span></a>
            </li>

            <li class="{{ set_active(['anh', 'anh/*']) }} nav-item">
                <a href="{{route('admin.photos.index')}}"><i class="fa fa-home"></i> <span class="nav-label">Hình Ảnh</span></a>
            </li>

            @if(key_exists('shop_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/don-hang', 'quan-ly/don-hang/*']) }} nav-item">
                    <a href="#"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Quản Lý Bán Hàng</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/don-hang']) }}"><a href="{{route('admin.carts.index')}}">Danh sách Đơn Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/don-hang/them']) }}"><a href="{{route('admin.carts.create')}}">Thêm Đơn Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/don-hang/tra-hang']) }}"><a href="{{route('admin.carts.returnIndex')}}">Danh sách Trả Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/don-hang/tra-hang/them']) }}"><a href="{{route('admin.carts.returnCreate')}}">Thêm Trả Hàng</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('product_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/san-pham', 'quan-ly/san-pham/*']) }} nav-item">
                    <a href="#"><i class="fa fa-tasks "></i> <span class="nav-label">Quản Lý Sản Phẩm</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/san-pham']) }}"><a href="{{route('admin.products.index')}}">Danh sách Sản Phẩm</a></li>
                        <li class="{{ set_active(['quan-ly/san-pham/danh-muc-san-pham']) }}"><a href="{{route('admin.categories.index')}}">Danh Mục Sản phẩm</a></li>
                        <li class="{{ set_active(['quan-ly/san-pham/mau-sac']) }}"><a href="{{route('admin.colors.index')}}">Màu sắc</a></li>
                        <li class="{{ set_active(['quan-ly/san-pham/size']) }}"><a href="{{route('admin.size.index')}}">Size</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('warehouse_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/kho-hang', 'quan-ly/kho-hang/*']) }} nav-item">
                    <a href="#"><i class="fa fa-paint-brush"></i> <span class="nav-label">Quản Lý Kho Hàng</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/kho-hang', 'quan-ly/kho-hang/chi-tiet*']) }}"><a href="{{route('admin.warehouses.index')}}">Danh Sách Kho</a></li>
                        <li class="{{ set_active(['quan-ly/kho-hang/nhap-hang', 'quan-ly/kho-hang/nhap-hang/chi-tiet*']) }}"><a href="{{route('admin.import_products.index')}}">Nhập Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/kho-hang/nhap-hang/nhan-hang', 'quan-ly/kho-hang/nhap-hang/nhap-kho*', 'quan-ly/kho-hang/nhap-hang/kiem-hang*']) }}"><a href="{{route('admin.import_products.receive')}}">Nhận Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/kho-hang/chuyen-kho', 'quan-ly/kho-hang/chuyen-kho/nhan-hang*']) }}"><a href="{{route('admin.transport_warehouse.index')}}">Chuyển kho</a></li>
                        <li class="{{ set_active(['quan-ly/kho-hang/tra-hang', 'quan-ly/kho-hang/tra-hang/chi-tiet*']) }}"><a href="{{route('admin.return_products.index')}}">Trả hàng</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('partner_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/cong-tac-vien*']) }} nav-item">
                    <a href="{{route('admin.partners.index')}}"><i class="fa fa-star-half-o" aria-hidden="true"></i><span class="nav-label">Cộng Tác Viên</span></a>
                </li>
            @endif
            


            @if(key_exists('customer_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/khach-hang*']) }} nav-item">
                    <a href="#"><i class="fa fa-users" aria-hidden="true"></i> <span class="nav-label">Khách Hàng</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/khach-hang/nhom*']) }}"><a href="{{route('admin.groupCustomer.index')}}">Nhóm khách hàng</a></li>
                        <li class="{{ set_active(['quan-ly/khach-hang*']) }}"><a href="{{route('admin.customers.index')}}">Khách hàng</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('accountant_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/ke-toan', 'quan-ly/ke-toan/*']) }} nav-item">
                    <a href="#"><i class="fa fa-calculator" aria-hidden="true"></i> <span class="nav-label">Kế Toán</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/ke-toan/chi-phi-nhap-hang']) }}"><a href="{{route('admin.statistics.importProduct')}}">Chi Phí Nhập Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/ke-toan/thong-ke-kho']) }}"><a href="{{route('admin.statistics.productQuantity')}}">Kho Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/ke-toan/doanh-thu']) }}"><a href="{{route('admin.statistics.revenue')}}">Doanh Thu Bán Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/ke-toan/phieu-chi']) }}"><a href="{{route('admin.payslips.index')}}">Phiếu Chi</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('supplier_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/nha-cung-cap*']) }} nav-item">
                    <a href="#"><i class="fa fa-university" aria-hidden="true"></i> <span class="nav-label">Nhà Cung Cấp</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/nha-cung-cap']) }}"><a href="{{route('admin.suppliers.index')}}">Nhà Cung Cấp</a></li>
                        <li class="{{ set_active(['quan-ly/nha-cung-cap/danh-sach-no']) }}"><a href="{{route('admin.creditors.index')}}">Danh sách Nợ</a></li>
                    </ul>
                </li>

                <li class="{{ set_active(['quan-ly/nha-cung-cap*']) }} nav-item">

                </li>
            @endif

            @if(key_exists('product_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/thuong-hieu*']) }} nav-item">
                    <a href="{{route('admin.brands.index')}}"><i class="fa fa-university" aria-hidden="true"></i> <span class="nav-label">Thương Hiệu</span></a>
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

            @if(key_exists('report_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/thong-ke', 'quan-ly/thong-ke/*']) }} nav-item">
                    <a href="#"><i class="fa fa-bar-chart" aria-hidden="true"></i><span class="nav-label">Thống Kê</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/thong-ke/doanh-thu']) }}"><a href="{{route('admin.statistics.revenueChart')}}">Doanh Thu</a></li>
                        <li class="{{ set_active(['quan-ly/thong-ke/don-hang']) }}"><a href="{{route('admin.statistics.cartChart')}}">Đơn Hàng</a></li>
                        <li class="{{ set_active(['quan-ly/ke-toan/no']) }}"><a href="{{route('admin.statistics.creditorChart')}}">Nợ</a></li>
                    </ul>
                </li>
            @endif

            @if(key_exists('setting_manager', Auth::user()->permissions))
                <li class="{{ set_active(['quan-ly/cai-dat', 'quan-ly/cai-dat/*']) }} nav-item">
                    <a href="#"><i class="fa fa-cog" aria-hidden="true"></i> <span class="nav-label">Cài Đặt</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="{{ set_active(['quan-ly/cai-dat/phong-ban']) }}"><a href="{{route('admin.roles.index')}}">Phòng Ban</a></li>
                    </ul>
                </li>
            @endif

        </ul>

    </div>
</nav>