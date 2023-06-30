@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Welcome, {{ Auth::user()->name }}!
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 25px"></div>

    @foreach ($tournaments as $tournament)
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $tournament->name }}</div>

                <div class="card-body">
                <p>{{ $tournament->email }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
