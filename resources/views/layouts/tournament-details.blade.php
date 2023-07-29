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
        Number of rounds<br />
    </div>
    <div class="col-md-3">
        <span class="text-muted">{{ $tournament->rounds }}</span>
    </div>
</div>
@can('admin')
<!--
<div class="row mt-3 mb-3">
<div class="col-md-3">
    Type<br />
    Singles allowed
</div>
<div class="col-md-3">
    <span class="text-muted">{{ ucfirst($tournament->type) }}<br />
    @if ($tournament->allow_singles == 1) Yes @else No @endif</span>
</div>
-->
@endcan