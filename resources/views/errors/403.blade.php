@extends('admin.layouts.inspinia.error')

@section('title', 'Page Not Found')

@section('content')
<div class="middle-box text-center animated fadeInDown">
    <h1>403</h1>
    <h3 class="font-bold">Permission Denied</h3>

    <div class="error-desc">
        You don't have permission for this
        <form class="form-inline m-t" role="form">
            <a href="@if(isset($redirect)) {{$redirect}} @else {{route('home')}} @endif" class="btn btn-primary">Go go Home Page</a>
        </form>
    </div>
</div>
@endsection