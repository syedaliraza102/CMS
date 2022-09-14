
<div class="form-group row">
    <label for="{{ $data['id'] }}"
        class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-9">
        <div img-upload method="POST" type="multipleimage" name="{{ $data['name'] }}" dir="{{ $data['dir'] }}" value='{!! json_encode($data['value']) !!}' multiple="multiple" url="submit.php"></div>
    </div>
</div>
