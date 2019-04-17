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
							<a href="{{$category->link}}">
							<img class="img-responsive" src="{{asset('storage/' . $category->photo)}}" />
							</a>
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
							<a href="{{route('frontend.products.view', [$product->id, $product->slug])}}">
								<img class="img-responsive" src="{{asset('storage/' . $product->thumb)}}" />
								<div class="name">{{$product->name}}</div>
							</a>
						</div>
					</div>
				@endforeach
			</div>

			@if(count($ads) > 0)
			<div class="row">
				@if(isset($ads[0]))
				<div class="col-xs-6">
					<a href="{{$ads[0]->link}}">
					<img style="max-width:100%" class="img-responsive" src="{{asset('storage/' . $ads[0]->thumb)}}" />
					</a>
				</div>
				@endif
				@if(isset($ads[1]))
					<div class="col-xs-6">
						<a href="{{$ads[1]->link}}">
						<img style="max-width:100%" class="img-responsive" src="{{asset('storage/' . $ads[1]->thumb)}}" />
						</a>
					</div>
				@endif
			</div>
			@endif

			<div class="tt-section">Sản phẩm nổi bật<span class="line"></span></div>
			<div class="row">
				@foreach($hotProducts as $product)
					<div class="col-sm-6 col-md-3">
						<div class="item-product">
							<a href="{{route('frontend.products.view', [$product->id, $product->slug])}}">
								<img class="img-responsive" src="{{asset('storage/' . $product->thumb)}}" />
								<div class="name">{{$product->name}}</div>
							</a>
						</div>
					</div>
				@endforeach
			</div>

			<div class="tt-section">Tin tức<span class="line"></span></div>
			<div class="row">
				@foreach($posts as $post)
					<div class="col-sm-6 col-md-4">
						<div class="item-post">
							<a href="{{route('frontend.detailPost', [$post->id, $post->slug])}}">
								<img class="img-responsive" src="{{asset('storage/' . $post->thumb)}}" />
								<div class="social"><label class="badge badge-success">@if($post->category_id == POST_CATEGORY_TIN_TUC) Tin tức @else Khuyến mãi @endif</label>
									<span>{{$post->created_at}}</span></div>
								<div class="name">{{$post->title}}</div>
								<p>
									{!!$post->description!!}
								</p>
							</a>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>
@endsection