@extends('layouts.app', ['title' => $user->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $user->name }} <img src="/{{ $user->avatar }}" class="avatar" style="margin-left: 10px" /></h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Scheduled Matches</h4>

            @if (count($matches) == 0)
            <p>No matches have been scheduled for this user yet.</p>
            @endif

            @foreach ($matches as $match)
                <div class="card mb-4">
                    <div class="card-header d-flex">
                        <div class="me-auto" style="font-size: 1.2em">
                            {{ $match->time }} @ {{ $match->court }}
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('match', $match->id) }}"><i class="bi bi-link-45deg" style="font-size: 1rem;"></i></a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-9">
                                <img src="/{{ $match->player1a_avatar }}" class="avatar-sm" />
                                <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a>
                                <br />
                                <img src="/{{ $match->player1b_avatar }}" class="avatar-sm mt-2" />
                                <a href="{{ route('user', $match->player1b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1b }}</a>
                            </div>
                            <div class="col-3 justify-content-center align-self-center">
                                @if ($match->score1 != "")
                                    {{ $match->score1 }}
                                    @php
                                        if ($match->score1 > $match->score2) {
                                    @endphp
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-trophy-fill" viewBox="0 0 16 16"><path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935z"/></svg>
                                    @php
                                        }
                                    @endphp
                                @endif
                            </div>
                        </div>

                        <hr/>

                        <div class="row">
                            <div class="col-9">
                                <img src="/{{ $match->player2a_avatar }}" class="avatar-sm" />
                                <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a>
                                <br />
                                <img src="/{{ $match->player2b_avatar }}" class="avatar-sm mt-2" />
                                <a href="{{ route('user', $match->player2b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2b }}</a>
                            </div>
                            <div class="col-3 justify-content-center align-self-center">
                                @if ($match->score2 != "")
                                    {{ $match->score2 }}
                                    @php
                                        if ($match->score2 > $match->score1) {
                                    @endphp
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-trophy-fill" viewBox="0 0 16 16"><path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935z"/></svg>
                                    @php
                                        }
                                    @endphp
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    
        <div class="col-md-8 mt-4">
            <h4>User Details</h4>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row mb-3">
                <div class="col-3">
                    Email<br />
                    Rating
                </div>
                <div class="col-9">
                    <span class="text-muted">{{ $user->email }}<br />
                    {{ $user->rating }}</span>
                </div>
            </div>
        </div>

        @can('admin')
        <div class="col-md-8 mt-4">
            <h4>Update User Details</h4>

            <form method="post" action="{{ route('update-user') }}" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="id" value="{{ $user->id }}" />

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $user->name }}@endif">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="@if(old('email')){{ old('email') }}@else{{ $user->email }}@endif">
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <input class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" type="number" value="@if(old('rating')){{ old('rating') }}@else{{ $user->rating }}@endif">
                </div>

                <div class="mb-3">
                    <label for="avatar" class="form-label">Avatar</label>
                    <input class="form-control" type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg">
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Update</button>
            </form>
        </div>
        @endcan
</div>
@endsection
