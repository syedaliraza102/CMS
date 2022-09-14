<div class="form-group row">
    <label for="{{ $data['id'] }}" class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        <select class="form-control select2 {{ $data['class'] }}" data-tags="true" {!! $data['rules'] !!} placeholder="Select a {{ $data['title'] }}" name="{{ $data['name'] }}" id="{{ $data['id'] }}">
            @foreach($data['options'] as $key => $value)
            <option @if($key==$data['value']) selected="selected" @endif value="{{ $key }}">{{ $value }}</option>
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