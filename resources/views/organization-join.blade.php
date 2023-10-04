@extends('layouts.app', ['title' => 'Home'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $organization->name }}</h2>
        </div>
    </div>

    <!-- TODO(PATBRO): make sure this page is only accessible via a special (unique) link -->

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Join this organization</h4>
            <a class="btn btn-primary" href="{{ route('organization-join', $organization->id) }}">Join!</a>
        </div>
    </div>
</div>

