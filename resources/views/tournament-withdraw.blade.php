@extends('layouts.app', ['title' => $tournament->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $tournament->name }}</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>Withdraw</h3>

            <p>If you want to withdraw from this tournament for whatever reason, then click the button below.</p>

            <form method="post" action="{{ route('confirm-withdraw') }}">
                @csrf
                
                <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">

                <button class="btn btn-danger" type="submit" name="submit">Withdraw definitely</button>
            </form>
            
        </div>
    </div>

</div>
@endsection