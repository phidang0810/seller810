@extends('frontend.layouts.master')

@section('title', 'Liên Hệ')

@section('js')
	<script src="{{asset('themes/inspinia/js/plugins/validate/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script>
        $(document).ready(function(){
            $('#contact').validate({
                messages: {
                    email: "Vui lòng nhập email",
                    phone: "Vui lòng nhập sdt của bạn",
                    name: "Vui lòng nhập họ tên",
                },
                submitHandler: function(form) {
                    //$(".bt-submit").attr("disabled", true);
                    form.submit();
                }
            });

        });
	</script>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/css/contact.css')}}">
@endsection

@section('content')
	<section id="main-content" class="page-contact">
		<div class="container">
			<div class="text-center">
				<h1 style="margin-bottom: 20px">Liên hệ</h1>
			</div>
			<div class="row">
				<div class="col-sm-5">
					<img class="img-responsive" style="max-width: 100%" src="{{asset('themes/frontend/assets/images/bg-contact.png')}}" />
				</div>
				<div class="col-sm-7">
					<h3>Thông tin liên hệ</h3>
					<p style="margin-bottom: 20px">
						561 Nguyễn Phúc Châu, P.4, Quận Tân Bình, TP.HCM <br/>
						Hotline: 0635.523.534<br/>
						Email: rampion@gmail.com <br/>

					</p>
					<form id="contact" type="post" action="/lien-he">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
									<input type="text" class="form-control required" placeholder="Họ tên" name="name"
										   value="{{ old('name') }}" autofocus />
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
									<input type="text" class="form-control email required" placeholder="email" name="email"
										   value="{{ old('email') }}" />
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
									<input type="text" class="form-control" placeholder="Số điện thoại" name="phone"
										   value="{{ old('phone') }}" />
								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
									<textarea rows="5" class="form-control" placeholder="Nội dung" name="content"> {{ old('content') }} </textarea>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<button style="border-radius: 3px;padding:5px 60px;font-size:14px" type="submit" class="btn btn-danger">GỬI</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection