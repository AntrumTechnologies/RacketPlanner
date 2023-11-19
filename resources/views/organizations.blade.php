@extends('layouts.app', ['title' => 'Organizations'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mb-5">
            <h2>Organizations</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Organizations</li>
                </ol>
            </nav>

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