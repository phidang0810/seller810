@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('css')
    <link href="{{asset('themes/inspinia/css/plugins/switchery/switchery.css')}}" rel="stylesheet" />
@endsection

@section('js')
    <script src="{{asset('plugins/krpano/pano.js')}}"></script>
    <script src="{{asset('plugins/file-upload/vendor/jquery.ui.widget.js')}}"></script>
    <script src="{{asset('plugins/file-upload/jquery.iframe-transport.js')}}"></script>
    <script src="{{asset('plugins/file-upload/jquery.fileupload.js')}}"></script>
    <script src="{{asset('plugins/file-upload/jquery.fileupload-process.js')}}"></script>
    <script src="{{asset('plugins/file-upload/jquery.fileupload-image.js')}}"></script>
    <script src="{{asset('plugins/file-upload/jquery.fileupload-validate.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/plugins/switchery/switchery.js')}}"></script>
    <script src="{{asset('themes/inspinia/js/app.js')}}"></script>
    <!-- Page-Level Scripts -->
    <script>
        var AWS_URL = "https://s3.amazonaws.com/" + "{{env('AWS_S3_BUCKET')}}";
        var xhr = [];
        var interval = false;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        const maxImageSize = 2073741824;
        const maxVideoSize = 1073741824;

        function formatBytes(bytes, decimals, binaryUnits) {
            if (bytes == 0) {
                return '0 Bytes';
            }
            var unitMultiple = (binaryUnits) ? 1024 : 1000;
            var unitNames = (unitMultiple === 1024) ?
                ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'] :
                ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            var unitChanges = Math.floor(Math.log(bytes) / Math.log(unitMultiple));
            return parseFloat((bytes / Math.pow(unitMultiple, unitChanges)).toFixed(decimals || 0)) + ' ' + unitNames[unitChanges];
        }

        function loadMainScene() {
            $.ajax({
                url: "{{route('admin.scene.list')}}",
                dataType: 'json',
                data: {panorama_id: $("#panoramaId").val()},
                beforeSend: function () {
                    $("#refresh").html('<i class="fa fa-refresh"></i> Scene');
                },
                success: function (response) {
                    var data = response.results.scenes;
                    if (data.length !== $('#mainScene option').length - 1) {
                        var options = '<option value="0">-- Select a scene --</option>';
                        for (var i = 0; i < data.length; i++) {
                            var selected = '';
                            if (data[i].is_main) selected += 'selected';
                            options += '<option value="' + data[i].id + '" ' + selected + '>' + data[i].name + '</option>';
                        }
                        $("#mainScene").html(options)
                        $("#refresh").html('<i class="fa fa-refresh"></i> Scene');
                    }
                }
            })
        }

        function loadIcon() {
            $.ajax({
                url: "{{route('admin.icon.list')}}",
                dataType: 'json',
                data: {panorama_id: $("#panoramaId").val()},
                beforeSend: function () {

                },
                success: function (response) {
                    var data = response.results;
                    var options = '<option value="0">-- None --</option>';
                    for (var i = 0; i < data.length; i++) {
                        var selected = '';
                        if (data[i].is_tag) selected += 'selected';
                        options += '<option value="' + data[i].id + '" ' + selected + '>' + data[i].name + '</option>';
                    }
                    $("#icon-select").html(options);
                }
            })
        }

        function getNotification(hasNewNotify) {
            $.ajax({
                url: "{{route('admin.notification.me')}}",
                dataType: 'json',
                data:{take:10},
                success: function (response) {
                    toastr.options = {
                        closeButton: true,
                        progressBar: true,
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    var notifyNumber = $('#notify-number').attr('data-number');
                    var html = '';
                    $.each(response.results, function(index, item) {
                        var active ='';
                        if (item.new) {
                            notifyNumber++;
                            toastr.success(item.message);
                            hasNewNotify = true;
                            active = 'class="new"'
                        }
                        if (index === response.results.length - 1) {
                            active += ' style="border-bottom:none"';
                        }
                        html += '<li '+active+'>' +
                            '        <a href="#">' +
                            '             <div>' +
                            '                   <img src="/images/profile_small.jpg" />'+
                            '                   <p>' + item.message +'</p>' +
                            '                   <div class="text-right text-muted small"><i class="fa fa-clock-o" aria-hidden="true"></i> '+ item.date +'</div>'+
                            '               </div>' +
                            '         </a>' +
                            '    </li>';
                    });
                    if (hasNewNotify){
                        $('#notify-list').html(html);
                        if (notifyNumber > 0) {
                            $('#notify-number').text(notifyNumber);
                            if(!document.hasFocus()){
                                interval = setInterval(function(){
                                    document.title="Have new notification";
                                }, 1000);
                            }
                        }
                    }
                }
            })
        }
        $(document).ready(function () {
            var pageTitle = $("title").text();
            $(window).focus(function() {
                $("title").text(pageTitle);
                if (interval) clearInterval(interval);
            });
            getNotification(true);
            setInterval(function(){
                getNotification(false);
                loadMainScene();
            }, 5000);

            @if(isset($main_scene))
            $("#pano").myPano();
            @endif
            loadMainScene();

            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

            elems.forEach(function(html) {
                var switchery = new Switchery(html);
            });
            $('#notify-menu').click(function(){
                $('#notify-number').text('');
                setTimeout(function() {
                    $('#notify-menu').find('li').removeClass('new');
                }, 4000);
            });
            $("#refresh").click(function () {
                loadMainScene();
            });
            $('[data-toggle="tooltip"]').tooltip();

            $("#bt-reset").click(function () {
                $("#mainForm")[0].reset();
            });
            var sceneTable = $('#sceneTable').dataTable({
                searching: false,
                processing: true,
                serverSide: true,
                "dom": '<"top"i>rt<"bottom"flp><"clear">',
                ajax: {
                    "url": "{{route('admin.scene.index')}}",
                    "data": function (d) {
                        d.panorama_id = $('#panoramaId').val()
                    }
                },
                columns: [
                    {data: 'name'},
                    {data: 'image'},
                    {data: 'status'}
                ],
                aoColumnDefs: [
                    // Column index begins at 0
                    {"sClass": "text-center", "aTargets": [1, 2]},
                    {"sClass": "v-middle", "aTargets": [0,1, 2]}
                ],
                "info": false,         // Will show "1 to n of n entries" Text at bottom
                "lengthChange": false // Will Disabled Record number per page

            });

            var videoTable = $('#videoTable').dataTable({
                searching: false,
                processing: true,
                serverSide: true,
                "dom": '<"top"i>rt<"bottom"flp><"clear">',
                ajax: {
                    "url": "{{route('admin.video.index')}}",
                    "data": function (d) {
                        d.panorama_id = $('#panoramaId').val()
                    }
                },
                columns: [
                    {data: 'name'},
                    {data: 'video'},
                    {data: 'status'}
                ],
                aoColumnDefs: [
                    // Column index begins at 0
                    {"sClass": "text-center", "aTargets": [1, 2]}
                ],
                "info": false,         // Will show "1 to n of n entries" Text at bottom
                "lengthChange": false // Will Disabled Record number per page

            });

            var iconTable = $('#iconTable').dataTable({
                searching: false,
                processing: true,
                serverSide: true,
                "dom": '<"top"i>rt<"bottom"flp><"clear">',
                ajax: {
                    "url": "{{route('admin.icon.index')}}",
                    "data": function (d) {
                        d.panorama_id = $('#panoramaId').val()
                    }
                },
                columns: [
                    {data: 'name'},
                    {data: 'icon'},
                    {data: 'status'}
                ],
                aoColumnDefs: [
                    // Column index begins at 0
                    {"sClass": "text-center", "aTargets": [1, 2]}
                ],
                "info": false,         // Will show "1 to n of n entries" Text at bottom
                "lengthChange": false // Will Disabled Record number per page

            });

            $('#fScene').fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var acceptFileTypes = /^image\/(gif|jpe?g|png|tiff)$/i;
                    var error = $(this).find(".errors");
                    if (data.files[0] && !acceptFileTypes.test(data.files[0].type)) {
                        error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                        error.find("ul").append('<li>Not an accepted file type</li>')
                        return;
                    }
                    if (data.files[0] && data.files[0].size > maxImageSize) {
                        error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                        error.find("ul").append('<li>Max file size: ' + formatBytes(maxImageSize) + '</li>');
                        return;
                    }
                    if (data.files && data.files[0]) {
                        var files = $(this).find('.files');
                        files.html('<p><i class="fa fa-file"></i> ' + data.files[0].name + '</p>');
                    }
                    $(this).find('.btn-primary').removeAttr('disabled');
                    $(this).find(".errors").html("");
                    $(this).find('.btn-primary').off('click').on('click', function () {
                        var form = $(this).closest("form");
                        if (form.find('input[name="name"]').val() != "") {
                            form.find('.mprogress-bar').show();
                            $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i> Uploading');
                            $(this).prop('disabled', true);
                            xhr.scene = data.submit();
                        }
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        progress + '%'
                    );
                    $(this).find('.mprogress-bar span').text(progress + '%');
                },
                done: function (e, data) {
                    $(this).find(".errors").html("");
                    $(this).find('.btn-primary').hide();
                    $(this).find('.alert-success').show();
                    $(this).find('.mprogress-bar').hide();
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        '0%'
                    );
                    xhr.scene = null;
                },
                fail: function (e, data) {
                    var error = $(this).find(".errors");
                    error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                    $.each(data.jqXHR.responseJSON.errors, function (key, value) {
                        error.find("ul").append('<li>' + value + '</li>')
                    });
                    $(this).find('.btn-primary').html('Upload');
                    $(this).find('.btn-primary').prop('disabled', false);
                    $(this).find('.mprogress-bar').hide();
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        '0%'
                    );
                    xhr.scene = null
                }
            });

            $('#fScene').submit(function () {
                return false;
            });

            $('#mCreateScene').on('hidden.bs.modal', function (e) {
                $(this).find('form')[0].reset();
                $(this).find('.btn-primary').html('Upload');
                $(this).find('.errors').html('');
                $(this).find('.btn-primary').prop('disabled', false);
                $(this).find('.btn-primary').show();
                $(this).find('.alert-success').hide();
                $(this).find('.files').html("");
                sceneTable.fnDraw();
                if (xhr.scene) xhr.scene.abort();
            });


            $('#fVideo').fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var acceptFileTypes = /^video\/(mp4)$/i;
                    var error = $(this).find(".errors");
                    if (data.files[0] && !acceptFileTypes.test(data.files[0].type)) {
                        error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                        error.find("ul").append('<li>Not an accepted file type</li>')
                        return;
                    }
                    if (data.files[0] && data.files[0].size > maxVideoSize) {
                        error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                        error.find("ul").append('<li>Max file size: ' + formatBytes(maxVideoSize) + '</li>');
                        return;
                    }
                    if (data.files && data.files[0]) {
                        var files = $(this).find('.files');
                        files.html('<p><i class="fa fa-video-camera"></i> ' + data.files[0].name + '</p>');
                    }
                    $(this).find('.btn-primary').removeAttr('disabled');
                    error.html("");
                    $(this).find('.btn-primary').off('click').on('click', function () {
                        var form = $(this).closest("form");
                        if (form.find('input[name="name"]').val() != "") {
                            form.find('.mprogress-bar').show();
                            $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i> Uploading');
                            $(this).prop('disabled', true);
                            xhr.video = data.submit();
                        }
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        progress + '%'
                    );
                    $(this).find('.mprogress-bar span').text(progress + '%');
                },
                done: function (e, data) {
                    $(this).find(".errors").html("");
                    $(this).find('.btn-primary').hide();
                    $(this).find('.alert-success').show();
                    $(this).find('.mprogress-bar').hide();
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        '0%'
                    );
                    xhr.video = null
                },
                fail: function (e, data) {
                    var error = $(this).find(".errors");
                    error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                    $.each(data.jqXHR.responseJSON.errors, function (key, value) {
                        error.find("ul").append('<li>' + value + '</li>')
                    });
                    $(this).find('.btn-primary').html('Upload');
                    $(this).find('.btn-primary').prop('disabled', false);
                    $(this).find('.mprogress-bar').hide();
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        '0%'
                    );
                    xhr.video = null
                }
            });

            $('#fVideo').submit(function () {
                return false;
            });
            $('#mCreateVideo').on('hidden.bs.modal', function (e) {
                $(this).find('form')[0].reset();
                $(this).find('.errors').html('');
                $(this).find('.btn-primary').html('Upload');
                $(this).find('.btn-primary').prop('disabled', false);
                $(this).find('.btn-primary').show();
                $(this).find('.alert-success').hide();
                $(this).find('.files').html("");
                videoTable.fnDraw();
                if (xhr.scene) xhr.scene.abort();
            });

            $('#fIcon').fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var acceptFileTypes = /^image\/(png|jpe?g|gif)$/i;
                    var error = $(this).find(".errors");
                    if (data.files[0] && !acceptFileTypes.test(data.files[0].type)) {
                        error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                        error.find("ul").append('<li>Not an accepted file type</li>')
                        return;
                    }
                    if (data.files && data.files[0]) {
                        var files = $(this).find('.files');
                        files.html('<p><i class="fa fa-file"></i> ' + data.files[0].name + '</p>');
                    }
                    $(this).find('.btn-primary').removeAttr('disabled');
                    error.html("");
                    $(this).find('.btn-primary').off('click').on('click', function () {
                        var form = $(this).closest("form");
                        if (form.find('input[name="name"]').val() != "") {
                            form.find('.mprogress-bar').show();
                            $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i> Uploading');
                            $(this).prop('disabled', true);
                            xhr.icon = data.submit();
                        }
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        progress + '%'
                    );
                    $(this).find('.mprogress-bar span').text(progress + '%');
                },
                done: function (e, data) {
                    $(this).find(".errors").html("");
                    $(this).find('.btn-primary').hide();
                    $(this).find('.alert-success').show();
                    $(this).find('.mprogress-bar').hide();
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        '0%'
                    );
                    xhr.icon = null
                },
                fail: function (e, data) {
                    var error = $(this).find(".errors");
                    error.html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                    $.each(data.jqXHR.responseJSON.errors, function (key, value) {
                        error.find("ul").append('<li>' + value + '</li>')
                    });
                    $(this).find('.btn-primary').html('Upload');
                    $(this).find('.btn-primary').prop('disabled', false);
                    $(this).find('.mprogress-bar').hide();
                    $(this).find('.mprogress-bar .bar').css(
                        'width',
                        '0%'
                    );
                    xhr.icon = null
                }
            });

            $('#fIcon').submit(function () {
                return false;
            });
            $('#mCreateIcon').on('hidden.bs.modal', function (e) {
                $(this).find('form')[0].reset();
                $(this).find('.errors').html('');
                $(this).find('.btn-primary').html('Upload');
                $(this).find('.btn-primary').prop('disabled', false);
                $(this).find('.btn-primary').show();
                $(this).find('.alert-success').hide();
                $(this).find('.files').html("");
                iconTable.fnDraw();
                if (xhr.scene) xhr.scene.abort();
                loadIcon();
            })

        });
    </script>
