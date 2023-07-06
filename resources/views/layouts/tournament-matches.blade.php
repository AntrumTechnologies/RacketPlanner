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
                        @if ($tournamentMatches[$matchTime][$i]->player3 != null && $tournamentMatches[$matchTime][$i]->player4 != null)
                            <td><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player1_id) }}">{{ $tournamentMatches[$matchTime][$i]->player1 }}</a> & <a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player2_id) }}">{{ $tournamentMatches[$matchTime][$i]->player2 }}</a> vs.<br />
                            <a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player3_id) }}">{{ $tournamentMatches[$matchTime][$i]->player3 }}</a> & <a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player4_id) }}">{{ $tournamentMatches[$matchTime][$i]->player4 }}</a></td>
                        @else
                            <td><a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player1_id) }}">{{ $tournamentMatches[$matchTime][$i]->player1 }}</a> vs. <a href="{{ route('user-details', $tournamentMatches[$matchTime][$i]->player2_id) }}">{{ $tournamentMatches[$matchTime][$i]->player2 }}</a></td>
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