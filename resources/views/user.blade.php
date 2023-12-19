@extends('layouts.app', ['title' => $user->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <img src="/{{ $user->avatar }}" class="avatar mb-2" />
            <h2>{{ $user->name }}</h2>

            @if ($user->id == Auth::id() || Auth::user()->can('superuser'))
            <p><a class="btn btn-primary" href="{{ route('edit-user', $user->id) }}">Edit user details</a></p>
            @endif

            <p>Email<br />
            <span class="text-muted">{{ $user->email }}</span></p>

            <p>Rating<br />
            <span class="text-muted">{{ $user->rating }}</span></p>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Scheduled Matches</h3>

            @if ($count_matches == 0)
                <p>No matches have been scheduled for this user yet.</p>
            @else
                @include('layouts.user-matches')
            @endif
        </div>
    </div>
</div>
@endsection
