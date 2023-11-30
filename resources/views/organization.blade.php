@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $organization->name }}</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('organizations') }}">Organizations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $organization->name }}</li>
                </ol>
            </nav>

            <!-- TODO(PATBRO): Perhaps add a contact button? Add edit button if this is your organization -->
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>All tournaments</h3>

            @if ($is_user_admin)
            <a class="btn btn-primary" href="{{ route('create-tournament') }}">Create new tournament</a>
            @endif

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