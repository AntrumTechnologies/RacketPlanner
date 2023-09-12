@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $organization->name }}</h2>

            <!-- TODO(PATBRO): Perhaps add a contact button? Add edit button if this is your organization -->
        </div>
    </div>

    <h3>Tournaments</h3>

    <!-- TODO(PATBRO): Ability to show past tournaments, or show them at the buton? -->

    @foreach ($tournaments as $tournament)
        @include('layouts.tournament-details');
    @endforeach
</div>

