@extends('layouts.app', ['title' => 'Edit Match Details'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit Match Details</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('tournament', $match->tournament_id) }}">{{ $match->tournament_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Match details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>{{ $match->tournament_name }}</h4>

            <div class="card mb-4">
                <div class="card-header d-flex">
                    <div class="me-auto" style="font-size: 1.2em">
                        {{ $match->time }} @ {{ $match->court }}
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

                    <form method="post" action="{{ route('update-match') }}"> 
                        @csrf

                        <input type="hidden" name="id" value="{{ $match->id }}" />
                        <div class="row">
                            <div class="col-1">
                                <img src="/{{ $match->player1a_avatar }}" class="avatar-sm mb-1" />
                                <br />
                                <img src="/{{ $match->player1b_avatar }}" class="avatar-sm mt-3" />
                            </div>
                            <div class="col-4">
                                <select class="form-select" name="player1a_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}"@if($match->player1a_id == $player->user_id) selected @endif>{{ $player->name }} ({{ $player->rating }})</option>
                                    @endforeach
                                </select>
                                <br />
                                <select class="form-select" name="player1b_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}"@if($match->player1b_id == $player->user_id) selected @endif>{{ $player->name }} ({{ $player->rating }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>

                        <hr/>

                        <div class="row">
                            <div class="col-1">
                                <img src="/{{ $match->player2a_avatar }}" class="avatar-sm mb-1" />
                                <br />
                                <img src="/{{ $match->player2b_avatar }}" class="avatar-sm mt-3" />
                            </div>
                            <div class="col-4">
                            <select class="form-select" name="player2a_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}"@if($match->player2a_id == $player->user_id) selected @endif>{{ $player->name }} ({{ $player->rating }})</option>
                                    @endforeach
                                </select>
                                <br />
                                <select class="form-select" name="player2b_id">
                                    <option value="">Select a player...</option>
                                    @foreach ($tournament_players as $player)
                                        <option value="{{ $player->id }}"@if($match->player2b_id == $player->user_id) selected @endif>{{ $player->name }} ({{ $player->rating }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
