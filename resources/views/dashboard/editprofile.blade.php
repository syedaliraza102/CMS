@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('dashboard.updateprofile') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field()  }}
                <div class="card">
                    <div class="card-header">Edit Profile</div>
                    <div class="card-body">
                        <div class=" form-group row">
                            <div class="col-md-3"> First Name </div>
                            <div class="col-md-9">
                                <input id="firstname" type="text"
                                    class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}"
                                    name="firstname" value="{{ old('firstname') ?? $data['firstname'] }}" required
                                    autofocus>

                                @if ($errors->has('firstname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('firstname') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class=" form-group row">
                            <div class="col-md-3"> Last Name </div>
                            <div class="col-md-9">
                                <input id="lastname" type="text"
                                    class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}"
                                    name="lastname" value="{{ old('lastname') ?? $data['lastname'] }}" required
                                    autofocus>
                                @if ($errors->has('lastname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('lastname') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class=" form-group row">
                            <div class="col-md-3"> Email </div>
                            <div class="col-md-9">
                                {{ \Auth::user()->email }}
                            </div>
                        </div>
                        <div class=" form-group row">
                            <div class="col-md-3"> avatar </div>
                            <div class="col-md-9">
                                <input id="avatar" type="file" class="{{ $errors->has('avatar') ? ' is-invalid' : '' }}"
                                    name="avatar" value="{{ old('avatar') }}">
                                @if ($errors->has('avatar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('avatar') }}</strong>
                                </span>
                                @endif
                                <div class="clearfix"></div>
                                <img id="avatar_img" style="max-width: 200px;margin-top: 40px"
                                    src="{{ $data['avatar_url'] }}">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-success" type="submit"> Submit </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#avatar').change(function () {
            var input = this;
            var url = $(this).val();
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
            if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#avatar_img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

    });
</script>
@endsection
