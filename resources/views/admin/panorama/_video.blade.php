<div class="ibox ">
    <div class="ibox-title">
        <h5>Video Collection</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="text-right">
            <button type="button" data-toggle="modal" data-target="#mCreateVideo" class="btn btn-warning btn-xs"><i
                        class="fa fa-upoad"></i> Upload Video
            </button>
        </div>
        <table class="table table-striped table-bordered table-hover" id="videoTable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Video</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="mCreateVideo" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('admin.video.store')}}" id="fVideo" method="post">
                <input type="hidden" name="panorama_id" value="{{$pano->id}}"/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Upload Video</h4>
                </div>
                <div class="modal-body">
                    <div class="errors">
                    </div>
                    <div class="alert alert-success alert-dismissable" style="display: none; margin-bottom: 20px">
                        The video has been uploaded
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Name (<span class="text-danger">*</span>)</label>
                            <div class="col-md-8">
                                <input type="text" name="name" required placeholder="Enter name"
                                       class="form-control m-b"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Video (<span class="text-danger">*</span>)</label>
                            <div class="col-md-8">
                                <label class="input-f btn btn-default">
                                    <input type="file" name="file" accept="video/mp4">
                                    <i class="fa fa-upload"></i> Select video
                                </label>
                                <small>(File type: mp4)</small>
                                <div class="files" style="margin-top: 10px">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mprogress-bar" style="display: none">
                        <div class="bar" style="width: 0%;"></div>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" disabled id="bt-upload"><i class="fa fa-upload"> </i>
                        Upload
                    </button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->