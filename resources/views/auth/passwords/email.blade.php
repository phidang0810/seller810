@extends('frontend.layouts.master')

@section('title', 'login')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/css/login.css')}}">
@endsection

@section('content')
    <section id="login-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-5">
                    <div class="wrap-login">
                        <h1>QUÊN MẬT KHẨU</h1>
                        <div>
                            @if(\Illuminate\Support\Facades\Session::has('success'))
                                <span class="help-block text-success">
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
                        <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <input id="email" type="email" class="form-control" placeholder="Email" name="email"
                                       value="{{ old('email') }}" required autofocus>
                            </div>
                            <button type="submit" class="btn bt-login block full-width m-b">Lây lại mật khẩu
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
