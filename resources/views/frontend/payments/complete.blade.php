@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
@endsection

@section('css')
@endsection

@section('content')
<div class="container" id="cart-complete">
	<div class="row">
		<div class="col">
			<h3 class="color-tink title text-center">Hoàn tất đơn hàng</h3>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<p class="success-icon text-center"><i class="far fa-check-circle"></i></p>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h4 class="title text-center">Mã đơn hàng - {{$cart_code}}</h4>
		</div>
	</div>
	<div class="row">
		<div class="col text-center description">
			<p>Đơn hàng đã được mua thành công.</p>
			@if($payment_method == 1)
			<p>Để hoàn tất đơn hàng, bạn vui lòng chuyển khoản theo thông tin bên dưới.</p>
			<p>Bạn vui lòng chuyển khoản theo cú pháp <span class="forced">[Mã đơn hàng - Số điện thoại]</span></p>
			@endif
		</div>
	</div>
	@if($payment_method == 1)
	<div class="row">
		<div class="col text-center">
			<div class="bank-info">
				<p><span class="forced">Ngân hàng Vietcombank</span></p>
				<p>Tên chủ tài khoản: <span class="forced">Đặng Thị Vân</span></p>
				<p>Số tài khoản: <span class="forced">0441 0006 96607</span></p>
				<p>Chi nhánh: <span class="forced">Công nghiệp Tân Bình</span></p>
			</div>
		</div>
	</div>
	@endif
	<div class="row">
		<div class="col text-center continue-shopping">
			<a href="{{route('frontend.products.index')}}" class="btn btn-custom-continue-shopping">Tiếp tục mua hàng</a>
		</div>
	</div>
</div>
@endsection