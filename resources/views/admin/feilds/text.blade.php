<div class="form-group row">
    <label for="{{ $data['id'] }}"
        class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        <span> {{ $data['helper'] }} </span>
        <input type="{{ $data['type'] }}" class="form-control {{ $data['class'] }}" {!! $data['rules'] !!} name="{{ $data['name'] }}"
            id="{{ $data['id'] }}" value="{{ $data['value'] }}" placeholder="{{ $data['placeholder'] }}">
    </div>
</div>
