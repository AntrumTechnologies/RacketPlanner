@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Match</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="card mb-4">
            <div class="card-header d-flex">
                <div class="me-auto">
                    {{ $match->round }}
                </div>

                <div class="ms-auto">
                    <a href="{{ route('match-details', $match->id) }}">Permalink</a>
                </div>
            </div>

            <div class="card-body">
                <h5>{{ $match->court }}</h5>

                <div class="row">
                    <div class="col-sm-9">
                        @if ($match->player3 != null && $match->player4 != null)
                            <a href="{{ route('user-details', $match->player1_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1 }}</a><br />
                            <a href="{{ route('user-details', $match->player2_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2 }}</a>
                        @else
                            <a href="{{ route('user-details', $match->player1_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player1 }}</a><br />
                        @endif
                    </div>
                    <div class="col-sm-3 justify-content-center align-self-center">
                        @if ($match->score1_2 == "" && $$match->yours == true)
                            <input class="form-control form-control-sm" type="number" name="score1_2" placeholder="Score">
                        @else
                            {{ $match->score1_2 }}
                        @endif
                    </div>
                </div>

                <hr/>

                <div class="row">
                    <div class="col-sm-9">
                        @if ($match->player3 != null && $match->player4 != null)
                            <a href="{{ route('user-details', $match->player3_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player3 }}</a><br />
                            <a href="{{ route('user-details', $match->player4_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player4 }}</a></td>
                        @else
                            <a href="{{ route('user-details', $match->player2_id) }}" class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">{{ $match->player2 }}</a>
                        @endif
                    </div>
                    <div class="col-sm-3 justify-content-center align-self-center">
                        @if ($match->score3_4 == "" && $$match->yours == true)
                            <input class="form-control form-control-sm" type="number" name="score3_4" placeholder="Score">
                        @else
                            {{ $match->score3_4 }}
                        @endif
                    </div>
                </div>

                @if ($match->score1_2 == "" && $$match->yours == true)
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
    </div>
    
</div>
@endsection
