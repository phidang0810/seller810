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
    <link href="{{asset('themes/inspinia/css/plugins/jasny/jasny-bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia//font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">

    <!-- Data Tables -->
    <link href="{{asset('themes/inspinia/css/plugins/dataTables/dataTables.bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/dataTables/dataTables.responsive.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/dataTables/dataTables.tableTools.min.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/swal/sweetalert.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">

    <link href="{{asset('/themes/inspinia/css/plugins/switchery/switchery.css')}}" rel="stylesheet" />
    <link href="{{asset('themes/inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/plugins/summernote/summernote.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('themes/inspinia/css/custom.css')}}" rel="stylesheet">
    @yield('css')
</head>

<body>

    <div id="wrapper">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top bg-white" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header hidden">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <div class="c-header-logo-wrapper">
                    <h1>
                        <a href="" title=""><img src="{{asset('themes/inspinia/img/logo.png')}}" alt=""></a>
                    </h1>
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
        @if(!isset($hasSidebar) || $hasSidebar == true)
        @include('admin._partials._sidebar')
        @endif

        <div id="page-wrapper" class="gray-bg">
            @if(!isset($hasTitle) || $hasTitle == true)
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    @if (isset($title))
                    <h2>{{$title}}</h2>
                    @endif
                    @include('admin._partials._breadcrumbs')
                </div>
            </div>
            @endif
            <div class="wrapper wrapper-content animated fadeInRight">
                @yield('content')
            </div>
            @include('admin._partials._footer')

        </div>
    </div>



    <!-- Mainly scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="{{asset('themes/inspinia/js/bootstrap.min.js')}}"></script>
    <!-- Jasny -->
    <script src="{{asset('themes/inspinia/js/plugins/jasny/jasny-bootstrap.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/jeditable/jquery.jeditable.js')}}"></script>

    <!-- Data Tables -->
    <script src="{{asset('themes/inspinia/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/dataTables/dataTables.responsive.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/dataTables/dataTables.tableTools.min.js')}}"></script>

    <script src="{{asset('themes/inspinia/js/plugins/summernote/summernote.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{asset('themes/inspinia/js/inspinia.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/swal/sweetalert.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/colorpicker/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/cleave/cleave.min.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/cleave/cleave-phone.i18n.js')}}"></script>

    <script src="{{asset('themes/inspinia/js/plugins/switchery/switchery.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $.extend( $.validator.messages, {
            required: "Vui lòng nhập.",
            remote: "Hãy sửa cho đúng.",
            email: "Định dạng email không hợp lệ.",
            url: "Hãy nhập URL.",
            date: "Hãy nhập ngày.",
            dateISO: "Hãy nhập ngày (ISO).",
            number: "Hãy nhập số.",
            digits: "Hãy nhập chữ số.",
            creditcard: "Hãy nhập số thẻ tín dụng.",
            equalTo: "Không đúng! Hãy nhập lại lần nữa.",
            extension: "Phần mở rộng không đúng.",
            maxlength: $.validator.format( "Hãy nhập từ {0} kí tự trở xuống." ),
            minlength: $.validator.format( "Hãy nhập từ {0} kí tự trở lên." ),
            rangelength: $.validator.format( "Hãy nhập từ {0} đến {1} kí tự." ),
            range: $.validator.format( "Hãy nhập từ {0} đến {1}." ),
            max: $.validator.format( "Hãy nhập từ {0} trở xuống." ),
            min: $.validator.format( "Hãy nhập từ {0} trở lên." )
        } );
    </script>
    @yield('js')
</body>

</html>
