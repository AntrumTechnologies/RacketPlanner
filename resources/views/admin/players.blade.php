@extends('layouts.app', ['title' => 'Manage Players '. $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Manage Players</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournament', $tournament->id) }}">{{ $tournament->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Players</li>
                </ol>
            </nav>

            @if ($matches_scheduled > 0)
            <div class="alert alert-info">
                <strong>Matches are already scheduled!</strong> You have to empty all slots prior to adding or removing a player!
            </div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>{{ $tournament->name }}</h3>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-4">
            <h4>Invite a new player</h4>

            <form method="post" action="{{ route('invite-player') }}">
                @csrf
                
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />

                <div class="mb-3">
                    <label for="user_id" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}" @if($matches_scheduled > 0) disabled @endif>
                </div>

                <div class="mb-3">
                    <label for="user_id" class="form-label">Email <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="email" value="{{ old('email') }}" @if($matches_scheduled > 0) disabled @endif>
                </div>

                <button type="submit" class="btn btn-primary" @if($matches_scheduled > 0) disabled @endif>Invite</button>
            </form>
        </div>

        <div class="col-md-4">
            <h4>Assign an existing player</h4>
            
            <form method="post" action="{{ route('assign-player') }}">
                @csrf
                
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />

                <div class="mb-3">
                    <label for="user_id" class="form-label">Name <span class="text-danger">*</span></label>
                    <select class="form-select" id="user_id" name="user_id" @if (count($users) == 0 || $matches_scheduled > 0) disabled @endif>
                        @if (count($users) == 0)
                        <option value="">All users are assigned already</option>
                        @else    
                            <option value="">Select a user...</option>
                        @endif

                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->rating }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <input class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" type="number" min="0" max="10" value="@if(old('rating')){{ old('rating') }}@endif" placeholder="Leave empty to use user' rating" @if($matches_scheduled > 0) disabled @endif>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" value="1" name="clinic" id="clinic" @if($matches_scheduled > 0) disabled @endif>
                        <label class="form-check-label" for="clinic">
                            Join clinic
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" @if(count($users) == 0 || ($matches_scheduled > 0)) disabled @endif>Assign</button>
            </form>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-4">
            <h4>Manually add a player</h4>
            
            <form method="post" action="{{ route('manual-add-player') }}">
                @csrf
                
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />

                <div class="mb-3">
                    <label for="user_id" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}" @if($matches_scheduled > 0) disabled @endif>
                </div>

                <div class="mb-3">
                    <label for="user_id" class="form-label">Email</label>
                    <input class="form-control" type="text" name="email" value="{{ old('email') }}" @if($matches_scheduled > 0) disabled @endif placeholder="Leave empty if user should not be able to login">
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                    <input class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" type="number" min="0" max="10" value="@if(old('rating')){{ old('rating') }}@endif" @if($matches_scheduled > 0) disabled @endif>
                </div>

                <button type="submit" class="btn btn-primary" @if($matches_scheduled > 0) disabled @endif>Add</button>
            </form>
        </div>

        <div class="col-md-4"></div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Enrolled players</h4>
                            
            @if (count($tournament_players) == 0)
                <p>There are no players assigned yet.</p>
            @else
            <p>{{ $count['present'] }} players are present, of which {{ $count['clinic'] }} join the clinic. {{ $count['absent'] }} players are absent.</p>
            @endif

            <ul class="list-group">
                @foreach ($tournament_players as $player)
                    <a href="{{ route('user', $player->user_id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto" id="player{{ $player->id }}">
                            {{ $player->name }} ({{ $player->rating }})
                        </div>
                        
                        @if ($player->clinic == 1)
                            <span class="badge rounded-pill bg-info mt-1">Clinic</span>
                        @endif

                        @if ($player->present == 0)
                        <form method="post" action="{{ route('mark-player-present') }}">
                        @else
                        <form method="post" action="{{ route('mark-player-absent') }}">
                        @endif
                            @csrf
                            
                            <input type="hidden" name="id" value="{{ $player->id }}" />

                            @if ($player->present == 0)
                            <button type="submit" name="submit" class="btn btn-success ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem; color: #fff">Mark present</button>
                            @else
                            <button type="submit" name="submit" class="btn btn-warning ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Mark absent</button>
                            @endif
                        </form>

                        <form method="post" action="{{ route('remove-player') }}">
                            @csrf
                        
                            <input type="hidden" name="id" value="{{ $player->id }}" />
                            <input type="hidden" name="name" value="{{ $player->name }}" />

                            <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" @if($matches_scheduled > 0) disabled @endif>Remove</button>
                        </form>
                    </a>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
