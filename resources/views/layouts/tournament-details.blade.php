<div class="row mb-3">
    <div class="col-md-3">
        Start<br/>
        End
    </div>
    <div class="col-md-3">
        <span class="text-muted">{{ $tournament->datetime_start }}<br />
        {{ $tournament->datetime_end }}</span>
    </div>

    <div class="col-md-3">
        Number of matches<br />
        Duration per match
    </div>
    <div class="col-md-3">
        <span class="text-muted">{{ $tournament->matches }}<br />
        {{ $tournament->duration_m }} min.</span>
    </div>
</div>
@can('admin')
<div class="row mt-3 mb-3">
<div class="col-md-3">
    Type<br />
    Singles allowed
</div>
<div class="col-md-3">
    <span class="text-muted">{{ ucfirst($tournament->type) }}<br />
    @if ($tournament->allow_singles == 1) Yes @else No @endif</span>
</div>

<div class="col-md-3">
    Time between matches
</div>
<div class="col-md-3">
    <span class="text-muted">{{ $tournament->time_between_matches_m }} min.</span>
</div>
</div>
@endcan