@extends('layouts.app', ['title' => 'Organizations'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mb-5">
            <h2>Organizations</h2>

            @if (count($organizations) == 1)
            <p>You are part of 1 organization.</p>
            @else
            <p>You are part of {{ count($organizations) }} organizations.</p>
            @endif

            @can('superuser')
            <a class="btn btn-primary" href="{{ route('create-organization') }}">Create new organization</a>
            @endcan
        </div>
    </div>

    @foreach ($organizations as $organization)
    <div class="row justify-content-center">
        <div class="col-md-8 mb-5">
            <h4>{{ $organization->name }}</h4>

            <p>Location: {{ $organization->location}}</p>

            <a class="btn btn-primary" href="{{ route('organization', $organization->id) }}">View organization' tournaments</a>
        </div>
    </div>
    @endforeach
</div>
@endsection