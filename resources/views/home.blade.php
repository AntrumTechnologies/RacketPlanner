@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Home</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Your Matches</h3>
            @if (count($matches) == 0)
                <p>No matches scheduled yet.</p>
            @else
                @foreach ($matches as $match)
                <div class="card">
                    <div class="card-header">
                        {{ $match->court }}
                    </div>

                    <div class="card-body">

                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <div class="col-md-8 mt-4">
            <h3>All Tournaments</h3>
            @foreach ($tournaments as $tournament)
            <div class="card">
                <div class="card-header">
                    {{ $tournament->name }}
                </div>

                <div class="card-body">
                    @include('layouts.tournament-details')

                    <a class="btn btn-secondary" href="{{ route('tournament', $tournament->id) }}">View tournament details</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
</div>
@endsection
