@extends('layouts.app', ['title' => 'Login'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (app('request')->input('error') == "invalid")
            <div class="alert alert-danger" role="alert">
                The link you clicked expired. Enter your email address below in order to receive a new link to login.
            </div>
            @endif

            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center" style="padding-top: 50px">
        <div class="col-md-8">
            <hr style="margin-bottom: 50px" />
            <div class="row">
                <div class="col-md-5">
                    <h1>Racket Planner makes scheduling effortless</h1>
                    <p class="lead">Make scheduling tennis tournaments so easy you only need to worry about your opponent.</p>
                </div>
                <div class="col-md-7">
                    <img src="/undraw_grand_slam_84ep.svg" class="img-fluid" style="filter: drop-shadow(3px 3px 2px rgb(0 0 0 / 0.4));">
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4" style="padding-top: 50px">
        <div class="col-md-8">
            <hr style="margin-bottom: 50px" />
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <h1>The only planner where you are in control</h1>
                    <p class="lead">Whether you are organizing a single or mixed tournament, you are fully in control of the schedule.</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <img src="/undraw_events_re_98ue.svg" class="img-fluid" style="filter: drop-shadow(3px 3px 2px rgb(0 0 0 / 0.4));">
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4" style="padding-top: 50px">
        <div class="col-md-8">
            <hr style="margin-bottom: 50px" />
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <p class="lead">Hey, Tennis trainers and Tennis clubs!<br />Interested in a demo?</p>

                    <p>Reach out via <a href="mailto:racketplanner@antrum-technologies.nl?subject=Racket Planner demo">racketplanner@antrum-technologies.nl</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
