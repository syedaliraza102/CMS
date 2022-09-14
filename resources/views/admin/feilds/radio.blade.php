<div class="form-group row">
    <label for="{{ $data['id'] }}" class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        @foreach($data['options'] as $key => $value)
        <div class="radio-inline">
            <label>
                <input type="radio" {!! $data['rules'] !!} class="{{ $data['class'] }}" @if($key==$data['value']) checked="checked" @endif name="{{ $data['name'] }}" id="{{ $data['id'].'_'.$key }}" value="{{ $key }}">
                {{ $value }}
            </label>
        </div>
        @endforeach
        <div class="row">
            <div class="col-md-12">
                <label id="{{ $data['name'] }}[]-error " class="error" for="{{ $data['name'] }}[]"></label>
            </div>
        </div>
    </div>
</div>