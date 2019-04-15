@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
	<script src="{{asset('themes/frontend/assets/plugins/owl/owl.carousel.min.js')}}" type="text/javascript"></script>
	<script>
        $(document).ready(function(){\
        });
	</script>

@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/plugins/owl/owl.theme.default.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/plugins/owl/owl.carousel.css')}}">
@endsection

@section('content')
	<section id="main-content">
		<h2 class="text-center" style="margin-bottom: 40px;font-size: 20px;font-weight: bold;">{{$title}}</h2>
		<div class="container">
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
			<div class="text-center">
				{{ $posts->links() }}
			</div>
		</div>
	</section>
@endsection