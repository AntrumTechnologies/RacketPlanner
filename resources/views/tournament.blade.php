@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $tournament->name }}</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Scheduled Matches</h4>
            @can('admin')
                <p>
                @if (count($players) == 0 || count($courts) == 0 || count($rounds) == 0)
                    <div class="btn-group">
                        <a class="btn btn-info disabled">Generate schedule</a>
                        <a class="btn btn-info disabled">Generate matches</a>
                        <a class="btn btn-primary disabled">Schedule next round</a>
                        <a class="btn btn-secondary disabled">Schedule all</a>
                    </div>
                @else
                    @if (count($schedule) == 0)
                    <div class="btn-group">
                        <a class="btn btn-info" href="{{ route('generate-schedule', $tournament->id) }}">Generate schedule</a>
                        <a class="btn btn-info" href="{{ route('generate-matches', $tournament->id) }}">Generate matches</a>
                        <a class="btn btn-primary disabled">Schedule next round</a>
                        <a class="btn btn-secondary disabled">Schedule all</a>
                    </div>
                    @else
                    <div class="btn-group">
                        <a class="btn btn-info disabled">Generate schedule</a>
                        <a class="btn btn-info disabled">Generate matches</a>
                        <a class="btn btn-primary" href="{{ route('plan-round', [$tournament->id, $next_round_id]) }}">Schedule next round</a>
                        <a class="btn btn-secondary disabled">Schedule all</a>
                    </div>
                    @endif
                @endif
                </p>
            @endcan

            @foreach ($schedule as $match)
                <div class="card mb-4">
                    <div class="card-header d-flex">
                        <div class="me-auto" style="font-size: 1.2em">
                            {{ $match->time }} @ {{ $match->court }}
                        </div>

                        @if ($match->id == null)
                            <div class="ms-auto">
                                <div class="btn-group">
                                    @if ($match->state == 'available')
                                        <a class="btn btn-sm btn-success active">Available</a>
                                        <a href="{{ route('slot-disable', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-danger">Disable</a>
                                        <a href="{{ route('slot-clinic', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-info">Clinic</a>
                                    @elseif ($match->state == 'disabled')
                                        <a href="{{ route('slot-available', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-success">Available</a>
                                        <a class="btn btn-sm btn-danger active">Disable</a>
                                        <a href="{{ route('slot-clinic', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-info">Clinic</a>
                                    @elseif ($match->state == 'clinic')
                                        <a href="{{ route('slot-available', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-success">Available</a>
                                        <a href="{{ route('slot-disable', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-danger">Disable</a>
                                        <a class="btn btn-sm btn-info active">Clinic</a>
                                    @endif
                                </div>

                                <a href="#" class="btn btn-sm btn-secondary">Schedule slot</a>
                            </div>
                        @else
                            <div class="ms-auto">
                                <a href="{{ route('match', $match->id) }}" class="small text-muted">Permalink</a>
                            </div>
                        @endcan
                    </div>

                    @if ($match->id != null)
                        <div class="card-body">
                            <div class="row">
                                <div class="col-9">
                                    <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a><br />
                                    <a href="{{ route('user', $match->player1b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1b }}</a>
                                </div>
                                <div class="col-3 justify-content-center align-self-center">
                                    @if ($match->score1 != "")
                                        {{ $match->score1 }}
                                        @php
                                            if ($match->score1 > $match->score2) {
                                        @endphp
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-trophy-fill" viewBox="0 0 16 16"><path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935z"/></svg>
                                        @php
                                            }
                                        @endphp
                                    @endif
                                </div>
                            </div>

                            <hr/>

                            <div class="row">
                                <div class="col-9">
                                    <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a><br />
                                    <a href="{{ route('user', $match->player2b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2b }}</a>
                                </div>
                                <div class="col-3 justify-content-center align-self-center">
                                    @if ($match->score2 != "")
                                        {{ $match->score2 }}
                                        @php
                                            if ($match->score2 > $match->score1) {
                                        @endphp
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-trophy-fill" viewBox="0 0 16 16"><path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935z"/></svg>
                                        @php
                                            }
                                        @endphp
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach   
            
            <div class="row mt-5">
                <div class="col-md-12">
                    <h4>All Players in Tournament</h4>

                    @if (count($players) == 0)
                        <p>No players are assigned yet. Perhaps start by assigning a few players?</p>
                    @else
                        <p><div class="list-group">
                            @foreach ($players as $user)
                            <a href="{{ route('user', $user->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    {{ $user->name }} 
                                </div>
                                @if ($user->rating != '') <span class="badge bg-primary rounded-pill">Rating: {{ $user->rating }}</span> @endif
                            </a>
                            @endforeach
                        </div></p>
                    @endif

                    @can('admin')
                        <a class="btn btn-secondary" href="{{ route('players', $tournament->id) }}">Manage players</a>
                    @endcan
                </div>
            </div>

            @can('admin')
            <div class="row mt-5">
                <div class="col-md-6">
                    <h4>Assigned Courts</h4>

                    @if (count($courts) == 0)
                        <p>There are no courts for this tournament yet.</p>
                    @else
                        <p><div class="list-group">
                            @foreach ($courts as $court)
                            <a href="{{ route('court', $court->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    {{ $court->name }} 
                                </div>
                            </a>
                            @endforeach
                        </div></p>
                    @endif

                    @can('admin')
                    <a class="btn btn-secondary" href="{{ route('create-court', $tournament->id) }}">Add new court</a>
                    @endcan
                </div>
                <div class="col-md-6">
                    <h4>Assigned Rounds</h4>

                    @if (count($rounds) == 0)
                        <p>There are no rounds for this tournament yet.</p>
                    @else
                        <p><div class="list-group">
                            @foreach ($rounds as $round)
                            <a href="{{ route('round', $round->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    {{ $round->name }} ({{ $round->starttime }} - {{ $round->endtime }})
                                </div>
                            </a>
                            @endforeach
                        </div></p>
                    @endif

                    @can('admin')
                    <a class="btn btn-secondary" href="{{ route('create-round', $tournament->id) }}">Add new round</a>
                    @endcan
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection