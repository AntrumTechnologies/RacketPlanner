@extends('layouts.app', ['title' => $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $tournament->name }}</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Enroll to this tournament</h4>

            <!-- TODO: make it possible for tournament organizers to ask special questions here (e.g. join the clinic?) -->

            <a class="btn btn-success" href="{{ route('enroll-tournament', $tournament->id) }}">Enroll!</a>
        </div>
    </div>

</div>
@endsection
