@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $organization->name }}</h2>

            <!-- TODO(PATBRO): Perhaps add a contact button? Add edit button if this is your organization -->
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>All tournaments</h3>

            @can('admin')
            <a class="btn btn-primary" href="{{ route('create-tournament') }}">Create new tournament</a>
            @endcan

            @if (count($tournaments) == 0)
            <p>No tournaments have been created yet.</p>
            @endif
        </div>

        <!-- TODO(PATBRO): Ability to show past tournaments, or show them at the buton? -->

        @foreach ($tournaments as $tournament)
            @include('layouts.tournament-details')
        @endforeach  
    </div>
</div>
@endsection