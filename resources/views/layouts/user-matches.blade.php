@foreach ($user_matches_per_tournament as $match)
    <div class="card mb-4">
        <div class="card-header d-flex" style="font-size: 1.2em">
            <div class="me-auto">
                {{ $match->time }} @ {{ $match->court }}
            </div>
            <div class="ms-auto">
                <a href="{{ route('match', $match->id) }}"><i class="bi bi-link-45deg" style="font-size: 1rem;"></i></a>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('save-score') }}">
                @csrf
                
                <input type="hidden" name="id" value="{{ $match->id }}">

                <div class="row">
                    <div class="col-9 mt-2">
                        @if ($match->player1a_id != $match->player1b_id)
                        <img src="/{{ $match->player1a_avatar }}" class="avatar-sm" />
                        <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a> ({{ $match->player1a_rating }})
                        <br />
                        <img src="/{{ $match->player1b_avatar }}" class="avatar-sm mt-2" />
                        <a href="{{ route('user', $match->player1b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1b }}</a> ({{ $match->player1b_rating }})
                        @else
                        <img src="/{{ $match->player1a_avatar }}" class="avatar-sm" />
                        <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a> ({{ $match->player1a_rating }})
                        @endif
                    </div>
                    <div class="col-3 mt-2 justify-content-center align-self-center">
                        @if ($match->score1 == "" && date('Y-m-d') == date('Y-m-d', strtotime($match->datetime)))
                            <input class="form-control form-control-sm" type="number" name="score1" placeholder="Score">
                        @else
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
                    <div class="col-9 mt-2">
                        @if ($match->player2a_id != $match->player2b_id)
                        <img src="/{{ $match->player2a_avatar }}" class="avatar-sm" />
                        <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a> ({{ $match->player2a_rating }})
                        <br />
                        <img src="/{{ $match->player2b_avatar }}" class="avatar-sm mt-2" />
                        <a href="{{ route('user', $match->player2b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2b }}</a> ({{ $match->player2b_rating }})
                        @else
                        <img src="/{{ $match->player2a_avatar }}" class="avatar-sm" />
                        <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a> ({{ $match->player2a_rating }})
                        @endif
                    </div>
                    <div class="col-3 mt-2 justify-content-center align-self-center">
                        @if ($match->score2 == "" && date('Y-m-d') == date('Y-m-d', strtotime($match->datetime)))
                            <input class="form-control form-control-sm" type="number" name="score2" placeholder="Score">
                        @else
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

                @if ($match->score1 == "" && date('Y-m-d') == date('Y-m-d', strtotime($match->datetime)))
                <div class="row mt-2">
                    <div class="col-sm-9">
                    </div>
                    <div class="col-sm-3">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Save score</button>
                        </div>    
                    </div>
                </div>
                @endif
            </form>

            <a href="{{ route('tournament', $match->tournament_id) }}?showround=all#slot{{ $match->slot }}" class="d-flex justify-content-center btn btn-sm btn-secondary mt-2">Go to match in tournament</a>
        </div>
    </div>
@endforeach

@foreach ($user_clinics as $clinic)
    <div class="card mb-4">
        <div class="card-header d-flex">
            <div class="me-auto" style="font-size: 1.2em">
                {{ $clinic->time }} @ {{ $clinic->court }}
            </div>
        </div>
        <div class="card-body">
            @foreach ($clinic->players as $player)
                <a href="{{ route('user', $player->user_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $player->user_name }}</a><br />
            @endforeach
        </div>
    </div>
@endforeach