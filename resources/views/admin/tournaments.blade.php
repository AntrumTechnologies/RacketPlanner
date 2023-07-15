@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournaments</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @can('admin')
            <a class="btn btn-primary" href="{{ route('create-tournament') }}">Create new tournament</a>
            @endcan
        </div>
    </div>

    @foreach ($tournaments as $tournament)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $tournament->name }}
                </div>

                <div class="card-body">
                    @include('layouts.tournament-details')

                    <a class="btn btn-secondary" href="{{ route('tournament', $tournament->id) }}">View tournament</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
