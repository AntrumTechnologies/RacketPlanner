@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Explore</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <h3>Tournaments</h3>
            @foreach ($tournaments as $tournament)
                @include('layouts.tournament-details');
            @endforeach
        </div>
        
        <div class="col-6">
            <h3>Organizations</h3>
            @foreach ($organizations as $organization)
                @include('layouts.organization-details');
            @endforeach
        </div>
    </div>
</div>