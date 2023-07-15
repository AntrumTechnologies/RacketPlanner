@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    Please check your email ({{ $email }}) for a link in order to login.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
