@extends('frontend.layouts.master')

@section('title', 'login')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/css/login.css')}}">
@endsection

@section('content')
    <section id="login-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="row">
                        <div class="col-8">
                            <div class="wrap-login">
                                <h1>ĐĂNG NHẬP</h1>
                                <div>
                                    @if(\Illuminate\Support\Facades\Session::has('success'))
                                    <span class="help-block text-danger">
                                        <strong>{{ \Illuminate\Support\Facades\Session::get('success') }}</strong>
                                    </span>
                                    @endif

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
                                <form role="form" action="{{ route('login') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input id="email" type="email" class="form-control" placeholder="Email" name="email"
                                               value="{{ old('email') }}" required autofocus>
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <input id="password" type="password" class="form-control" placeholder="Mật khẩu"
                                               name="password" required>
                                    </div>
                                    @if(Request::get('to'))
                                        <input type="hidden" name="redirectTo" value="{{Request::get('to')}}">@endif
                                    <div class="form-group">
                                        <input name="remember" type="checkbox" /> <span>Nhớ tài khoản</span>
                                        <a href="/password/reset">Quên mật khẩu?</a>
                                    </div>
                                    <button type="submit" class="btn bt-login block full-width m-b">Đăng nhập
                                    </button>

                                    <div class="justify-content-center" style="margin-bottom:10px;display: flex;align-items: center;">
                                        <span class="line"></span><span>Hoặc</span><span class="line"></span>
                                    </div>
                                    <div class="login-social">
                                        <a href="" class="bt-gg"></a>
                                        <a href="" class="bt-fb"></a>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="col-4 col-right justify-content-center">
                            <div style="color:#fff; text-align:center; margin: 100px 0 20px 0;font-weight: bold;">ĐĂNG KÝ MỚI</div>
                            <div style="line-height: 20px; font-size:13px;text-align:center;margin-bottom: 60px">Hãy là thành viên của <br/> Rampoin để nhận <br/> những ưu đãi tốt nhất.</div>
                            <a class="bt-register" href="{{route('register')}}">ĐĂNG KÝ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection