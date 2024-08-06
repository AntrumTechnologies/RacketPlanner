@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Hi {{ $first_name }}!</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (!empty($tournament_today)) 
                <div class="alert alert-warning">Mark yourself present!</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Upcoming matches</h3>
            @if ((!isset($user_matches_per_tournament) || count($user_matches_per_tournament) == 0) && (!isset($user_clinics) || count($user_clinics) == 0))
                <p>No matches have been scheduled for you yet.</p>
            @else
                @include('layouts.user-matches')
            @endif
        </div>        
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8 mt-4">
            <h3>Upcoming tournaments</h3>

            @if (count($your_tournaments) == 0)
            <p>It's a bit empty here. Why not join a new tournament?</p>
            <a href="{{ route('tournaments') }}" class="btn btn-primary">See all tournaments</a>
            @endif
        </div>
        
        @foreach ($your_tournaments as $tournament)
            @include('layouts.tournament-details')
        @endforeach
    </div>
</div>
@endsection
