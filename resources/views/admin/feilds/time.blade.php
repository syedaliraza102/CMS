<?php $data['value'] = !empty($data['value']) ? date('H:i', strtotime($data['value'])) : ''  ?>
<div class="form-group row">
    <label for="{{ $data['id'] }}"
        class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">

        <div class="input-group">
            <input type="text" class="form-control timepicker {{ $data['class'] }}" {!! $data['rules'] !!} name="{{ $data['name'] }}"
            id="{{ $data['id'] }}" value="{{ $data['value'] }}" placeholder="Select {{ $data['title'] }}">
            <div class="input-group-addon">
              <i class="fa fa-clock-o"></i>
            </div>
          </div>
    </div>
</div>
<script>
    $(function () {

        $('#{{ $data['id'] }}').timepicker({
      showInputs: false,
      showMeridian : false
    })
    });
</script>
