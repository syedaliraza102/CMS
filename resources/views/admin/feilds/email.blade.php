<div class="form-group row">
        <label for="{{ $data['id'] }}"
            class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
        <div class="col-sm-{{ $data['feildwidth'] }}">
            <input type="email" class="form-control {{ $data['class'] }}" {!! $data['rules'] !!} name="{{ $data['name'] }}"
                id="{{ $data['id'] }}" value="{{ $data['value'] }}" placeholder="{{ $data['placeholder'] }}" required>
        </div>
    </div>
