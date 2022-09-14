<div class="form-group row">
    <label for="{{ $data['id'] }}" class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-9">
        <div img-upload name="{{ $data['name'] }}" type="image" value='<?php echo json_encode($data['value']); ?>' <?php echo $data['rules']; ?> imgreq="false" dir="{{ $data['dir'] }}"></div>
    </div>
</div>

<style>
    .previewControls {
        position: absolute;
        top: 0;
        right: 15px;
    }
</style>