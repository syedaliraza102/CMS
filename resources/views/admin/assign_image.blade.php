<div class="form-group row ng-scope">
    <label for="lesson_topic_text_id" class="col-sm-2 control-label text-right">Image</label>
    <div class="col-sm-8">
        @if(!empty($data['value']))

        <img src="{{ asset('/public/'.$data['value'])  }}" style="max-width: 250px;" alt="" srcset="">
        @else
        -
        @endif
    </div>
</div>