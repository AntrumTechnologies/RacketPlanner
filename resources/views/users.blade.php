@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>All users</h2>

            <a class="btn btn-primary" href="#">Add user</a>
        </div>
    </div>

    @foreach ($users as $user)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $user->name }}
                    </div>
                    <div class="ms-auto">
                         <a href="{{ route('edit-tournament', $user->id) }}">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    <p>ID: {{ $user->id }}</p>
                    <p>Email: {{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
