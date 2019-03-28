@extends('frontend.layouts.master')

@section('title', 'login')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/css/login.css')}}">
@endsection

@section('js')
    <script src="{{asset('themes/inspinia/js/plugins/validate/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $('#register').validate({
                rules: {
                    password_confirm: {
                        equalTo: "#password"
                    }
                },
                messages: {
                    email: "Vui lòng nhập email",
                    password: "Vui lòng nhập mật khẩu",
                    name: "Vui lòng nhập họ tên",
                },
                submitHandler: function(form) {
                    $(".bt-login").attr("disabled", true);
                    form.submit();
                }
            });

        });
    </script>
@endsection

@section('content')
    <section id="login-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="wrap-login">
                        <h1>ĐĂNG KÝ</h1>
                        <div>
                            @if ($errors->has('email'))
                                <span class="help-block text-danger">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                            @endif
                            @if ($errors->has('password'))
                                <span class="help-block text-danger">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                            @endif
                        </div>
                        <form role="form" id="register" action="{{ route('register') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input id="email" type="text" class="form-control email required" placeholder="Email" name="email"
                                               value="{{ old('email') }}" autofocus>
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <input id="password" type="password" class="form-control required" placeholder="Mật khẩu"
                                               name="password">
                                    </div>
                                    <div class="form-group{{ $errors->has('password_confirm') ? ' has-error' : '' }}">
                                        <input id="password_confirm" type="password_confirm" class="form-control required" placeholder="Nhập lại mật khẩu"
                                               name="password_confirm">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <input id="name" type="text" class="form-control required" placeholder="Họ tên" name="name"
                                               value="{{ old('text') }}" autofocus>
                                    </div>
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <input id="phone" type="text" class="form-control" placeholder="Số điện thoại" name="phone">
                                    </div>
                                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                        <input id="address" type="text" class="form-control" placeholder="Địa chỉ" name="address">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn bt-login block m-b" style="padding-left:40px;padding-right:40px;width:auto">ĐĂNG KÝ</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection