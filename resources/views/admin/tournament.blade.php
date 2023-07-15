@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournament</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $tournament->name }}
                    </div>

                    @can('admin')
                    <div class="ms-auto">
                         <a href="{{ route('tournament-details', $tournament->id) }}">Edit</a>
                    </div>
                    @endcan
                </div>

                <div class="card-body">
                    @include('layouts.tournament-details')

                    @can('admin')
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <h4>Assigned Players</h4>

                            <div class="list-group">
                                @foreach ($tournamentUsers as $user)
                                <a href="{{ route('user-details', $user->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        {{ $user->name }} 
                                    </div>
                                    @if ($user->rating != '') <span class="badge bg-primary rounded-pill">{{ $user->rating }}</span> @endif
                                </a>
                                @endforeach
                            </div>

                            <br />
                            <a class="btn btn-secondary" href="{{ route('tournament-users', $tournament->id) }}">Manage players</a>
                        </div>
                        <div class="col-md-6">
                            <h4>Assigned Courts</h4>

                            <div class="list-group">
                                @foreach ($tournamentCourts as $court)
                                <a href="{{ route('court-details', $court->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        {{ $court->name }} 
                                    </div>
                                    @if ($court->type != '') <span class="badge bg-primary rounded-pill">{{ ucfirst($court->type) }}</span> @endif
                                </a>
                                @endforeach
                            </div>

                            <br />
                            <a class="btn btn-secondary" href="{{ route('tournament-courts', $tournament->id) }}">Manage courts</a>
                        </div>
                    </div>
                    @endcan

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h4>Scheduled Matches</h4>
                            @if (count($tournamentMatches) == 0)
                                <p>No matches scheduled yet.</p>
                            @else
                                @include('layouts.tournament-matches')
                            @endif

                            @can('admin')
                            <a class="btn btn-secondary" href="#">Schedule new round</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
