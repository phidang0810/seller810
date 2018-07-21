<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="{{asset('themes/inspinia/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia//font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <!-- Data Tables -->
    <link href="{{asset('themes/inspinia/css/plugins/dataTables/dataTables.bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/dataTables/dataTables.responsive.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/dataTables/dataTables.tableTools.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/swal/sweetalert.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">

    <link href="{{asset('themes/inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/custom.css')}}" rel="stylesheet">
    @yield('css')
</head>

<body>

<div id="wrapper">
    @include('admin._partials._sidebar')

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown" id="notify-menu">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                            <i class="fa fa-bell"></i>  <span class="label label-primary" id="notify-number" data-number="0"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts" id="notify-list">

                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>

            </nav>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                @if (isset($title))
                <h2>{{$title}}</h2>
                @endif
                @include('admin._partials._breadcrumbs')
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            @yield('content')
        </div>
        @include('admin._partials._footer')

    </div>
</div>



<!-- Mainly scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="{{asset('themes/inspinia/js/bootstrap.min.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/jeditable/jquery.jeditable.js')}}"></script>

<!-- Data Tables -->
<script src="{{asset('themes/inspinia/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/dataTables/dataTables.responsive.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/dataTables/dataTables.tableTools.min.js')}}"></script>

<!-- Custom and plugin javascript -->
<script src="{{asset('themes/inspinia/js/inspinia.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/pace/pace.min.js')}}"></script>
<script src="{{asset('plugins/swal/sweetalert.min.js')}}"></script>
<script src="{{asset('themes/inspinia/js/plugins/toastr/toastr.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@yield('js')
</body>

</html>
