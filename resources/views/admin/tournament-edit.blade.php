@extends('layouts.app', ['title' => 'Edit '. $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit details</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournament', $tournament->id) }}">{{ $tournament->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit details</li>
                </ol>
            </nav>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>{{ $tournament->name }}</h3>
            <form method="post" action="{{ route('update-tournament') }}">
                @csrf
                
                <input type="hidden" name="id" value="{{ $tournament->id }}" />

                <div class="alert alert-light">
                    <h5>Tournament details</h5>

                    <div class="mb-3">
                        <label for="organization" class="form-label">Organization <span class="text-danger">*</span></label>
                        @if (count($organizations) > 1)
                            <select class="form-select" id="organization" name="organization">
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}" @if (old('organization') == "{{ $organization->id }}") selected @elseif($tournament->owner_organization_id == $organization->id) selected @endif>{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input readonly class="form-control-plaintext" id="organization" value="{{ $organizations[0]->name }} ">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $tournament->name }}@endif">
                    </div>

                    <div class="mb-3">
                        <label for="datetime_start" class="form-label">Start <span class="text-danger">*</span></label>
                        <input class="form-control @error('datetime_start') is-invalid @enderror" id="datetime_start" name="datetime_start" type="datetime-local" placeholder="Y-m-d H:i" value="@if(old('datetime_start')){{ old('datetime_start') }}@else{{ $tournament->datetime_start }}@endif">
                    </div>

                    <div class="mb-3">
                        <label for="datetime_end" class="form-label">End <span class="text-danger">*</span></label>
                        <input class="form-control @error('datetime_end') is-invalid @enderror" id="datetime_end" name="datetime_end" type="datetime-local" placeholder="Y-m-d H:i" value="@if(old('datetime_end')){{ old('datetime_end') }}@else{{ $tournament->datetime_end }}@endif">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" name="description" placeholder="Short descriptive text what the tournament is about">@if(old('description')){{ old('description') }}@else{{ $tournament->description }}@endif</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input class="form-control @error('location') is-invalid @enderror" id="location" name="location" type="text" placeholder="Description of the location" value="@if(old('location')){{ old('location') }}@else{{ $tournament->location }}@endif">
                    </div>

                    <div class="mb-3">
                        <label for="location_link" class="form-label">Location link</label>
                        <input class="form-control @error('location_link') is-invalid @enderror" id="location_link" name="location_link" type="url" placeholder="Link to Google Maps" value="@if(old('location_link')){{ old('location_link') }}@else{{ $tournament->location_link }}@endif">
                    </div>

                    <div class="mb-3">
                        <label for="max_players" class="form-label">Maximum number of players <span class="text-danger">*</span></label>
                        <input class="form-control @error('max_players') is-invalid @enderror" id="max_players" name="max_players" type="number" placeholder="0 for infinite" value="@if(old('max_players')){{ old('max_players') }}@else{{ $tournament->max_players }}@endif">
                    </div>

                    <div class="mb-3">
                        <label for="enroll_until" class="form-label">Register until</label>
                        <input class="form-control @error('enroll_until') is-invalid @enderror" id="enroll_until" name="enroll_until" type="datetime-local" placeholder="Y-m-d H:i" value="@if(old('enroll_until')){{ old('enroll_until') }}@else{{ $tournament->enroll_until }}@endif">
                    </div>
                </div>

                <div class="alert alert-light">
                    <h5>Match generation config</h5>

                    <div class="mb-3">
                        <label for="number_of_matches" class="form-label">Number of matches per player:</label> <output id="number_of_matches_output">@if(old('number_of_matches')){{ old('number_of_matches') }}@else{{ $tournament->number_of_matches }}@endif</output>
                        <div class="row">
                            <div class="col-1 text-center">10</div>
                            <div class="col-10"><input type="range" class="form-range" id="number_of_matches" name="number_of_matches" min="10" max="60" step="1" value="@if(old('number_of_matches')){{ old('number_of_matches') }}@else{{ $tournament->number_of_matches }}@endif" oninput="document.getElementById('number_of_matches_output').value = this.value"></div>
                            <div class="col-1 text-center">60</div>
                        </div>
                        <div id="number_of_matches_help" class="form-text">Number of possible matches that will be generated for each and every player.</div>
                    </div>

                    <div class="mb-3">
                        <label for="partnerRatingTolerance" class="form-label">Partner rating tolerance:</label> <output id="partnerRatingToleranceOutput">@if(old('partner_rating_tolerance')){{ old('partner_rating_tolerance') }}@else{{ $tournament->partner_rating_tolerance }}@endif</output>
                        <div class="row">
                            <div class="col-1 text-center">0</div>
                            <div class="col-10"><input type="range" class="form-range" id="partnerRatingTolerance" name="partner_rating_tolerance" min="0" max="10" step="0.1" value="@if(old('partner_rating_tolerance')){{ old('partner_rating_tolerance') }}@else{{ $tournament->partner_rating_tolerance }}@endif" oninput="document.getElementById('partnerRatingToleranceOutput').value = this.value"></div>
                            <div class="col-1 text-center">10</div>
                        </div>
                        <div id="partnerRatingToleranceHelp" class="form-text">Maximum difference in rating accepted between members of the same team. Decrease to keep differences to a minimum.</div>
                    </div>

                    <div class="mb-3">
                        <label for="teamRatingTolerance" class="form-label">Team rating tolerance:</label> <output id="teamRatingToleranceOutput">@if(old('team_rating_tolerance')){{ old('team_rating_tolerance') }}@else{{ $tournament->team_rating_tolerance }}@endif</output>
                        <div class="row">
                            <div class="col-1 text-center">0</div>
                            <div class="col-10"><input type="range" class="form-range" id="teamRatingTolerance" name="team_rating_tolerance" min="0" max="10" step="0.1" value="@if(old('team_rating_tolerance')){{ old('team_rating_tolerance') }}@else{{ $tournament->team_rating_tolerance }}@endif" oninput="document.getElementById('teamRatingToleranceOutput').value = this.value"></div>
                            <div class="col-1 text-center">10</div>
                        </div>
                        <div id="teamRatingToleranceHelp" class="form-text">Maximum difference in rating accepted between two teams. Increase if everyone should be able to play with everyone. Decrease to keep teams equal.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Update</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
            </form>

            <hr class="mt-4 mb-4" />

            <h3>Delete tournament</h3>

            <form method="post" action="{{ route('delete-tournament') }}">
                @csrf

                <input type="hidden" name="id" value="{{ $tournament->id }}" />
                <p>There is no way to restore this action.</p>
                <button type="submit" class="btn btn-warning" name="submit" onclick="return confirm('Are you sure?');">Delete</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
