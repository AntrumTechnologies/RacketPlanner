@extends('layouts.app', ['title' => 'Players '. $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournament Players</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('tournament', $tournament->id) }}">Tournament</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tournament Players</li>
                </ol>
            </nav>

            <h4>{{ $tournament->name }}</h4>

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
            <h4>Assign Player</h4>
            
            <form method="post" action="{{ route('assign-player') }}">
                @csrf
                
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />

                <div class="mb-3">
                    <label for="user_id" class="form-label">Name</label>
                    <select class="form-select" id="user_id" name="user_id" @if (count($users) == 0) disabled @endif>
                        @if (count($users) == 0)
                        <option value="">All users are assigned already</option>
                        @else    
                            <option value="">Select a user...</option>
                        @endif

                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" value="1" name="clinic" id="clinic">
                        <label class="form-check-label" for="clinic">
                            Clinic
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" @if (count($users) == 0) disabled @endif>Assign</button>
            </form>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Assigned Players</h4>
                            
            @if (count($tournament_players) == 0)
                <p>There are no players assigned yet.</p>
            @endif

            <ul class="list-group">
                @foreach ($tournament_players as $player)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            {{ $player->name }}
                        </div>
                        
                        @if ($player->clinic == 1)
                            <span class="badge rounded-pill text-bg-primary mt-1">Clinic</span>
                        @endif

                        @if ($player->present == 0)
                        <form method="post" action="{{ route('mark-player-present') }}">
                        @else
                        <form method="post" action="{{ route('mark-player-absent') }}">
                        @endif
                            @csrf
                            
                            <input type="hidden" name="id" value="{{ $player->id }}" />

                            @if ($player->present == 0)
                            <button type="submit" name="submit" class="btn btn-success ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Mark present</button>
                            @else
                            <button type="submit" name="submit" class="btn btn-warning ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Mark absent</button>
                            @endif
                        </form>

                        <form method="post" action="{{ route('remove-player') }}">
                            @csrf
                        
                            <input type="hidden" name="id" value="{{ $player->id }}" />
                            <input type="hidden" name="name" value="{{ $player->name }}" />

                            <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Remove</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
