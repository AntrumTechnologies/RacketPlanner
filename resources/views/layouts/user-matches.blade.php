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

@foreach ($user_matches_per_tournament as $matches)
    @foreach ($matches as $match)
        <div class="card mb-4">
            <div class="card-header d-flex">
                <div class="me-auto" style="font-size: 1.2em">
                    {{ $match->time }} @ {{ $match->court }}
                </div>
                <div class="ms-auto">
                    <a href="{{ route('match', $match->id) }}" class="small text-muted">Permalink</a>
                </div>
            </div>

            <div class="card-body">
                <form method="post" action="{{ route('save-score') }}">
                    @csrf
                    
                    <input type="hidden" name="id" value="{{ $match->id }}">

                    <div class="row">
                        <div class="col-9 mt-2">
                            <a href="{{ route('user', $match->player1a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1a }}</a><br />
                            <a href="{{ route('user', $match->player1b_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1b }}</a>
                        </div>
                        <div class="col-3 mt-2 justify-content-center align-self-center">
                            @if ($match->score1 == "")
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
                            <a href="{{ route('user', $match->player2a_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2a }}</a><br />
                            <a href="{{ route('user', $match->player2b) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2b }}</a>
                        </div>
                        <div class="col-3 mt-2 justify-content-center align-self-center">
                            @if ($match->score2 == "")
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

                    @if ($match->score1 == "")
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
            </div>
        </div>
    @endforeach
@endforeach