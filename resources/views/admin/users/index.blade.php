@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        var url_delete = "{{route('admin.users.delete')}}";
        var table;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            table = $('#dataTables').dataTable({
                searching: false,
                processing: true,
                serverSide: true,
                "dom": '<"top"i>rt<"bottom"flp><"clear">',
                ajax: {
                    "url": "{{route('admin.users.index')}}",
                    "data": function ( d ) {
                        d.keyword = $('#s-keyword').val();
                        d.role = $('#s-role').val();
                        d.status = $('#s-status').val();
                    }
                },
                columns: [
                    {data: 'id'},
                    {data: 'email'},
                    {data: 'full_name'},
                    {data: 'role'},
                    {data: 'created_at'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                "aoColumnDefs": [
                    // Column index begins at 0
                    { "sClass": "text-center", "aTargets": [ 5 ] },
                    { "sClass": "text-right", "aTargets": [ 6 ] }
                ]

            });

            $('#fSearch').submit(function(){
                table.fnDraw();
                return false;
            });

            $('#bt-reset').click(function(){
                $('#fSearch')[0].reset();
                table.fnDraw();
            });
        });

        $("#dataTables").on("click", '.bt-delete', function(){
            var email = $(this).attr('data-email');
            var data = {
                ids: [$(this).attr('data-id')]
            };
            swal({
                    title: "Warning!",
                    text: "Are you sure you want to delete <b>"+email+"</b> ?",
                    html:true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    $.ajax({
                        url: url_delete,
                        type: 'DELETE',
                        data: data,
                        dataType:'json',
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: "Successfully!",
                                    text: "The account " + email + " file has been deleted.",
                                    html: true,
                                    type: "success",
                                    confirmButtonClass: "btn-primary"
                                });
                            } else {
                                errorHtml = '<ul class="text-left">';
                                $.each( response.errors, function( key, error ) {
                                    errorHtml += '<li>' + error + '</li>';
                                });
                                errorHtml += '</ul>';
                                swal({
                                    title: "Error! Refresh page and try again.",
                                    text: errorHtml,
                                    html: true,
                                    type: "error",
                                    confirmButtonClass: "btn-danger",
                                });
                            }
                            table.fnDraw();
                        }
                    });

                });
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
                                    <label>Email, name</label>
                                    <input type="text" placeholder="Enter email, name" name="keyword" id="s-keyword" class="form-control" value="{{app('request')->input('keyword')}}">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Select role</label>
                                    <select class="form-control" name="role" id="s-role">
                                        <option value=""> -- All -- </option>
                                        @foreach($roles as $role)
                                            <option @if(app('request')->input('role') == $role->id) selected @endif value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Select status</label>
                                    <select class="form-control" name="status" id="s-status">
                                        <option value=""> -- All -- </option>
                                        <option @if(app('request')->has('status') && app('request')->input('status') == ACTIVE) selected @endif value="{{ACTIVE}}">Active</option>
                                        <option @if(app('request')->has('status') && app('request')->input('status') == INACTIVE) selected @endif value="{{INACTIVE}}">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-default" type="button" id="bt-reset" style="margin-bottom: 0;margin-top: 22px; margin-right:5px">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <button class="btn btn-warning" type="submit" style="margin-bottom: 0;margin-top: 22px;">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                    </div>
                </form>
                <div class="hr-line-dashed"></div>
                <div class="text-right">
                    <a href="{{route('admin.users.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Account</a>
                </div>
                <!-- Account list -->
                <table class="table table-striped table-bordered table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Role</th>
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
@endsection