@extends('layouts.app', ['title' => 'Create Match'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Create Match</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('tournament', $tournament->id) }}">Tournament</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Match</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>{{ $tournament->name }}</h4>

            <div class="card mb-4">
                <div class="card-header d-flex">
                    <div class="me-auto" style="font-size: 1.2em">
                        {{ $time }} @ {{ $court }}
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('store-match') }}"> 
                        @csrf

                        <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />
                        <input type="hidden" name="slot_id" value="{{ $slot_id }}" />
                        <div class="row">
                            <div class="col-1">
                                <img src="/avatars/placeholder.png" class="avatar-sm mb-1" />
                                <br />
                                <img src="/avatars/placeholder.png" class="avatar-sm mt-3" />
                            </div>
                            <div class="col-4">
                                <select class="form-select" name="player1a_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                                <br />
                                <select class="form-select" name="player1b_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>

                        <hr/>

                        <div class="row">
                            <div class="col-1">
                                <img src="/avatars/placeholder.png" class="avatar-sm mb-1" />
                                <br />
                                <img src="/avatars/placeholder.png" class="avatar-sm mt-3" />
                            </div>
                            <div class="col-4">
                            <select class="form-select" name="player2a_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                                <br />
                                <select class="form-select" name="player2b_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
