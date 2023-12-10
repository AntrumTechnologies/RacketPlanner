@extends('layouts.app', ['title' => $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex">
                <div class="me-auto">
                    <h2>{{ $tournament->name }}</h2>
                </div>
                
                @if ($is_user_admin)
                <div class="ms-auto">
                    <a class="btn btn-primary btn-sm" href="{{ route('edit-tournament', $tournament->id) }}">Edit details</a>
                </div>
                @endif
            </div>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tournament->name }}</li>
                </ol>
            </nav>

            @if ($is_user_admin)
                @if ($regenerate_schedule == true)
                <div class="alert alert-warning" role="alert">
                    <p>A change was made to the courts or rounds. Please generate the schedule again. Note: this might take a while.</p>
                    <a class="btn btn-warning" href="{{ route('generate-schedule', $tournament->id) }}">Regenerate schedule</a>
                </div>
                @endif

                @if ($regenerate_matches == true)
                <div class="alert alert-warning" role="alert">
                    <p>A change was made to the players. Please generate the matches again.</p>
                    <a class="btn btn-warning" href="{{ route('generate-matches', $tournament->id) }}">Regenerate matches</a>
                </div>
                @endif
            @endif

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
        @if ($tournament->is_enrolled == true)
            @if ($tournament->can_withdraw == true)
                <div class="alert alert-light" role="alert">
                    <p>You are enrolled in this tournament! You can withdraw until @if (empty($tournament->enroll_until)) the tournament starts. @else {{ $tournament->enroll_until }}. @endif</p>
                    <a class="btn btn-warning" href="{{ route('tournament-withdraw', $tournament) }}">Withdraw</a>
                </div>
            @else
                <div class="alert alert-light" role="alert">
                    You are enrolled in this tournament! You can't withdraw anymore.
                </div>
            @endif
        @else
            @if ($tournament->can_enroll == true)
                <div class="alert alert-light" role="alert">
                    <p>You are <strong>not</strong> enrolled in this tournament. You can enroll until @if (empty($tournament->enroll_until)) the tournament starts. @else {{ $tournament->enroll_until }}. @endif</p>
                    <a class="btn btn-success" href="{{ route('tournament-enroll', $tournament) }}" style="color: #fff">Enroll</a>
                </div>
            @else
                <div class="alert alert-light" role="alert">
                    You are <strong>not</strong> enrolled in this tournament and can't enroll anymore.
                </div>
            @endif
        @endif
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (!empty($tournament->description))
            <h4>Description</h4>
            <p class="text-muted">{{ $tournament->description }}</p>
            @endif

            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Start</h5>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">{{ $tournament->datetime_start }}</span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Organizer</h5>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">{{ $tournament->organizer }}</span>
                </div>
            </div>
            
            @if (!empty($tournament->location))
            <div class="row mb-3">
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
            </div>
            @endif

            @if (!empty($tournament->public_link))
            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Public invite link</h5>
                </div>
                <div class="col-md-3">
                    <input type="text" value="https://tep.antrum-technologies.nl/tournament/invite/{{ $tournament->public_link }}" class="form-control" readonly />
                </div>
            </div>
            @endif
        </div>
    </div>

    @if ($is_user_admin)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Admin</h3>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <h5>Manage</h5>
                    <ul class="list-group">
                        <a href="{{ route('players', $tournament->id) }}" class="list-group-item link-primary">Manage or invite players</a>
                        <a href="{{ route('courts-rounds', $tournament->id) }}" class="list-group-item link-primary">Manage courts and rounds</a>
                        @if (count($players) == 0)
                        <a class="list-group-item disabled">Show leaderboard</a>
                        @else
                        <a href="{{ route('leaderboard', $tournament->id) }}" class="list-group-item link-primary">Show leaderboard</a>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-6">
                    <h5>Matches</h5>
                    @if (count($players) == 0 || $courts == 0 || $rounds == 0)
                        <div class="btn-group mb-2">
                            <a class="btn btn-primary disabled">Schedule next round</a>
                            <a class="btn btn-secondary disabled">Schedule all</a>
                        </div><br />
                        <div class="btn-group">
                            <a class="btn btn-danger disabled">Empty all slots</a>
                        </div>
                    @else
                        <div class="btn-group mb-2">
                            @if (count($schedule) == 0)
                            <a class="btn btn-primary disabled">Schedule next round</a>
                            <a class="btn btn-secondary disabled">Schedule all</a>
                            @else
                            <a class="btn btn-primary" href="{{ route('plan-round', [$tournament->id, $next_round_id]) }}">Schedule next round</a>
                            <a class="btn btn-secondary" href="{{ route('plan-schedule', $tournament->id) }} ">Schedule all</a>
                            @endif
                        </div><br />
                        <div class="btn-group">
                            @if ($matches_scheduled == 0)
                            <a class="btn btn-danger disabled">Empty all slots</a>
                            @else
                            <a class="btn btn-danger" href="{{ route('empty-all-slots', [$tournament->id]) }}">Empty all slots</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>           
    @endif

    @if ($tournament->is_enrolled == true)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Your Score</h3>
            
            @if ($tournament->leaderboard == 1)
                @if (count($players) == 0)
                <p><a class="btn btn-secondary disabled">Show leaderboard</a></p>
                @else
                <p><a class="btn btn-secondary" href="{{ route('leaderboard', $tournament->id) }}">Show leaderboard</a></p>
                @endif
            @endif
            
            <div class="text-center">
                <h1 style="font-size: 2.8em"><span class="badge bg-primary" style="width: 75px;">{{ $score }}</span></h1>
            </div>
        </div>        
    </div>
    @endif

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Scheduled Matches</h3>
            @if (count($schedule) == 0)
                <p>No matches have been scheduled yet.</p>
            @else
                <div class="accordion" id="accordionExample">
                @foreach ($schedule as $match)
                    @php
                        if (isset($previous_match_time) && $match->time != $previous_match_time) {
                            echo '</div></div>';
                        }
                    
                        if (!isset($previous_match_time) || $match->time != $previous_match_time) {
                    @endphp
                    <div class="accordion-item">
                    
                        <div class="accordion-header" style="position: relative">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $match->round_id }}" aria-expanded="true" aria-controls="collapse{{ $match->round_id }}" style="background-color: #f8fafc !important;">
                                <div class="input-group gap-3 accordion-header">
                                    <h5 style="padding-top: 0.3em" id="round{{ $match->round_id }}">{{ $match->round }}</h5>
                                </div>
                            </button>
                            <span class="ms-auto me-3" style="position: absolute; top: 15px; right: 50px; z-index: 999;">
                                    @if ($match->public == 0)
                                        <a href="{{ route('publish-round', [$tournament->id, $match->round_id]) }}" class="btn btn-primary">Publish round</a>
                                    @else
                                        <a href="{{ route('unpublish-round', [$tournament->id, $match->round_id]) }}" class="btn btn-warning">Unpublish round</a>
                                    @endif
                                    </span>
                        </div>

                        @php
                            if (!isset($previous_match_time) || request()->get('showround') == 'all') {
                        @endphp
                        <div id="collapse{{ $match->round_id }}" class="accordion-collapse collapse show">
                    @php
                            } else {
                    @endphp
                            <div id="collapse{{ $match->round_id }}" class="accordion-collapse collapse">
                    @php   
                            }
                        }
                        
                        $previous_match_time = $match->time;
                    @endphp
                    
                    <div class="card mb-4 m-3">
                        <div class="card-header d-flex">
                            <div class="me-auto" style="font-size: 1.2em" id="slot{{ $match->schedule_id }}">
                                {{ $match->time }} @ {{ $match->court }}
                            </div>

                            @if ($match->id == null)
                                @if ($is_user_admin)
                                <div class="ms-auto">
                                    <div class="btn-group">
                                        @if ($match->state == 'available')
                                            <a class="btn btn-sm btn-success active" style="color: #fff">Available</a>
                                            <a href="{{ route('slot-disable', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning">Disable</a>
                                            <a href="{{ route('slot-clinic', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning">Clinic</a>
                                        @elseif ($match->state == 'disabled')
                                            <a href="{{ route('slot-available', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning">Available</a>
                                            <a class="btn btn-sm btn-success active" style="color: #fff">Disable</a>
                                            <a href="{{ route('slot-clinic', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning">Clinic</a>
                                        @elseif ($match->state == 'clinic')
                                            <a href="{{ route('slot-available', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning">Available</a>
                                            <a href="{{ route('slot-disable', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning">Disable</a>
                                            <a class="btn btn-sm btn-success" style="color: #fff">Clinic</a>
                                        @endif
                                    </div>

                                    @if ($match->public == 0)
                                        <a href="{{ route('publish-slot', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-primary ms-2">Publish</a>
                                    @else
                                        <a href="{{ route('unpublish-slot', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning ms-2">Unpublish</a>
                                    @endif
                                </div>
                                @endif
                            @else
                                <div class="ms-auto">
                                    <a href="{{ route('match', $match->id) }}"><i class="bi bi-link-45deg" style="font-size: 1rem;"></i></a>

                                    @if ($is_user_admin)
                                        @if ($match->public == 0)
                                            <a href="{{ route('empty-slot', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-danger ms-2">Empty slot</a>
                                            <a href="{{ route('edit-match', [$match->id]) }}" class="btn btn-sm btn-warning ms-2">Edit match</a>
                                            <a href="{{ route('publish-slot', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-primary ms-2">Publish</a>
                                        @else
                                            <a href="{{ route('unpublish-slot', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-warning ms-2">Unpublish</a>
                                        @endif
                                    @endif
                                </div>
                            @endcan
                        </div>

                        @if ($match->state == "clinic")
                            <div class="card-body">
                                @foreach ($schedule_clinic as $clinic)
                                    <a href="{{ route('user', $clinic->user_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $clinic->user_name }}</a><br />
                                @endforeach
                            </div>
                        @endif

                        @if ($match->id != null)
                            <div class="card-body">
                                <form method="post" action="{{ route('save-score') }}">
                                    @csrf
                                    
                                    <input type="hidden" name="id" value="{{ $match->id }}">
                                    <div class="row">
                                        <div class="col-9">
                                            <img src="/{{ $match->player1a_avatar }}" class="avatar-sm" />
                                            <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a> ({{ $match->player1a_rating }})
                                            <br />
                                            <img src="/{{ $match->player1b_avatar }}" class="avatar-sm mt-2" />
                                            <a href="{{ route('user', $match->player1b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1b }}</a> ({{ $match->player1b_rating }})
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

                                                @if ($is_user_admin)
                                                    <input class="form-control form-control-sm" type="number" name="score1" placeholder="Score">
                                                @endif
                                            @else
                                                @if ($is_user_admin)
                                                    <input class="form-control form-control-sm" type="number" name="score1" placeholder="Score">
                                                @else
                                                    @if ($match->user_is_player)
                                                        <input class="form-control form-control-sm" type="number" name="score1" placeholder="Score">
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <hr/>

                                    <div class="row">
                                        <div class="col-9">
                                            <img src="/{{ $match->player2a_avatar }}" class="avatar-sm" />
                                            <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a> ({{ $match->player2a_rating }})
                                            <br />
                                            <img src="/{{ $match->player2b_avatar }}" class="avatar-sm mt-2" />
                                            <a href="{{ route('user', $match->player2b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2b }}</a> ({{ $match->player2b_rating }})
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

                                                @if ($is_user_admin)
                                                    <input class="form-control form-control-sm" type="number" name="score2" placeholder="Score">
                                                @endif
                                            @else
                                                @if ($is_user_admin)
                                                    <input class="form-control form-control-sm" type="number" name="score1" placeholder="Score">
                                                @else
                                                    @if ($match->user_is_player)
                                                        <input class="form-control form-control-sm" type="number" name="score1" placeholder="Score">
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    @if ($is_user_admin)
                                        <div class="row mt-2">
                                            <div class="col-sm-9">
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-primary btn-sm">Save score</button>
                                                </div>    
                                            </div>
                                        </div>
                                    @else
                                        @if ($match->user_is_player)
                                            <div class="row mt-2">
                                                <div class="col-sm-9">
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" class="btn btn-primary btn-sm">Save score</button>
                                                    </div>    
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </form>
                            </div>
                        @else
                            @if ($match->state == 'available')
                            <div class="card-body">
                                <div class="btn-group">
                                    <a href="{{ route('plan-slot', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-dark">Schedule slot</a>
                                    <a href="{{ route('create-match', [$tournament->id, $match->schedule_id]) }}" class="btn btn-sm btn-secondary">Manual fill</a>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                @endforeach  
                </div></div>
            @endif
        </div>
    </div> 

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>All Players</h4>

            @if (count($players) == 0)
                <p>No players are enrolled yet.</p>
            @else
                <p>{{ $count['present'] }} players are present, of which {{ $count['clinic'] }} join the clinic. {{ $count['absent'] }} players are absent.</p>
                <p><div class="list-group">
                    @foreach ($players as $player)
                    <a href="{{ route('user', $player->user_id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            {{ $player->name }} ({{ $player->rating }})
                        </div>
                        @if ($player->clinic == true)<span class="badge bg-info rounded-pill">Clinic</span>@endif
                        @if ($player->present == true)
                            <span class="badge bg-success rounded-pill ms-2" style="min-width: 85px">{{ $player->no_matches }} match(es)</span>
                        @else
                            <span class="badge bg-warning rounded-pill ms-2" style="min-width: 85px">Not present</span>
                        @endif
                    </a>
                    @endforeach
                </div></p>
            @endif
        </div>
    </div>
</div>
@endsection
