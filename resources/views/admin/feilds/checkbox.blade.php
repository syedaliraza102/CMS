<?php
$data['value'] = is_array($data['value']) ? $data['value'] : [];
?>
<div class="form-group row">
    <label for="{{ $data['id'] }}" class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        <div class="row ml-0">


        @foreach($data['options'] as $key => $value)
        
        <div class="custom-control custom-checkbox custom-control-inline col-md-4">
            <input type="checkbox" class="custom-control-input {{ $data['class'] }}" {!! $data['rules'] !!} @if(in_array($key,$data['value'])) checked="checked" @endif name="{{ $data['name'] }}[]" id="{{ $data['id'].'_'.$key }}" value="{{ $key }}">
            <label class="custom-control-label" for="{{ $data['id'].'_'.$key }}">{{ $value }}</label>
        </div>
        @endforeach
    </div>
        <div class="row">
            <div class="col-md-12">
                <label id="{{ $data['name'] }}[]-error " class="error" for="{{ $data['name'] }}[]"></label>
            </div>
        </div>
    </div>
</div>
