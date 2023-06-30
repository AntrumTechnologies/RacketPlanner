@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournament Details</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $tournament->name }}
                    </div>
                    <div class="ms-auto">
                         <a href="{{ route('edit-tournament', $tournament->id) }}">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    @include('layouts.tournament-details')

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <h4>Assigned Players</h4>

                            <div class="list-group">
                                @foreach ($tournamentUsers as $user)
                                <a href="#" class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        {{ $user->name }} 
                                    </div>
                                    @if ($user->rating != '') <span class="badge bg-primary rounded-pill">{{ $user->rating }}</span> @endif
                                </a>
                                @endforeach
                            </div>

                            <br />
                            <a class="btn btn-secondary" href="{{ route('tournament-matches', $tournament->id) }}">Manage players</a>
                        </div>
                        <div class="col-md-6">
                            <h4>Assigned Courts</h4>

                            <div class="list-group">
                                @foreach ($tournamentCourts as $court)
                                <a href="#" class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        {{ $court->name }} 
                                    </div>
                                    @if ($court->type != '') <span class="badge bg-primary rounded-pill">{{ ucfirst($court->type) }}</span> @endif
                                </a>
                                @endforeach
                            </div>

                            <br />
                            <a class="btn btn-secondary" href="{{ route('tournament-matches', $tournament->id) }}">Manage courts</a>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h4>Scheduled Matches</h4>
                            @include('layouts.tournament-matches')

                            <a class="btn btn-secondary" href="{{ route('tournament-matches', $tournament->id) }}">Manage matches</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
