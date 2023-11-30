@extends('layouts.app', ['title' => 'You are invited!'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>You're invited!</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            @if (Auth::check())
                <div class="alert alert-light mb-4">
                    <p>You're invited to a tournament. Click the buton below to enroll.</p>
                    <p><a class="btn btn-primary" href="{{ route('tournament-enroll', $tournament->id) }}" style="color: #fff">Enroll</a></p>
                </div>
            @else
                <div class="alert alert-light mb-4">
                    <p>You're invited to a tournament. Fill in your email address below to enroll.</p>
                    <form method="POST" action="{{ route('tournament-store-invite') }}">
                        @csrf

                        <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enroll') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <h3>{{ $tournament->name }}</h3>

            @if (!empty($tournament->description))
            <h5>Description</h5>
            <p class="text-muted">{{ $tournament->description }}</p>
            @endif

            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Start</h5>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="text-muted">{{ $tournament->datetime_start }}</span>
                </div>

                <div class="col-md-3">
                    <h5>@if (!empty($tournament->enroll_until)) Enroll until @else @if (!empty($tournament->max_players) && $tournament->max_players != 0) Max. players @endif @endif</h5>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="text-muted">@if (!empty($tournament->enroll_until)) {{ $tournament->enroll_until }} @else @if (!empty($tournament->max_players) && $tournament->max_players != 0) {{ $tournament->max_players }} @endif @endif</span>
                </div>

                <div class="col-md-3">
                    <h5>Organizer</h5>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="text-muted">{{ $tournament->organizer }}</span>
                </div>

                @if (!empty($tournament->enroll_until) && !empty($tournament->max_players) && $tournament->max_players != 0)
                <div class="col-md-3">
                    <h5>Max. players</h5>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="text-muted">{{ $tournament->max_players }}</span>
                </div>
                @endif

                @if (!empty($tournament->location))
                <div class="col-md-3">
                    <h5>Location</h5>
                </div>
                <div class="col-md-3">
                    @if (empty($tournament->location_link))
                        <span class="text-muted">{{ $tournament->location }}</span>
                    @else
                        <span class="text-muted"><a href="{{ $tournament->location_link }}" title="Link to location">{{ $tournament->location }}</a></span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
