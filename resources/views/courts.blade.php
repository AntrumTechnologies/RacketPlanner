@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>All courts</h2>

            <a class="btn btn-primary" href="{{ route('create-tournament') }}">Add court</a>
        </div>
    </div>

    @foreach ($courts as $court)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $court->name }}
                    </div>
                    <div class="ms-auto">
                         <a href="{{ route('edit-tournament', $court->id) }}">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    <p>Type: {{ $court->type }}</p>
                    <p>Available from: {{ $court->availability_start }} to {{ $court->availability_end }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
