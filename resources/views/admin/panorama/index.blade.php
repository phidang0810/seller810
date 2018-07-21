@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        var url_delete = "{{route('admin.panorama.delete')}}";
        var url_search = "{{route('admin.panorama.index')}}";
        var url_create = "{{route('admin.panorama.create')}}";
        var table;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function () {
            table = $('#dataTables').dataTable({
                searching: false,
                processing: true,
                serverSide: true,
                "dom": '<"top"i>rt<"bottom"flp><"clear">',
                ajax: {
                    "url": url_search,
                    "data": function (d) {
                        d.keyword = $('#s-keyword').val();
                        d.status = $('#s-status').val();
                    }
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'created_at'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                aoColumnDefs: [
                    {"sClass": "text-center", "aTargets": [3]},
                    {"sClass": "text-right", "aTargets": [4]}
                ]

            });

            $('#fSearch').submit(function () {
                table.fnDraw();
                return false;
            });

            $('#bt-reset').click(function () {
                $('#fSearch')[0].reset();
                table.fnDraw();
            });
        });

        $("#dataTables").on("click", '.bt-delete', function () {
            var name = $(this).attr('data-name');
            var data = {
                id: $(this).attr('data-id')
            };
            swal({
                    title: "Warning!",
                    text: "Are you sure you want to delete <b>" + name + "</b> ?",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                },
                function () {
                    $.ajax({
                        url: url_delete,
                        type: 'DELETE',
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: "Successfully!",
                                    text: "<b>" + name + "</b> has been deleted.",
                                    html: true,
                                    type: "success",
                                    confirmButtonClass: "btn-primary"
                                });
                            } else {
                                errorHtml = '<ul class="text-left">';
                                $.each(response.errors, function (key, error) {
                                    errorHtml += '<li>' + error + '</li>';
                                });
                                errorHtml += '</ul>';
                                swal({
                                    title: "Error! Refresh page and try again.",
                                    text: errorHtml,
                                    html: true,
                                    type: "error",
                                    confirmButtonClass: "btn-danger"
                                });
                            }
                            table.fnDraw();
                        }
                    });

                });
        });

        $('#fCreate').submit(function () {
            var form = $(this);
            var data = form.serializeArray();
            $.ajax({
                url: url_create,
                data: data,
                method: form.attr('method'),
                dataType: 'json',
                beforeSend: function () {
                    $("#errors").html("");
                    form.find('button[type=submit]').prop('disabled', true);
                    form.find('button[type=submit]').html('<i class="fa fa-spinner fa-spin fa-fw"></i> Loading');
                },
                success: function (response) {
                    window.location.href = response.redirect;
                },
                error: function (jqXHR) {
                    $("#errors").html('<div class="alert alert-danger alert-dismissable"><ul></ul></div>');
                    $.each(jqXHR.responseJSON.errors, function (key, value) {
                        $("#errors ul").append('<li>' + value + '</li>')
                    })
                    form.find('button[type=submit]').prop('disabled', false);
                    form.find('button[type=submit]').html('Create Panorama');
                }
            });
            return false;
        });

        $('#mCreate').on('hidden.bs.modal', function (e) {
            $("#errors").html("");
            $('#fCreate')[0].reset();
        });
    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                @include('admin._partials._alert')
                <div class="ibox-content">

                    <!-- Search form -->
                    <form role="form" id="fSearch">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Search by name</label>
                                    <input type="text" placeholder="Name" name="keyword" id="s-keyword"
                                           class="form-control" value="{{app('request')->input('keyword')}}">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Select status</label>
                                    <select class="form-control" name="status" id="s-status">
                                        <option value=""> -- All --</option>
                                        @foreach($status as $key => $value)
                                            <option @if(app('request')->input('status') == $key) selected
                                                    @endif value="{{$key}}">{{$value['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-default" type="button" id="bt-reset"
                                        style="margin-bottom: 0;margin-top: 22px; margin-right:5px">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <button class="btn btn-warning" type="submit"
                                        style="margin-bottom: 0;margin-top: 22px;">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="hr-line-dashed"></div>
                    <div class="text-right">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#mCreate"><i
                                    class="fa fa-plus"></i> New Panorama</a>
                    </div>
                    <!-- Table list -->
                    <table class="table table-striped table-bordered table-hover" id="dataTables">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="mCreate">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="fCreate" action="{{route('admin.panorama.create')}}" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Create Panorama</h4>
                    </div>
                    <div class="modal-body">
                        <div id="errors">
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Panorama name (<span class="text-danger">*</span>)</label>
                                <div class="col-md-8">
                                    <input type="text" name="name" placeholder="Panorama name" class="form-control m-b"
                                           value=""/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Panorama</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection