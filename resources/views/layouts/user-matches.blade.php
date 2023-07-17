@foreach ($matches->keys() as $matchTime)
<div class="card mb-4">
    <div class="card-header">
        Round 0
    </div>

    <div class="card-body">
        <h5>{{ $matches[$matchTime][0]->court }}</h5>

        <div class="row">
            <div class="col-sm-9">
                @if ($matches[$matchTime][0]->player3 != null && $matches[$matchTime][0]->player4 != null)
                    <a href="{{ route('user-details', $matches[$matchTime][0]->player1_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $matches[$matchTime][0]->player1 }}</a><br />
                    <a href="{{ route('user-details', $matches[$matchTime][0]->player2_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $matches[$matchTime][0]->player2 }}</a>
                @else
                    <a href="{{ route('user-details', $matches[$matchTime][0]->player1_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $matches[$matchTime][0]->player1 }}</a><br />
                @endif
            </div>
            <div class="col-sm-3 justify-content-center align-self-center">
                @if ($matches[$matchTime][0]->score1_2 == "")
                    <input class="form-control form-control-sm" type="number" name="score1_2" placeholder="Score">
                @else
                    {{ $matches[$matchTime][0]->score1_2 }}
                @endif
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-sm-9">
                @if ($matches[$matchTime][0]->player3 != null && $matches[$matchTime][0]->player4 != null)
                    <a href="{{ route('user-details', $matches[$matchTime][0]->player3_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $matches[$matchTime][0]->player3 }}</a><br />
                    <a href="{{ route('user-details', $matches[$matchTime][0]->player4_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $matches[$matchTime][0]->player4 }}</a></td>
                @else
                    <a href="{{ route('user-details', $matches[$matchTime][0]->player2_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $matches[$matchTime][0]->player2 }}</a>
                @endif
            </div>
            <div class="col-sm-3 justify-content-center align-self-center">
                @if ($matches[$matchTime][0]->score3_4 == "")
                    <input class="form-control form-control-sm" type="number" name="score3_4" placeholder="Score">
                @else
                    {{ $matches[$matchTime][0]->score3_4 }}
                @endif
            </div>
        </div>

        @if ($matches[$matchTime][0]->score1_2 == "")
        <div class="row mt-2">
            <div class="col-sm-9">
            </div>
            <div class="col-sm-3">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-sm">Save score</button>
                </div>    
            </div>
        </div>
        @endif
    </div>

    <div class="card-footer text-body-secondary">
        {{ $matchTime }}
    </div>
</div>
@endforeach