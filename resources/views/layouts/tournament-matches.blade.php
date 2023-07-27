<table class="table">
    <thead>
        <tr>
            <th>Time</th>
            @foreach ($tournamentCourts as $court)
            <th>{{ $court->name }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($tournamentMatches->keys() as $matchTime)
        <tr>
            <td>{{ $matchTime }}</td>

                @php
                    $i = 0;
                @endphp

                @foreach ($tournamentCourts as $court)
                    @if ($i >= count($tournamentMatches[$matchTime]))
                        <td class="text-muted">Available</td>
                        @php
                            continue;
                        @endphp
                    @endif

                    @if ($court->name == $tournamentMatches[$matchTime][$i]->court)
                        @if ($tournamentMatches[$matchTime][$i]->player1b != null && $tournamentMatches[$matchTime][$i]->player2b != null)
                            <td><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player1a_id) }}">{{ $tournamentMatches[$matchTime][$i]->player1a }}</a><br /><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player1b_id) }}">{{ $tournamentMatches[$matchTime][$i]->player1b }}</a> vs.<br />
                            <a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player2a_id) }}">{{ $tournamentMatches[$matchTime][$i]->player2a }}</a><br /><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player2b_id) }}">{{ $tournamentMatches[$matchTime][$i]->player2b }}</a></td>
                        @else
                            <td><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player1a_id) }}">{{ $tournamentMatches[$matchTime][$i]->player1a }}</a> vs.<br /><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player2a_id) }}">{{ $tournamentMatches[$matchTime][$i]->player2a }}</a></td>
                        @endif

                        @php
                            $i++;
                        @endphp
                    @else 
                        <td class="text-muted">Available</td>
                    @endif
                @endforeach
        </tr>
        @endforeach
    </tbody>
</table>