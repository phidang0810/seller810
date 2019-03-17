<header>
	<!-- BEGIN: Top Bar -->
	<section id="top-bar">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="tb-left">
						Hotline: 0962343534
					</div>
					<div class="tb-right">
						@if(!Auth::check())
						<a href="{{route('login')}}" title="Đăng nhập" class="sign-in">Đăng nhập</a>
						<a href="#" title="Đăng ký"><button type="button" class="btn btn-custom-tink btn-sm-custom">Đăng ký</button></a>
					</div>
					@else
					<a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" title="Đăng xuất" class="sign-out">Đăng xuất</a>
					@endif

					<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
						{{ csrf_field() }}
					</form>
				</div>
			</div>
		</div>
	</section>
	<!-- END: Top Bar -->

	<!-- BEGIN: Top Navigation -->
	<section id="top-nav">
		<nav class="navbar navbar-expand-lg navbar-light navbar-custom">
			<div class="container">
				<a class="navbar-brand" href="#"><img src="{{asset('themes/frontend/assets/images/portal/logo.png')}}" alt="Rompion"></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mr-auto lobster">
						<li class="nav-item active">
							<a class="nav-link" href="{{route('home')}}">Trang chủ <span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{route('frontend.products.index')}}">Sản phẩm</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">Tin tức</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">Liên hệ</a>
						</li>
					</ul>
					<form class="form-inline my-2 my-lg-0" onsubmit="event.preventDefault();search_products();">
						<input id="search-string" class="form-control mr-sm-2 custom-input" type="search" placeholder="Tìm sản phẩm" aria-label="Tìm sản phẩm">
						<button class="btn my-2 icon-custom-grey" type="submit"><i class="fas fa-search"></i></button>
					</form>
					@if(!Auth::check())
					<a href="{{ route( 'login', ['to' =>Request::url()] ) }}" class="btn icon-custom-grey" id="cart-icon">
						@else
						<a href="{{route('frontend.carts.index')}}" title="Giỏ hàng" class="btn icon-custom-grey" id="cart-icon">
							@endif
							<i class="fas fa-shopping-basket"></i>
							<span class="number"></span>
						</a>
					</div>
				</div>
			</nav>
		</section>
		<!-- END: Top Navigation -->
	</header>