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
                        @if ($match->player3 == null && $match->player4 == null)
                            <td>{{ $match->player1 }} vs. {{ $match->player2 }} vs. {{ $match->player3 }} vs. {{ $match->player4 }}</td>
                        @else
                        <td>{{ $match->player1 }} vs. {{ $match->player2 }}</td>
                        @endif
                    @endif
                @endforeach
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>