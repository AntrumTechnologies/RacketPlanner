@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>All rounds</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <a class="btn btn-primary" href="{{ route('create-round') }}">Create new round</a>
        </div>
    </div>

    
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            @foreach ($rounds as $round)
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $round->name }}
                    </div>
                    <div class="ms-auto">
                         <a href="{{ route('round-details', $round->id) }}">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
