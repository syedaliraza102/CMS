

  <uib-tabset active="active">
        @if(!empty($data['tabs']))
        @foreach ($data['tabs'] as $key => $value)
  <uib-tab index="{{ $key }}" heading="{{ $value['title'] }}"> {!! $value['fields_str']  !!} </uib-tab>
        @endforeach
        @endif
      </uib-tabset>

