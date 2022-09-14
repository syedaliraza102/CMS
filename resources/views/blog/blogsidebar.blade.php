<?php
$data = \App\Common::blog_sidebar();
//dd($data);
?>

{{-- <div class="card">
        <div class="card-header">Search Blog </div>
        <div class="card-body">
                <form class="form-inline">
                        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                      </form>
        </div>
    </div> --}}

@if(!empty($data['recent_post']))
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">Recent Post </div>
    <div class="card-body">
        <ul>
            @foreach($data['recent_post'] as $key => $value)
            <li>
            <a href="{{ route('blog.detail',['slug' => $value['slug']]) }}"> {{ $value['title'] }} </a>
            </li>
            @endforeach
         </ul>
    </div>
</div>
@endif


@if(!empty($data['blog_category']))
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">Blog Category </div>
    <div class="card-body">
        <ul>
            @foreach($data['blog_category'] as $key => $value)
            <li>
            <a href="{{ route('blog.detail',['slug' => $value['slug']]) }}"> {{ $value['title'] }}({{ $value['post_count'] }}) </a>
            </li>
            @endforeach
         </ul>
    </div>
</div>
@endif


@if(!empty($data['blog_tag_counts']))
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">Tags </div>
    <div class="card-body">
            @foreach($data['blog_tag_counts'] as $key => $value)
    <a class="btn btn-sm btn-primary text-white m-1" href="{{ route('blog.tags',['tag' => str_replace(' ','-',$key)])  }}" >  {{$key}} ({{ $value }}) </a>
            @endforeach
    </div>
</div>
@endif
