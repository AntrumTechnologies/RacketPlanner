@extends('layouts.app', ['title' => 'Manage Courts and Rounds'. $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Manage Courts and Rounds</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournament', $tournament->id) }}">{{ $tournament->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Courts and Rounds</li>
                </ol>
            </nav>

            @if ($matches_scheduled > 0)
            <div class="alert alert-info">
                <strong>Matches are already scheduled!</strong> You have to empty all slots prior to changing any courts or rounds!
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
            <h4>Courts</h4>

            @if (count($courts) == 0)
                <div class="list-group">
                    <div class="list-group-item">
                        <form method="post" action="{{ route('store-court') }}">
                            @csrf
                            <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />

                            <div class="d-flex justify-content-between align-items-start">
                                <div class="me-auto">
                                    <input type="text" class="form-control form-control-sm" name="name" placeholder="New court name..." />
                                </div>
                        
                                <button type="submit" name="submit" class="btn btn-primary" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" @if($matches_scheduled > 0) disabled @endif>Add court</button>
                            </div>
                        </form>    
                    </div>
                </div>
            @else
                <p class="text-muted">Select a court to edit its details.</p>
                <p><div class="list-group">
                    @foreach ($courts as $court)
                    <a href="{{ route('court', $court->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            {{ $court->name }} 
                        </div>

                        <form method="post" action="{{ route('delete-court') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $court->id }}" />
                            <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />
                            <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" @if($matches_scheduled > 0) disabled @endif>Delete</button>
                        </form>
                    </a>
                    @endforeach

                    <div class="list-group-item">
                        <form method="post" action="{{ route('store-court') }}">
                            @csrf
                            <input type="hidden" name="tournament_id" value="{{ $tournament->id }}" />

                            <div class="d-flex justify-content-between align-items-start">
                                <div class="me-auto">
                                    <input type="text" class="form-control form-control-sm" name="name" placeholder="New court name..." />
                                </div>
                        
                                <button type="submit" name="submit" class="btn btn-primary" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" @if($matches_scheduled > 0) disabled @endif>Add court</button>
                            </div>
                        </form>    
                    </div>
                </div></p>
            @endif
        </div>

        <div class="col-md-4">
            <h4>Rounds</h4>

            @if (count($rounds) == 0)
                <p>There are no rounds for this tournament yet.</p>
            @else
                <p class="text-muted">Select a round to edit its details.</p>
                <p><div class="list-group">
                    @foreach ($rounds as $round)
                    <a href="{{ route('round', $round->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            {{ $round->name }} ({{ $round->starttime }})
                        </div>

                        <form method="post" action="{{ route('delete-round') }}">
                            @csrf
                            
                            <input type="hidden" name="id" value="{{ $round->id }}" />
                            <input type="hidden" name="tournament_id" value="{{ $round->tournament_id }}" />

                            <button type="submit" class="btn btn-sm btn-danger" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" @if($matches_scheduled > 0) disabled @endif>Delete</button>
                        </form>
                    </a>
                    @endforeach
                </div></p>
            @endif

            <a class="btn btn-primary @if($matches_scheduled > 0) disabled @endif" href="{{ route('create-round', $tournament->id) }}">Add round</a>
        </div>
    </div>
</div>
@endsection