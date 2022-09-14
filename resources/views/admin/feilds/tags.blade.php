<?php
$data['value'] = is_array($data['value']) ? $data['value'] : [];
//dd($data['value']);
?>
<div class="form-group row">
    <label class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        <select class="form-control select2 {{ $data['class'] }}" data-tags="true" multiple="multiple" {!! $data['rules'] !!} data-placeholder="Select {{ $data['title'] }}" name="{{ $data['name'] }}[]" id="{{ $data['id'] }}">
            @foreach($data['options'] as $key => $value)
            <option @if(in_array($value,$data['value'])) selected="selected" @endif value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        <label id="{{ $data['id'] }}-error" class="error" for="{{ $data['id'] }}"></label>
    </div>
</div>
<script>
    $(function() {
        $('.select2').select2();
    });
</script>