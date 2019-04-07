@extends('frontend.layouts.master')

@section('title', $title)

@section('js')
<script type="text/javascript">
	var urlGetInCartCart = "{{route('frontend.carts.payment')}}";
	var urlPostStorePayment = "{{route('frontend.carts.storePayment')}}";
</script>
<script src="{{asset('themes/frontend/assets/js/payment.js')}}" type="text/javascript" charset="utf-8" async defer></script>
@endsection

@section('css')
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col">
			<h3 class="title-page text-center">Thông tin thanh toán</h3>
		</div>
	</div>
	<div class="row" id="alert-section"></div>
	<form action="POST" target="{{route('frontend.carts.storePayment')}}" id="form-payment">
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col">
						<h6 class="color-tink wrapper-title">Địa chỉ giao hàng</h6>
					</div>
				</div>
				<div class="row">
					<div class="col-6 group-input">
						<div class="row">
							<div class="col">Họ tên</div>
						</div>
						<div class="row">
							<div class="col"><input type="text" class="form-control" placeholder="Nhập họ và tên" name="name"></div>
						</div>
					</div>
					<div class="col-6 group-input">
						<div class="row">
							<div class="col">Email</div>
						</div>
						<div class="row">
							<div class="col"><input type="text" class="form-control" placeholder="Nhập email" name="email"></div>
						</div>
					</div>
					<div class="col-6 group-input">
						<div class="row">
							<div class="col">Số điện thoại</div>
						</div>
						<div class="row">
							<div class="col"><input type="text" class="form-control" placeholder="Nhập số điện thoại" name="phone"></div>
						</div>
					</div>
					<div class="col-6 group-input">
						<div class="row">
							<div class="col">Địa chỉ</div>
						</div>
						<div class="row">
							<div class="col"><input type="text" class="form-control" placeholder="Nhập địa chỉ" name="address"></div>
						</div>
					</div>
					<div class="col-6 group-input">
						<div class="row">
							<div class="col">Thành phố</div>
						</div>
						<div class="row">
							<div class="col">
								<select name="city" id="city" class="form-control">
									<option value="" selected>-- Chọn thành phố --</option>
									{!! $city_options !!}
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-6 group-input">
						<div class="custom-control custom-checkbox mr-sm-2">
							<input type="checkbox" class="custom-control-input" id="is_payment_default" name="is_payment_default" value="true">
							<label class="custom-control-label" for="is_payment_default">Đặt làm thanh toán mặc định</label>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col">
						<h6 class="color-tink wrapper-title">Phương thức thanh toán</h6>
					</div>
				</div>
				<div class="row" id="payment-bank">
					<div class="col">
						<div class="payment-method">
							<a href="javascript:;" onclick="bankPayment();">
								<div class="row">
									<div class="col-2">
										<i class="fas fa-university"></i>
									</div>
									<div class="col-10">
										<div class="row">
											<div class="col title">Chuyển khoản</div>
										</div>
										<div class="row">
											<div class="col">Ngân hàng Vietcombank</div>
										</div>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
				<div class="row" id="payment-cod">
					<div class="col">
						<div class="payment-method">
							<a href="javascript:;" onclick="codPayment();">
								<div class="row">
									<div class="col-2">
										<i class="fas fa-motorcycle"></i>
									</div>
									<div class="col-10">
										<div class="row">
											<div class="col title">COD</div>
										</div>
										<div class="row">
											<div class="col">Thanh toán khi nhận hàng</div>
										</div>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
				<hr>
				<div id="transport-info">
					<div class="row">
						<div class="col">
							<h6 class="color-tink wrapper-title">Thông tin vận chuyển</h6>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<div class="transport-method">
								<a href="javascript:;" onclick="truckTransport();">
									<div class="row">
										<div class="col"><i class="fas fa-truck-moving"></i> <span class="title">Chành xe</span></div>
									</div>
								</div>
							</a>
						</div>
						<div class="col">
							<div class="transport-method">
								<a href="javascript:;" onclick="postOfficeTransport();">
									<div class="row">
										<div class="col"><i class="fas fa-archive"></i> <span class="title">Bưu điện</span></div>
									</div>
								</div>
							</a>
						</div>
					</div>
					<div class="row" id="truck-transport-info">
						<div class="col-6 group-input">
							<div class="row">
								<div class="col">Tên nhà xe</div>
							</div>
							<div class="row">
								<div class="col"><input type="text" class="form-control" placeholder="Nhập tên nhà xe" name="garage-name"></div>
							</div>
						</div>
						<div class="col-6 group-input">
							<div class="row">
								<div class="col">Số điện thoại</div>
							</div>
							<div class="row">
								<div class="col"><input type="text" class="form-control" placeholder="Nhập số điện thoại" name="garage-phone"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row cart-info">
					<div class="col">
						<div class="row">
							<div class="col title"><h6 class="color-tink wrapper-title">Đơn hàng</h6></div>
						</div>
						<div class="row cart-details" id="cart-details">
							
						</div>
						<hr>
						<div class="row">
							<div class="col-9">Tổng cộng</div>
							<div class="col-3 text-right" id="price"></div>
						</div>
						<div class="row">
							<div class="col-9">Phí ship</div>
							<div class="col-3 text-right" id="shipping-fee"></div>
						</div>
						<div class="row">
							<div class="col-9">
								<input type="text" name="discount-code" placeholder="Mã giảm giá" class="form-control">
							</div>
							<div class="col-3">
								<a href="javascript:;" class="btn btn-custom-discount-cart">Xác nhận</a>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-6"><h6 class="total-price-label">Thành tiền</h6></div>
							<div class="col-6 color-tink text-right" id="total_price"></div>
						</div>
					</div>
				</div>
				<div class="row payment-submit">
					<a href="javascript:;" onclick="storePayment()" class="btn btn-custom-payment">Thanh toán</a>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection