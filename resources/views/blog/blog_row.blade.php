<div class="row">
        <div class="col-md-3">
        <a class="m-2" href="{{ route('blog.detail',['slug' => $value['slug'] ]) }}">
        <img src="{{ url($value['thumb']) }}" style="width: 100%" alt="{{ $value['title'] }}">
        </a>
        </div>
        <div class="col-md-9">
            <h3> <a class="m-2" href="{{ route('blog.detail',['slug' => $value['slug'] ]) }}"> {{ $value['title']  }} </a></h3>
            @if(!empty($value['tags']))
            <div>
                    @foreach($value['tags'] as $key2 => $value2)
                    <a class="btn btn-sm btn-primary text-white m-1" href="{{ route('blog.tags',['tag' => str_slug($value2)])  }}" >   {{ $value2 }} </a>
                    @endforeach
            </div>
            @endif
            {!!  \App\Common::show_more($value['description'],route('blog.detail',['slug' => $value['slug'] ]))  !!}
        </div>
    </div>
