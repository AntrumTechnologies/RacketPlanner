@extends('layouts.app', ['title' => 'Verify'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>Verify your email</h1>

            <div class="alert alert-info" role="alert">
                Check your inbox ({{ $email }}) for a link to login.
            </div>

            <p>Please allow up to 5 minutes for the email to arrive.</p>
        </div>
    </div>
</div>
@endsection
