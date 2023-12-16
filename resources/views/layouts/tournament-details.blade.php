<div class="col-md-8 mt-4">
    <div class="card">
        <div class="card-header">
            {{ $tournament->name }}
        </div>

        <div class="card-body">
            @if ($tournament->is_enrolled == true)
            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Your score</h5>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">{{ $tournament->score }}</span>
                </div>
            </div>
            @endif

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
                    <span class="text-muted"><a href="{{ route('organization', $tournament->organization_id) }}">{{ $tournament->organizer }}</a></span>
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
                        <span class="text-muted"><a href="{{ $tournament->location_link }}" title="Link to location" target="_blank">{{ $tournament->location }}</a></span>
                    @endif
                </div>
                @endif
            </div>

            <div class="btn-group">
                <a class="btn btn-primary" href="{{ route('tournament', $tournament->id) }}">View tournament</a>

                @if ($tournament->is_enrolled == true)
                    @if ($tournament->can_withdraw == true)
                        <a class="btn btn-warning" href="{{ route('tournament-withdraw', $tournament->id) }}">Withdraw</a>
                    @else
                        <a class="btn btn-warning disabled">Withdraw</a>
                    @endif
                @else
                    @if ($tournament->can_enroll == true)
                        <a class="btn btn-success" href="{{ route('tournament-enroll', $tournament->id) }}" style="color: #fff">Enroll</a>
                    @else
                        <a class="btn btn-success disabled" style="color: #fff">Enroll</a>
                    @endif
                @endif
            </div>

            @if ($tournament->is_user_admin)
            <a class="btn btn-secondary" href="{{ route('players', $tournament->id) }}">Manage or invite players</a>
            @endif
        </div>
    </div>    
</div>
