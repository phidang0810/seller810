@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
	@if(isset($category))
	var category = "{{$category->id}}";
	@else 
	var category = null;
	@endif
</script>

<script src="{{asset('themes/frontend/assets/js/products.js')}}" type="text/javascript" charset="utf-8" async defer></script>
@endsection

@section('css')
@endsection

@section('content')
<section id="main-content">
	<div class="container">
		<!-- BEGIN: Top content actions -->
		<div class="row" id="top-content-actions">
			<div class="col">
				<a href="#" class="color-tink"><i class="fas fa-sort-amount-down"></i></a>
				<span class="color-grey">Sort by</span> <span class="color-tink font-weight-bold">Price:</span>
				<select class="form-control form-custom" id="select-sorting">
					<option value="asc">Low to High</option>
					<option value="desc">High to Low</option>
				</select>
			</div>
			<div class="col">
				<div class="float-right" id="display-actions">
					<a href="javascript:;" class="grid-icon active" onclick="set_display_type('grid');"><i class="fas fa-th-large"></i></a>
					<a href="javascript:;" class="list-icon" onclick="set_display_type('list');"><i class="fas fa-list-ul"></i></a>
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
									<i class="fas fa-minus" onclick="clearFilter('cat');"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 filter-content color-grey">
						<div class="filters">
							<ul class="category-list">
								@if(isset($category))
								<li><span>{{$category->name}}</span>
									<ul class="child-list">
										@endif
										@foreach($categories as $category)
										<li><a href="{{ route('frontend.products.category', ['slug' => $category->slug]) }}" class="color-grey">{{$category->name}}</a></li>
										@endforeach
										@if(isset($category))
									</ul></li>
									@endif
								</ul>
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
										<i class="fas fa-minus" onclick="clearFilter('size');"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 filter-content color-grey">
							<div class="filters">
								@foreach($sizes as $size)
								<div class="custom-control custom-radio radio-red">
									<input type="radio" id="sizeRadio{{$size->id}}" name="sizeRadio" class="custom-control-input" value="{{$size->name}}">
									<label class="custom-control-label" for="sizeRadio{{$size->id}}">{{$size->name}}</label>
								</div>
								@endforeach
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
										<i class="fas fa-minus" onclick="clearFilter('color');"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 filter-content color-grey" id="color-filters">
							<div class="filters">
								@foreach($colors as $color)
									<a href="javascript:;" title="{{$color->name}}" class="color-choice" data-id="{{$color->name}}">
										<span style="background-color: {{$color->code}}"></span>
									</a>
								@endforeach
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
										<i class="fas fa-minus" onclick="clearFilter('price');"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 filter-content color-grey">
							<div class="filters">							
								@foreach($product_prices as $product_price)
								<div class="custom-control custom-radio radio-red">
									<input type="radio" id="productPriceRadio{{$product_price['value']}}" name="productPriceRadio" class="custom-control-input" value="{{$product_price['value']}}">
									<label class="custom-control-label" for="productPriceRadio{{$product_price['value']}}">{{$product_price['label']}}</label>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<!-- END: Filters section -->
				</div>
				<!-- END: Filters -->

				<!-- BEGIN: Products -->
				<div class="col-md-9" id="products-wrapper">
					<div class="row" id="products-list">
					</div>
					<div class="row" id="pagination">
						<div class="col-md-12">
							<nav aria-label="Products paginations">
								<ul class="pagination justify-content-center custom-pagination">

								</ul>
							</nav>
						</div>
					</div>
				</div>
				<!-- END: Products -->
			</div>
			<!-- END: Filters & Products -->
		</div>
	</section>
	@endsection