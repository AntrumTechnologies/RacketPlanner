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

            @foreach ($tournamentMatches[$matchTime] as $match)
                @foreach ($tournamentCourts as $court)
                    @if ($court->name == $match->court)
                        @if ($match->player3 != null && $match->player4 != null)
                            <td><a href="{{ route('user-details', $match->player1_id) }}">{{ $match->player1 }}</a> & <a href="{{ route('user-details', $match->player2_id) }}">{{ $match->player2 }}</a> vs.<br />
                            <a href="{{ route('user-details', $match->player3_id) }}">{{ $match->player3 }}</a> & <a href="{{ route('user-details', $match->player4_id) }}">{{ $match->player4 }}</a></td>
                        @else
                        <td><a href="{{ route('user-details', $match->player1_id) }}">{{ $match->player1 }}</a> vs. <a href="{{ route('user-details', $match->player2_id) }}">{{ $match->player2 }}</a></td>
                        @endif
                    @endif
                @endforeach
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>