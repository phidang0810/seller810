@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
	<script src="{{asset('themes/frontend/assets/plugins/owl/owl.carousel.min.js')}}" type="text/javascript"></script>
	<script>
        $(document).ready(function(){
            $(".owl-carousel").owlCarousel({
                items: 1
			});
        });
	</script>

@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/plugins/owl/owl.theme.default.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/plugins/owl/owl.carousel.css')}}">
@endsection

@section('content')
	<section id="main-content">
		<div id="slider" style="margin-bottom: 40px">
			<div class="owl-carousel">
				@foreach($slides as $slide)
					<div><img src="{{asset('storage/' . $slide->photo)}}" /></div>
				@endforeach
			</div>
		</div>
		<div class="container">
			<div class="row">
				@foreach($categories as $category)
					<div class="col-sm-6 col-md-3">
						<div class="item-category">
							<img class="img-responsive" src="{{asset('storage/' . $category->photo)}}" />
							<div class="name">{{$category->name}}</div>
						</div>
					</div>
				@endforeach
			</div>

			<div class="tt-section">Sản phẩm mới nhất<span class="line"></span></div>
			<div class="row">
				@foreach($newProducts as $product)
					<div class="col-sm-6 col-md-3">
						<div class="item-product">
							<a href="">
								<img class="img-responsive" src="{{asset('storage/' . $product->thumb)}}" />
								<div class="name">{{$product->name}}</div>
							</a>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>
@endsection