@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
	var urlGetInCartCart = "{{route('frontend.carts.index')}}";
	var urlGetCartDetails = "{{route('frontend.carts.index')}}";
</script>
<script src="{{asset('themes/frontend/assets/js/cart.js')}}" type="text/javascript" charset="utf-8" async defer></script>
@endsection

@section('css')
@endsection

@section('content')
<div class="container" id="cart-detail-wrapper">
	<div class="row">
		<div class="col-9" id="cart-details">
			
		</div>
		<div class="col-3" id="cart-infor">
			<div class="row" id="cart-price">
				<div class="col-12">
					<div class="row">
						<div class="col-5">
							Tạm tính:
						</div>
						<div class="col-7">
							<p id="pre-cal"></p>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-5">
							Thành tiền:
						</div>
						<div class="col-7">
							<p id="total-price"></p>
						</div>
					</div>
				</div>
			</div>
			<div class="row" id="submit-cart">
				<div class="col-12">
					<a href="{{route('frontend.carts.payment')}}" class="btn btn-custom-order">Tiến hành đặt hàng</a>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection