@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Users</h2>

            <a class="btn btn-primary" href="{{ route('create-user') }}">Create new user</a>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
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
                         <a href="{{ route('user', $user->id) }}">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            User ID
                        </div>
                        <div class="col-sm-10">
                            <span class="text-muted">{{ $user->id }}</span>
                        </div>
                        <div class="col-sm-2">
                            Email
                        </div>
                        <div class="col-sm-10">
                            <span class="text-muted">{{ $user->email }}</span>
                        </div>
                        <div class="col-sm-2">
                            Rating
                        </div>
                        <div class="col-sm-10">
                            <span class="text-muted">{{ $user->rating }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
