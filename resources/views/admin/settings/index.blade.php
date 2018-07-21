@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function() {
        });
    </script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            @include('admin._partials._alert')
            <form role="form" method="POST" action="{{route('admin.settings.store')}}">
                {{ csrf_field() }}
                <div class="ibox-content">
                    @foreach($data as $groupName => $groups)
                        <h3>{{$groupName}}</h3>
                        <div class="hr-line-dashed"></div>
                        <div style="padding-left: 40px; margin-bottom: 40px">
                            @foreach($groups as $item)
                                @if($item->type == 'textbox')
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">{{$item['title']}}</label>
                                            <div class="col-sm-5">
                                                <input type="textbox" name="{{$item['key']}}" placeholder="{{$item['title']}}" class="form-control m-b" value="{{$item['value']}}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection