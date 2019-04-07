@extends('admin.layouts.inspinia.master')

@section('title', $title)

@section('js')
<!-- Page-Level Scripts -->
<script>
    $(document).ready(function() {
        $("#bt-reset").click(function(){
            $("#mainForm")[0].reset();
        })

        $("#mainForm").validate();
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins pl-15 pr-15">
            @include('admin._partials._alert')
            <form role="form" method="POST" id="mainForm" action="{{route('admin.categories.store')}}"
            enctype="multipart/form-data">
                {{ csrf_field() }}
                @if (isset($data->id))
                <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Tên danh mục (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-5">
                                        <input type="text" name="name" placeholder="" class="form-control required m-b" value="@if(isset($data->name)){{$data->name}}@else{{old('name')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Danh mục cha</label>
                                    <div class="col-md-5">
                                        <select class="form-control m-b" name="parent_id">
                                            <option value="0">Chọn danh mục cha</option>
                                            {!! $categoriesTree !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mã danh mục (<span class="text-danger">*</span>)</label>
                                    <div class="col-md-5">
                                        <input type="text" name="code" placeholder="" class="form-control required m-b" value="@if(isset($data->code)){{$data->code}}@else{{old('code')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mô tả</label>
                                    <div class="col-md-5">
                                        <textarea name="description" id="" cols="30" rows="10"  class="form-control m-b">@if(isset($data->description)){{$data->description}}@else{{old('description')}}@endif</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Thứ tự</label>
                                    <div class="col-md-5">
                                        <input type="text" name="order" placeholder="" class="form-control m-b" value="@if(isset($data->order)){{$data->order}}@else{{old('order')}}@endif"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Hiển Thị Trang Chủ</label>
                                    <div class="col-md-3">
                                        <select class="form-control m-b" name="is_home">
                                            <option @if(isset($data->is_home) && $data->is_home === ACTIVE || old('is_home') === ACTIVE) selected @endif value="{{ACTIVE}}">Có</option>
                                            <option @if(isset($data->is_home) && $data->is_home === INACTIVE || old('is_home') === INACTIVE) selected @endif value="{{INACTIVE}}">Không</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Trạng thái</label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="active">
                                            <option @if(isset($data->active) && $data->active === ACTIVE || old('active') === ACTIVE) selected @endif value="{{ACTIVE}}">Đã kích hoạt</option>
                                            <option @if(isset($data->active) && $data->active === INACTIVE || old('active') === INACTIVE) selected @endif value="{{INACTIVE}}">Chưa kích hoạt</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- BEGIN: Product photo -->
                                                <div class="c-product-photo">
                                                    @if(!isset($data->photo) || empty($data->photo) || $data->photo === '' )
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100%;">
                                                            <img style="" data-src="holder.js/100%x100%" alt="..." src="data:image/png;base64,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">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%;"></div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100%;">
                                                            <img style="" data-src="holder.js/100%x100%" alt="..." src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAyAAAAMgBAMAAAApXhtbAAAAG1BMVEXMzMyWlpbFxcWxsbGjo6OcnJy3t7e+vr6qqqrLdpw6AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAITElEQVR4nO3dzXMbSRkHYK1sST6uIAaOFkXgiii4R1nCOSIUcIwpKPa4CgXF0a5id/9tMpqvluY32VjJbZ7n5LSUfivvtN/u6fnIbAYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMCo+W9f3f7+N2eN/321/uvbs7bV//a3332+ELG7FHdilvt15c8njf+omm5/Hb745nOFiN2luBOz2q1rfykaf1E33d4VbfPmi3/8PCFidynu1BzWrbdd23y/Hmbr+frSbKUQsbsYd2KWXbLWP+4a22SV2eqS9eRsxRCxuxR3avrRW4zfXdf0ovviddf27HOEiN2luFOzL7L1o6atGNI/6b54H7J6eYjYXYw7MTfVv/67n8//tS/G6qb6+Z/zv1ef3TVt8+oPf/jml7syq5eHiN2luFNT5eBl9cNxEfpN3bhrhm21Bn1ovnjdDNvqez/79BCxuxR3arZdPp53WahG70+rH1bF8N20yTw8dfimELG7GHdq9t38WS17vjz+dNXV9ft+XbRt63pV6J904pZCxO5i3ImZ90WkysJxfM4WXal/X1hum0/7tL4vLK8/NUTsLsadmGWx6ly0w7JP26pL5rJP66H7+PIQsbsUd2puitpw1WbusT8N2LVF/6ZP6/XTZvUYInaX4k7NdTHaV22d2PenBvdtPVn0pwbLp9WTGCJ2l+JOzaJYzsybzFRFv23btJ9vuun4+Pndp4WI3cW4U7PoEzObNQkpi/51O5Lvi92M3ZPO1VOI2F2MOzVfFNlqR+hNkYyrtsA/Fgdhe17gq22QF/3fOJ3zU4jYXYw7NameLEbmlW7Zczgv8NfFQVicn2XHkpW6i3GnpkxCWzLikC7qezkB1KpVapu/7fkMk0LE7mLcqSmXsO1SdFP+Auzr9M7LEbsYzLiP3Tl2lcnTk+wUInaX4k7OTZGZTZPKQ1lzdnVtWZU1/XpwZljtTL1uOzyrZylE7C7FnZxyY2rbJOa+TEwz+y7LDN4MlkBX3RWMzfp8CZZCxO5S3OlZn2xXHMf2ttw8bP5wVRaiq+HW3779NXgcXlAMIWJ3Ke70PHbXrw/t8uhkcDbD9uSXYjlck943vwar80VvDhG7S3Gn59AWm6vuMt1J+T7UZxgn08Zq+FuwaK5gXIdLSyFE7C7FnZ7j9dU37+rrq/Wo3ZULnP6A9AurcEBWzeIqXb0KIWJ3Ke4E9Td6tCk4WXFu6taTle483Hiyq4/EPl1ZGoaI3aW4E3TTJ6uZGM4TU03D5xkcnEZvjrWqWlEN79oahojdpbhT1N+P81A3XHRAbo6TyGKw6M0hHJAP6LP1sm7Yl7sWX3QHpNgtCQdkfhz+27wFNQgRu0txJ6ioJ83idH2WmC9np/tMZ99ovD8W63f7eHV3GCJ2l+JO0GMx49bl/7IDUlWrr9ZxKh6GcEBGFaM37cR+/AFpbwO9+5gQDsioY3n/6t38P8e16duq6aSWb8Ickib1dnEbLiuFELG7FHd63mehfozm+FDN66bprvv8Y1dZ7T3u4Up4CGGVNWbZVZFjZTmeR194QOrKNNwRTCEckDHXxTT8+IEz5h88U2/uZw8HKoVwpj5mUzys9LyZki/ZXKxs1/FWkRTC5uKY+yKHy2bKvfSAVJNIWBmlEA7ImMeyVK/rrY1LrodUqik73LOeQrgeMubx7LLpi9llVwxn9Q58Og1JIVwxHLM7G5UvZhddU68sjgfkYdCeQrimPuZkZXOoi8tJ+e7vOumnjeFdJ5X7kfOQFCJ2l+JOTlr7X3BfVvPNdZrtUwj3ZY35oX2rj7tzsXLVbFYNxnXcokrduXNxNsxWc1b+xHt7K9Ulw3+vwycphHt7x6R6cvbIU3f3e7fq2aa5uzoJX6ZTw7gjkrqLcaemHKntdsVFz4fUjzTvwsBOITwfMmZXLvebZc5FT1DVO1aHsL2YQniCaszJWVt7Mra/4BnDw3E+vw4L3xjCM4Yj7ssEtmv/xwuewt0fG1fhElUM4SncEZtiIu1KxiE8L148OX4I54XL5lfjcVjOYojYXYo7NYsigd2oveBNDu2DbJv1YJc2hvAmhxFXRQIPbV2/4F0n2ybrN+vBr08MEbvzrpO6hjy7O/7YPbxxwduA5u3cUf1wO/xsEMLbgMZURb9+e+u2H6rvG2/fzur9wofmizfNF5dh3m5uJG17efsRIWJ3Ke7UHF87+ebd7FffFmO7Gr7P/pbeKHeX3yi36TK9GO6exBCxuxR3albrQlv9n/zOxV2XweqvnpX/GMI7F8dsi2w9tI27rulF98UPvJW0fVynsh+O7hjCW0lHXPXJ6qeGJ763t32grRLqfwzhvb1j+vH70LU98c3W1UFoV8LlazY+FMKbrcd0L2YvX5XfvIO9Wa7W2pe1vxx0sS/WuqtQ02KI2F2KOznLOjOn/03B746D9/QE8Go//N4nhIjdpbiTM//61fpP3581fr0f/j8ey2/Xl/7/ISlE7C7FBQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgMv8H4Jxvf8xwCA2AAAAAElFTkSuQmCC">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%;">
                                                            <img data-src="holder.js/100%x100%" alt="{{$data->name}}" src="{{asset('storage/' .$data->photo)}}" data-holder-rendered="true">
                                                        </div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="photo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <!-- END: Product photo -->
                            <h5>Danh mục sản phẩm</h5>
                            <div class="list-tree-section m-b">
                                {!! $categoriesTreeList !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="text-right">
                                <a href="{{route('admin.categories.index')}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Trở lại</a>
                                <button type="button" class="btn btn-default" id="bt-reset"><i class="fa fa-refresh"></i> Làm mới</button>
                                <button type="submit" name="action" class="btn btn-primary" value="save"><i class="fa fa-save"></i> Lưu</button>
                                <button type="submit" name="action" class="btn btn-warning" value="save_quit"><i class="fa fa-save"></i> Lưu &amp; Thoát</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection