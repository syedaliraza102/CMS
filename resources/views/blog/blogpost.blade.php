@extends('layouts.app')

@section('content')
<?php //dd($data);  ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"> <h1> {{ $data['title']  }} </h1> </div>
                <div class="card-body">
                        @if(!empty($data['image_url']))
                        @foreach($data['image_url'] as $key2 => $value2)
                <img style="width: 100%" src="{{ $value2 }}" alt="{{ $data['title'] }} {{ $key2 }}">
                        @endforeach
                        @else
                        <img style="width: 100%" src="{{ $data['thumb_url'] }}" alt="{{ $data['title'] }}">
                        @endif
                @if(!empty($data['tags']))
                <div>
                        @foreach($data['tags'] as $key2 => $value2)
                        <a class="btn btn-sm btn-primary text-white m-1" href="{{ route('blog.tags',['tag' => str_slug($value2)])  }}" >   {{ $value2 }} </a>
                        @endforeach
                </div>
                @endif
                    {!! $data['description']  !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
                @include('blog.blogsidebar')
            </div>
    </div>
</div>
@endsection