@endsection
@section('content')
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-12">
        @include('admin._partials._alert')
            <!-- General Information -->
            @include('admin.panorama._general')

        </div>
        <div class="col-md-4">
            <!-- Scene Collection -->
            @include('admin.panorama._scene')
        </div>
        <div class="col-md-4">
            <!-- Video Collection -->
            @include('admin.panorama._video')
        </div>
        <div class="col-md-4">
            <!-- Icon Collection -->
            @include('admin.panorama._icon')
        </div>
        <div class="col-md-12">
            <div class="ibox">
                <div class="ibox-content">
                    <span class="text-muted small pull-right">Last modification: <i
                                class="fa fa-clock-o"></i> {{$pano->updated_at}}</span>
                    <h2>PREVIEW</h2>
                    <div>
                        @if(isset($main_scene))
                            <div id="pano" style="width:100%;height:650px;position:relative"
                                 data-swf="{{asset('plugins/krpano/krpano.swf')}}"
                                 data-xml="{{$pano->xml_builder_path}}">
                                <div id="main-menu">
                                    <ul>
                                        <li class="title">DHD TOOL</li>
                                        <li><a href="#" id="polyHSButton" data-status="start">Add Polygon Hotspot</a></li>
                                        <li><a href="#" onclick="stopDraw();openModelIcons();return false;">Add Hotspot</a></li>
                                        <li><a href="#" onclick="stopDraw();openModelVideos();return false;">Add Video</a></li>
                                        <li><a href="#" onclick="stopDraw();setLimitZoom();return false;">Set Limit Zoom</a></li>
                                        <li><a href="#" onclick="stopDraw();setDefaultView(); return false;">Set Default View</a></li>
                                        <li><a class="btn-warning" href="#" onclick="stopDraw();updateXML();return false;"> Save</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="m-dialog"></div>
                                <div id="notify" class="notify" style="display: none"></div>
                                <div class="dialog-overlay" style="display: none"></div>
                                <div id="setting-box" class="setting-box">
                                    <div class="general"></div>
                                </div>
                                <div id="list-box" class="setting-box"></div>
                            </div>
                        @else
                            <div id="pano" style="width:100%;height:600px;position:relative;background-color: #ccc">
                                <div style="line-height:600px;text-align:center;font-size:40px;color:#333">CHOOSE A
                                    SCENE TO START
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection