@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Home</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Your Score</h3>
            
            <div class="text-center">
                <h1 style="font-size: 2.8em"><span class="badge bg-primary" style="width: 75px;">{{ $score }}</span></h1>
            </div>
        </div>        
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Your Matches</h3>
            @if (count($user_matches_per_tournament) == 0)
                <p>No matches have been scheduled for you yet.</p>
            @else
                @include('layouts.user-matches')
            @endif
        </div>        
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8 mt-4">
            <h3>All Tournaments</h3>
        </div>

        @foreach ($all_tournaments as $tournament)
            <div class="col-md-8 mt-4">
                
                <div class="card">
                    <div class="card-header">
                        {{ $tournament->name }}
                    </div>

                    <div class="card-body">
                        @include('layouts.tournament-details')

                        <a class="btn btn-primary" href="{{ route('tournament', $tournament->id) }}">View tournament</a>
                    </div>
                </div>    
            </div>
        @endforeach
    </div>
</div>
@endsection
