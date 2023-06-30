@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>All tournaments</h2>

            <a class="btn btn-primary" href="#">Add tournament</a>
        </div>
    </div>

    @foreach ($tournaments as $tournament)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $tournament->name }}
                    </div>
                    <div class="ms-auto">
                         <a href="#">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    @include('layouts.tournament-details')

                    <a class="btn btn-secondary" href="{{ route('tournament-details', $tournament->id) }}">View tournament details</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
