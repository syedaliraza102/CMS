<div class="form-group row">
    <label for="{{ $data['id'] }}"
        class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-9">

        <div img-upload name="{{ $data['name'] }}" type="file" value='{!! json_encode($data['value']) !!}'   dir="{{ $data['dir'] }}"></div>
    </div>
</div>
