@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('dashboard.submit_account_settings') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field()  }}
                <div class="card">
                    <div class="card-header">Account Settings</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="password"
                                class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-9">
                                <input id="password" type="password"
                                    class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    name="password">
                                <span> Leave this field empty if you don't want to
                                    change password </span>
                                @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm"
                                class="col-md-3 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-9">
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation">
                            </div>
                        </div>
                        <div class=" form-group row">
                            <div class="col-md-3 text-md-right"> is_subscribed </div>
                            <div class="col-md-9">
                                <div class="form-check">
                                    <input type="checkbox" @if($data['is_subscribed']=='yes' ) checked="checked" @endif
                                        class="form-check-input" id="is_subscribed" name="is_subscribed">
                                    <label class="form-check-label" for="is_subscribed">Check me out</label>
                                </div>
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
@endsection
