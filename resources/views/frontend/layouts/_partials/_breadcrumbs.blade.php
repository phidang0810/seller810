@if(isset($breadcrumbs))
    <ol class="breadcrumb">
        <li>
            <a href="javascript:;">Trang Chủ</a>
        </li>
        @foreach($breadcrumbs as $item)
            @if (isset($item['link']))
                <li><a href="{{$item['link']}}">{{$item['name']}}</a></li>
            @else
                <li class="active">{{$item['name']}}</li>
            @endif
        @endforeach
    </ol>
@endif