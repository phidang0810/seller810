<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title')</title>

	<!-- BEGIN: CSS -->
	<!-- BEGIN: Global CSS -->
	<!-- Font Lobsters -->
	<link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/plugins/bootstrap-4.2.1-dist/css/bootstrap.min.css')}}">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/css/style.css')}}" media="all">
	<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/css/color.css')}}" media="all">
	<!-- END: Global CSS -->
	<!-- BEGIN: Page CSS -->
    @yield('css')
	<!-- END: CSS -->
</head>
<body>
	<!-- BEGIN: Header -->
	@include('frontend.layouts._partials._header')
	<!-- END: Header -->

	<!-- BEGIN: Main Content -->
	<section id="main-content">
		<div class="container">
			@yield('content')
		</div>
	</section>
	<!-- END: Main Content -->

	<!-- BEGIN: Footer -->
	@include('frontend.layouts._partials._footer')
	<!-- END: Footer -->

	<!-- BEGIN: JS -->
	<!-- BEGIN: Global JS -->
	<script src="{{asset('themes/frontend/assets/plugins/jquery/jquery-3.3.1.min.js')}}" type="text/javascript" charset="utf-8"></script>
	<script src="{{asset('themes/frontend/assets/plugins/bootstrap-4.2.1-dist/js/bootstrap.min.js')}}" type="text/javascript" charset="utf-8" async defer></script>
	<!-- Main JS -->
	<script src="{{asset('themes/frontend/assets/js/main.js')}}" type="text/javascript" charset="utf-8" async defer></script>
	<!-- END: Global JS -->
	<!-- BEGIN: Web JS -->
    @yield('js')
	<!-- END: JS -->
</body>
</html>