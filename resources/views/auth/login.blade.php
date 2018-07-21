<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DHD Film | Login</title>

    <link href="{{asset('themes/inspinia/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('themes/inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/style.css')}}" rel="stylesheet">

</head>

<body class="gray-bg">
<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-6">
            <h2 class="font-bold">Welcome to DHD Film</h2>
            <p>Our Story</p>
            <p>DHD Films is an award winning, full-service video production and motion graphics studio based in Dallas, Texas. </p>
            <p>We tell compelling stories designed to help build powerful brands. </p>
            <p>Our clients include Fortune 500 companies, innovative startups, government and creative agencies. </p>
            <p>We specialize in the creation and deployment of visual campaigns using video, the fastest growing communications medium.</p>

        </div>
        <div class="col-md-6">
            <div class="ibox-content">
                <form class="m-t" role="form" action="{{ route('login') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <input id="email" type="email" class="form-control" placeholder="example@yopmail.com" name="email" value="{{ old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif

                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input id="password" type="password" class="form-control" placeholder="password" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                </form>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            <small>JAVAC Technology</small>
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