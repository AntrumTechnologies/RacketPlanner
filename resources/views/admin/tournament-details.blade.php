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
                </div>

                <div class="card-body">
                    <h4>Update Tournament Details</h4>

                    <form method="post" action="{{ route('update-tournament-details') }}">
                        @csrf
                        
                        <input type="hidden" name="id" value="{{ $tournament->id }}" />

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $tournament->name }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="datetime_start" class="form-label">Start</label>
                            <input class="form-control @error('datetime_start') is-invalid @enderror" id="datetime_start" name="datetime_start" type="text" placeholder="Y-m-d H:i" value="@if(old('datetime_start')){{ old('datetime_start') }}@else{{ $tournament->datetime_start }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="datetime_end" class="form-label">End</label>
                            <input class="form-control @error('datetime_end') is-invalid @enderror" id="datetime_end" name="datetime_end" type="text" placeholder="Y-m-d H:i" value="@if(old('datetime_end')){{ old('datetime_end') }}@else{{ $tournament->datetime_end }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="matches" class="form-label">Number of matches</label>
                            <input class="form-control @error('matches') is-invalid @enderror" id="matches" name="matches" type="number" value="@if(old('matches')){{ old('matches') }}@else{{ $tournament->matches }}@endif">
                        </div>

                        <label for="duration_m" class="form-label">Duration per match</label>
                        <div class="input-group mb-3">
                            <input class="form-control @error('duration_m') is-invalid @enderror" id="duration_m" name="duration_m" type="text" value="@if(old('duration_m')){{ old('duration_m') }}@else{{ $tournament->duration_m }}@endif">
                            <span class="input-group-text">minutes</span>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="single" @if (old('type') == "single") selected @elseif ($tournament->type == "single") selected @endif>Single</option>
                                <option value="double" @if (old('type') == "double") selected @elseif ($tournament->type == "double") selected @endif>Double</option>
                                <option value="mix" @if (old('type') == "mix") selected @elseif ($tournament->type == "mix") selected @endif>Mix</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="allow_singles" class="form-label">Singles allowed</label>
                            <select class="form-select" id="allow_singles" name="allow_singles">
                                <option value="tennis" @if (old('allow_singles') == 0) selected @elseif ($tournament->allow_singles == 0) selected @endif>No</option>
                                <option value="padel" @if (old('allow_singles') == 1) selected @elseif ($tournament->allow_singles == 1) selected @endif>Yes</option>
                            </select>
                            <div class="form-text" id="basic-addon4">When single matches are allowed, matches will still be scheduled as mixes, but singles could also occur</div>
                        </div>

                        <div class="mb-3">
                            <label for="max_diff_rating" class="form-label">Maximum difference in rating</label>
                            <input class="form-control @error('max_diff_rating') is-invalid @enderror" id="max_diff_rating" name="max_diff_rating" type="number" value="@if(old('max_diff_rating')){{old('max_diff_rating')}}@else{{ $tournament->max_diff_rating}}@endif">
                        </div>

                        <label for="time_between_matches_m" class="form-label">Time between matches</label>
                        <div class="input-group mb-3">
                            <input class="form-control @error('time_between_matches_m') is-invalid @enderror" id="time_between_matches_m" name="time_between_matches_m" type="number" value="@if(old('time_between_matches_m')){{old('time_between_matches_m')}}@else{{ $tournament->time_between_matches_m}}@endif">
                            <span class="input-group-text">minutes</span>
                        </div>

                        <button type="submit" class="btn btn-primary" name="submit">Update</button>
                    </form>

                    <br />
                    <h4>Delete Tournament</h4>

                    <form method="post" action="{{ route('delete-tournament') }}">
                        @csrf

                        <input type="hidden" name="id" value="{{ $tournament->id }}" />
                        <p>Note: all corresponding matches will be deleted as well. There is no way to restore this action.</p>
                        <button type="submit" class="btn btn-danger" name="submit">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection