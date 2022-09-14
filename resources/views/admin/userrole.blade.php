<?php $data['value'] = is_array($data['value']) ? $data['value'] : [] ?>
<div class="form-group row">
    @foreach($data['htmldata']['routes'] as $key => $value)
    <label for="{{ $data['id'] }}" class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $key }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        @foreach($value as $key2 => $value2)
        <div class="checkbox checkbox-inline">
            <label>
                <input type="checkbox" required class="{{ $data['class'] }}" @if(in_array($key2,$data['value'])) checked="checked" @endif name="{{ $data['name'] }}[]" id="{{ $data['id'].'_'.$key2 }}" value="{{ $key2 }}">
                {{ $value2 }}
            </label>
        </div>
        @endforeach
    </div>
    @endforeach
</div>