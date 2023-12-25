@extends('layouts.app', ['title' => 'Create Tournament'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Create Tournament</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Tournament</li>
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
            <h4>Tournament Details</h4>

            <form method="post" action="{{ route('store-tournament') }}">
                @csrf

                <div class="mb-3">
                    <label for="organization" class="form-label">Organization <span class="text-danger">*</span></label>
                    @if (count($organizations) > 1)
                        <select class="form-select" id="organization" name="owner_organization_id">
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}" @if (old('organization') == $organization->id) selected @endif>{{ $organization->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" id="owner_organization_id" name="owner_organization_id" value="{{ $organizations[0]->id }}">
                        <input readonly class="form-control-plaintext" id="organization" value="{{ $organizations[0]->name }}">
                    @endif
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="datetime_start" class="form-label">Start <span class="text-danger">*</span></label>
                    <input class="form-control @error('datetime_start') is-invalid @enderror" id="datetime_start" name="datetime_start" type="datetime-local" placeholder="Y-m-d H:i" value="@if(old('datetime_start')){{ old('datetime_start') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="datetime_end" class="form-label">End <span class="text-danger">*</span></label>
                    <input class="form-control @error('datetime_end') is-invalid @enderror" id="datetime_end" name="datetime_end" type="datetime-local" placeholder="Y-m-d H:i" value="@if(old('datetime_end')){{ old('datetime_end') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" rows="3" name="description" placeholder="Short descriptive text what the tournament is about">@if(old('description')){{ old('description') }}@endif</textarea>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input class="form-control @error('location') is-invalid @enderror" id="location" name="location" type="text" placeholder="Description of the location" value="@if(old('location')){{ old('location') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="location_link" class="form-label">Location link</label>
                    <input class="form-control @error('location_link') is-invalid @enderror" id="location_link" name="location_link" type="url" placeholder="Link to Google Maps" value="@if(old('location_link')){{ old('location_link') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="max_players" class="form-label">Maximum number of players <span class="text-danger">*</span></label>
                    <input class="form-control @error('max_players') is-invalid @enderror" id="max_players" name="max_players" type="number" placeholder="0 for infinite" value="@if(old('max_players')){{ old('max_players') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="enroll_until" class="form-label">Register until</label>
                    <input class="form-control @error('enroll_until') is-invalid @enderror" id="enroll_until" name="enroll_until" type="datetime-local" placeholder="Y-m-d H:i" value="@if(old('enroll_until')){{ old('enroll_until') }}@endif">
                </div>

                <div class="mb-3">
                    <label class="form-label">Type of matches</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="double_matches" id="single_matches" value="0" @if(old('double_matches') == false) checked @endif>
                        <label class="form-check-label" for="single_matches">
                            Single matches
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="double_matches" id="double_matches" value="1" @if(old('double_matches') == true) checked @endif>
                        <label class="form-check-label" for="double_matches">
                            Double matches
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Create</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
