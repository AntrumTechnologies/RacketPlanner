@extends('layouts.app', ['title' => 'Match Details'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Match Details</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('tournament', $match->tournament_id) }}">Tournament</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Match details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>{{ $match->tournament_name }}</h4>

            <div class="card mb-4">
                <div class="card-header d-flex">
                    <div class="me-auto" style="font-size: 1.2em">
                        {{ $match->time }} @ {{ $match->court }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a><br />
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
                            <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a><br />
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
        </div>
    </div>
    
</div>
@endsection
