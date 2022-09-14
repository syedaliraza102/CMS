@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $data['title']  }}</div>
                <div class="card-body">
                    @empty($data['posts']['data'])
                        <h3> No Post Found </h3>
                    @else
                        @foreach($data['posts']['data'] as $key => $value)
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
