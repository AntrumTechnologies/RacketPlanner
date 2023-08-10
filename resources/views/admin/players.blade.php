@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournament Players</h2>

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
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $tournament->name }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
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
                                <button type="submit" class="btn btn-primary" @if (count($users) == 0) disabled @endif>Assign</button>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h4>Assigned Players</h4>
                            
                            @if (count($tournament_players) == 0)
                                <p>There are no players assigned yet.</p>
                            @endif

                            <div class="list-group">
                                @foreach ($tournament_players as $player)
                                <form method="post" action="{{ route('remove-player') }}">
                                    @csrf
                                
                                    <input type="hidden" name="id" value="{{ $player->id }}" />
                                    <input type="hidden" name="name" value="{{ $player->name }}" />

                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            {{ $player->name }} ({{ $player->email }})
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-danger" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Remove</button>
                                    </div>
                                </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection