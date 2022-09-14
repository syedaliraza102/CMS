<div class="form-group row">
    <label for="{{ $data['id'] }}"
        class="col-sm-{{ $data['titlewidth'] }} control-label text-right">{{ $data['title'] }}</label>
    <div class="col-sm-{{ $data['feildwidth'] }}">

                <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" {!! $data['rules'] !!} class="form-control pull-right {{ $data['class'] }}" name="{{ $data['name'] }}"
                    id="{{ $data['id'] }}" value="{{ $data['value'] }}" placeholder="Select {{ $data['title'] }}">
                  </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#{{ $data['id'] }}').daterangepicker();
    });
</script>
