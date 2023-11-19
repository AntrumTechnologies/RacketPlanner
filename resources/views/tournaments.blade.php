@extends('layouts.app', ['title' => 'Tournaments'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournaments</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tournaments</li>
                </ol>
            </nav>

            @if (count($tournaments) == 1)
            <p>There is 1 tournament to show.</p>
            @else
            <p>There are {{ count($tournaments) }} tournaments to show.</p>
            @endif

            @can('admin')
            <a class="btn btn-primary" href="{{ route('create-tournament') }}">Create new tournament</a>
            @endcan

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    @if (count($tournaments) == 0)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <p>No tournaments have been created yet.</p>
        </div>
    </div>
    @endif

    @foreach ($tournaments as $tournament)
    <div class="row justify-content-center mt-4">
        @include('layouts.tournament-details')
    </div>
    @endforeach
</div>
@endsection
