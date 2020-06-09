@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1>{{ $poster->title }}</h1>
            <div name="content">{!! $poster->content !!}</div>
            <style>#cke_1_top, #cke_1_bottom{display:none}</style>
            <a href="/" class="btn btn-secondary">< Back</a>
        </div>
    </div>
</div>
@endsection
