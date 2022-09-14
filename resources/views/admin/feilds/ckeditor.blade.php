<div class="form-group row">
    <label for="{{ $data['id'] }}"
        class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">
        <span> {{ $data['helper'] }} </span>
            <textarea class="form-control {{ $data['class'] }}" name="{{ $data['name'] }}" {!! $data['rules'] !!}
            id="{{ $data['id'] }}" rows="5" placeholder="{{ $data['placeholder'] }}">{{ $data['value'] }}</textarea>
    </div>
</div>
<script>
    $(function () {

    CKEDITOR.replace('{{ $data['id'] }}')
    });
</script>
