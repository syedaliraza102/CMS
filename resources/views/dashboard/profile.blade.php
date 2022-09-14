@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profile</div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        @foreach(\Auth::user()->toArray() as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td> {{ $value }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
