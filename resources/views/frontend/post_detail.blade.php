@extends('frontend.layouts.master')

@section('title', $data->title)

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
		<div class="container">
			<h3 style="font-size:20px">{{$data->title}}</h3>
			<div class="share">
				<label class="badge badge-success">@if($data->category_id == POST_CATEGORY_TIN_TUC) Tin tức @else Khuyến mãi @endif</label>
				<span>{{$data->created_at}}</span>
			</div>
			<div>
				{!! $data->content !!}
			</div>
		</div>
	</section>
@endsection