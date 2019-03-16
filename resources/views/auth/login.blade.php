<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HỆ THỐNG QUẢN LÝ</title>

    <link href="{{asset('themes/inspinia/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('themes/inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/custom.css')}}" rel="stylesheet">

</head>

<body class="gray-bg login-bg">
<div class="loginColumns fadeInDown">
    <div class="row">

        <div class="col-md-6">
            <h1 style="text-align: center;
    font-weight: bold;
    margin-bottom: 70px;">LOGIN
                <span style="font-size: 12px;
    display: block;
    border-bottom: 5px solid #ccc;
    width: 40px;
    margin: auto;
    margin-top: 5px;"></span>
            </h1>
            <div class="">
                <form role="form" action="{{ route('login') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <span class="login-row">
                            <input id="email" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <span class="login-row">
                            <input id="password" type="password" class="form-control" placeholder="Mật khẩu" name="password" required>
                            <i class="fa fa-key" aria-hidden="true"></i>
                        </span>
                    </div>
                    @if(Request::get('to'))<input type="hidden" name="redirectTo" value="{{Request::get('to')}}">@endif
                    <button type="submit" class="btn btn-primary block full-width m-b">Đăng nhập</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
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
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            <small>Phidangmtv@gmail.com</small>
        </div>
        <div class="col-md-6 text-right">
            <small>Copyright © 2018</small>
        </div>
    </div>
</div>
<!-- Mainly scripts -->
<script src="{{asset('themes/inspinia/js/jquery-2.1.1.js')}}"></script>
<script src="{{asset('themes/inspinia/js/bootstrap.min.js')}}"></script>

</body>



</html>