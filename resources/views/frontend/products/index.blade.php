@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
@endsection

@section('css')
@endsection

@section('content')
<!-- BEGIN: Top content actions -->
<div class="row" id="top-content-actions">
	<div class="col">
		<a href="#" class="color-tink"><i class="fas fa-sort-amount-down"></i></a>
		<span class="color-grey">Sort by</span> <span class="color-tink font-weight-bold">Price:</span>
		<select class="form-control form-custom" id="select-sorting">
			<option>Low to High</option>
			<option>High to Low</option>
		</select>
	</div>
	<div class="col">
		<div class="float-right" id="display-actions">
			<a href="#" class="active"><i class="fas fa-th-large"></i></a>
			<a href="#"><i class="fas fa-list-ul"></i></a>
		</div>
	</div>
</div>
<!-- END: Top content actions -->

<!-- BEGIN: Filters & Products -->
<div class="row" id="filters-products">
	<!-- BEGIN: Filters -->
	<div class="col-md-3" id="filters">
		<!-- BEGON: Filters section -->
		<div class="row filter-wrapper" id="category">
			<div class="col-md-12 filter-header lobster">
				<div class="row">
					<div class="col header-title color-tink">
						<div class="row"><h6>Danh mục</h6></div>
					</div>
					<div class="col">
						<div class="float-right header-right color-grey">
							<i class="fas fa-minus"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 filter-content color-grey">
				<div class="filters">
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio1">Áo bé trai</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio2">Áo bé gái</label>
					</div>
				</div>
			</div>
		</div>
		<!-- END: Filters section -->

		<!-- BEGON: Filters section -->
		<div class="row filter-wrapper" id="size">
			<div class="col-md-12 filter-header lobster">
				<div class="row">
					<div class="col header-title color-tink">
						<div class="row"><h6>Size</h6></div>
					</div>
					<div class="col">
						<div class="float-right header-right color-grey">
							<i class="fas fa-minus"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 filter-content color-grey">
				<div class="filters">
					<div class="custom-control custom-radio radio-red">
						<input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio1">1-8</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio2">2-8</label>
					</div>
				</div>
			</div>
		</div>
		<!-- END: Filters section -->

		<!-- BEGON: Filters section -->
		<div class="row filter-wrapper" id="color">
			<div class="col-md-12 filter-header lobster">
				<div class="row">
					<div class="col header-title color-tink">
						<div class="row"><h6>Màu sắc</h6></div>
					</div>
					<div class="col">
						<div class="float-right header-right color-grey">
							<i class="fas fa-minus"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 filter-content color-grey">
				<div class="filters">
					<div class="custom-control custom-radio radio-red">
						<input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio1">Đỏ</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio2">Cam</label>
					</div>
				</div>
			</div>
		</div>
		<!-- END: Filters section -->

		<!-- BEGON: Filters section -->
		<div class="row filter-wrapper" id="price">
			<div class="col-md-12 filter-header lobster">
				<div class="row">
					<div class="col header-title color-tink">
						<div class="row"><h6>Giá</h6></div>
					</div>
					<div class="col">
						<div class="float-right header-right color-grey">
							<i class="fas fa-minus"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 filter-content color-grey">
				<div class="filters">
					<div class="custom-control custom-radio radio-red">
						<input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio1">$25 - $100</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
						<label class="custom-control-label" for="customRadio2">$100 - $500</label>
					</div>
				</div>
			</div>
		</div>
		<!-- END: Filters section -->
	</div>
	<!-- END: Filters -->

	<!-- BEGIN: Products -->
	<div class="col-md-9" id="products-wrapper">
		<div class="row" id="products-list">
			<div class="col-md-4 col-sm-6 product">
				<a href="#">
					<img src="assets/images/portal/1.png" alt="">
					<h6 class="product-name">Bộ áo quần trẻ em nam</h6>
					<h6 class="product-price">200.000 VND</h6>
				</a>
			</div>
		</div>
		<div class="row" id="pagination">
			<div class="col-md-12">
				<nav aria-label="Products paginations">
					<ul class="pagination justify-content-center custom-pagination">
						<li class="page-item active">
							<a class="page-link" href="#" tabindex="-1">1</a>
						</li>
						<li class="page-item"><a class="page-link" href="#">2</a></li>
						<li class="page-item"><a class="page-link" href="#">3</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</div>
	<!-- END: Products -->
</div>
<!-- END: Filters & Products -->
@endsection