<?php
$data['value'] = is_array($data['value']) ? $data['value'] : [];
?>
<div class="form-group row">
    <label for="{{ $data['id'] }}" class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        @foreach($data['options'] as $key => $value)
        {{-- <div class="checkbox">
            <label>
                <input type="checkbox" {!! $data['rules'] !!} required class="{{ $data['class'] }}" @if(in_array($key,$data['value'])) checked="checked" @endif name="{{ $data['name'] }}[]" id="{{ $data['id'].'_'.$key }}" value="{{ $key }}">
                {{ $value }}
            </label>
        </div> --}}
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input {{ $data['class'] }}" {!! $data['rules'] !!} @if(in_array($key,$data['value'])) checked="checked" @endif name="{{ $data['name'] }}[]" id="{{ $data['id'].'_'.$key }}" value="{{ $key }}">
            <label class="custom-control-label" for="{{ $data['id'].'_'.$key }}">{{ $value }}</label>
        </div>
        @endforeach
        <div class="row">
            <div class="col-md-12">
                <label id="{{ $data['name'] }}[]-error " class="error" for="{{ $data['name'] }}[]"></label>
            </div>
        </div>
    </div>
</div>
{{-- <div class="form-group row">
<div class="custom-control custom-checkbox custom-control-inline">
    <input type="checkbox" class="custom-control-input" id="customCheck3">
    <label class="custom-control-label" for="customCheck3">Check this custom checkbox</label>
</div>
</div> --}}