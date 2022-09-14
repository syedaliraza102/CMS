@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $data['title']  }}</div>
                <div class="card-body">
                    {!! $data['description']  !!}
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                    <div class="card-header">Post</div>
                    <div class="card-body">
                        @empty($data['post'])
                            <h3> No Post Found </h3>
                        @else
                            @foreach($data['post'] as $key => $value)
                            @include('blog.blog_row',['value' => $value])
                            @endforeach
                        @endempty
                    </div>
                </div>
        </div>
        <div class="col-md-4">
                @include('blog.blogsidebar')
        </div>
    </div>
</div>
@endsection
