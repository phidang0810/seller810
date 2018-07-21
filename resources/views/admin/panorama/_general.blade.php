<!-- Information -->
<div class="ibox">
    <div class="ibox-title">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" style="float:left;margin-top: -10px;border: none;">
            <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab"
                                                      data-toggle="tab">General Information</a></li>
            <li role="presentation"><a href="#social" aria-controls="social" role="tab" data-toggle="tab">Social
                    Config</a></li>
        </ul>

        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>


    <!-- Tab panes -->
    <div class="ibox-content">
        <form id="fOption" action="{{route('admin.panorama.update')}}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="tab-content">
                <!-- General Tab -->

                <div role="tabpanel" class="tab-pane active" id="general">
                    <input type="hidden" name="id" id="panoramaId" value="{{$pano->id}}"/>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Name (<span class="text-danger">*</span>)</label>
                                <input type="text" name="name" placeholder="Enter name"
                                       class="form-control m-b" value="{{$pano->name}}"/>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>URL Alias (<span class="text-danger">*</span>)</label>
                                <input type="text" class="form-control m-b" name="url" value="{{$pano->url}}"/>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Main Scene <a href="#" data-toggle="tooltip" data-placement="top"
                                                     title="Only show ready scenes"><i
                                                class="fa fa-question-circle"></i></a></label>
                                <select class="form-control m-b" name="main_scene" id="mainScene">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Theme (<span class="text-danger">*</span>)</label>
                                <select class="form-control" name="theme">
                                    @foreach($themes as $theme)
                                        <option @if($pano->theme_id == $theme->id) selected
                                                @endif value="{{$theme->id}}">{{$theme->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tag Icon(<span class="text-danger">*</span>)</label>
                                <select class="form-control" name="icon" id="icon-select">
                                <option value="0">-- None --</option>
                                @foreach($icons as $icon)
                                    <option @if($icon->is_tag) selected
                                            @endif value="{{$icon->id}}">{{$icon->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    @foreach($status as $key => $value)
                                        <option @if($pano->status == $key) selected
                                                @endif value="{{$key}}">{{$value['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Logo</label>
                                @if(isset($pano->logo))
                                    <div style="margin-bottom: 10px">
                                        <img src="{{$pano->logo}}" width="100px" style="border: 1px solid #ccc;padding: 2px;" />
                                    </div>
                                @endif
                                <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg" class="form-control m-b"/>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control m-b" rows="5" name="description">@if(isset($pano->description)){{$pano->description}}@else{{old('description')}}@endif</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Tab -->
                <div role="tabpanel" class="tab-pane" id="social">
                    @foreach($groupSocial as $groupName => $group)
                        <div class="panel panel-default">
                            <div class="panel-heading">{{$groupName}}</div>
                            <div class="panel-body">
                                @foreach($group as $item)
                                    <div class="row v-center">
                                        <div class="col-md-2">
                                            {{$item['title']}}
                                        </div>
                                        @if($item['group'] === SOCIAL_TAG)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>App Key</label>
                                                <input type="text" name="social[{{$item['key']}}][app_id]" placeholder="Enter application key" class="form-control m-b" value="{{$item['app_id']}}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Secret Key</label>
                                                <input type="text" class="form-control m-b" name="social[{{$item['key']}}][app_secret]" placeholder="Enter application secret key" value="{{$item['app_secret']}}"/>
                                            </div>
                                        </div>
                                        @endif

                                        @if($item['group'] === SOCIAL_PAGE)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Page URL</label>
                                                    <input type="text" class="form-control m-b" name="social[{{$item['key']}}][url]" placeholder="Enter url" value="{{$item['url']}}"/>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <input type="checkbox" name="social[{{$item['key']}}][active]" class="js-switch" value="{{$item['active']}}" @if($item['active']) checked @endif/>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>

                    <div class="hr-line-dashed"></div>
                    <div class="text-right" style="margin-top: 10px">
                        <a href="{{route('admin.panorama.index')}}" class="btn btn-default"><i
                                    class="fa fa-arrow-left"></i> Back To List</a>
                        @if($pano->status == PANORAMA_PUBLIC)
                            <a href="{{route('panorama.detail', [$pano->url])}}" target="_blank"
                               class="btn btn-default"><i class="fa fa-eye"></i> Preview </a>
                        @endif
                        <button type="button" class="btn btn-default" id="refresh"><i
                                    class="fa fa-refresh"></i> Scene
                        </button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Apply
                        </button>
                    </div>
            </div>
        </form>
    </div>

</div>