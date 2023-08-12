@extends('layouts.app', ['title' => 'Leaderboard '. $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Leaderboard</h2>
            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('tournament', $tournament->id) }}">Tournament</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leaderboard</li>
                </ol>
            </nav>

            <h4>{{ $tournament->name }}</h4>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <table class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Score</th>
                </tr>
                @php
                    $i = 0;
                @endphp

                @foreach ($players as $player)
                    <tr>
                        @php
                            if (!isset($previous_player_score) || $player->points != $previous_player_score) {
                                $i++;
                                echo '<td>'. $i .'</td>';
                            } else {
                                echo '<td></td>';
                            }

                            $previous_player_score = $player->points;
                        @endphp
                        <td><a href="{{ route('user', $player->user_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $player->user_name }}</a></td>
                        <td>{{ $player->points }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
