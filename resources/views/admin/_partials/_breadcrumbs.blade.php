@if(isset($breadcrumbs))
    <ol class="breadcrumb">
        <li>
            <a href="{{route('admin.dashboard')}}"><i class="fa fa-home"></i> Dashboard</a>
        </li>
        @foreach($breadcrumbs as $item)
            @if (isset($item['link']))
                <li><a href="{{$item['link']}}">{{$item['name']}}</a></li>
            @else
                <li class="active"><strong>{{$item['name']}}</strong></li>
            @endif
        @endforeach
    </ol>
@endif