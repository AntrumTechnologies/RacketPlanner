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
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        New Tournament
                    </div>
                </div>

                <div class="card-body">
                    <h4>Tournament Details</h4>

                    <form method="post" action="{{ route('store-tournament') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="organization" class="form-label">Organization</label>
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
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="datetime_start" class="form-label">Start</label>
                            <input class="form-control @error('datetime_start') is-invalid @enderror" id="datetime_start" name="datetime_start" type="text" placeholder="Y-m-d H:i" value="@if(old('datetime_start')){{ old('datetime_start') }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="datetime_end" class="form-label">End</label>
                            <input class="form-control @error('datetime_end') is-invalid @enderror" id="datetime_end" name="datetime_end" type="text" placeholder="Y-m-d H:i" value="@if(old('datetime_end')){{ old('datetime_end') }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="single" @if (old('type') == "single") selected @endif>Single</option>
                                <option value="double" @if (old('type') == "double") selected @endif>Double</option>
                                <option value="mix" @if (old('type') == "mix") selected @endif>Mix</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="allow_singles" class="form-label">Singles allowed</label>
                            <select class="form-select" id="allow_singles" name="allow_singles">
                                <option value="0" @if (old('allow_singles') == 0) selected @endif>No</option>
                                <option value="1" @if (old('allow_singles') == 1) selected @endif>Yes</option>
                            </select>
                            <div class="form-text" id="basic-addon4">When single matches are allowed, matches will still be scheduled as mixes, but singles could also occur</div>
                        </div>

                        <div class="mb-3">
                            <label for="max_players" class="form-label">Maximum number of players</label>
                            <input class="form-control @error('max_players') is-invalid @enderror" id="max_players" name="max_players" type="text" placeholder="0 for infinite" value="@if(old('max_players')){{ old('max_players') }}@endif">
                        </div>

                        <button type="submit" class="btn btn-primary" name="submit">Create</button>
                        <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
