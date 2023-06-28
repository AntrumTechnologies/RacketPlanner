@extends('layouts.app')

@section('content')
<div class="container">
    @foreach ($tournaments as $tournament)
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $tournament }}</div>

                <div class="card-body">
                <p>{{ $tournament }}</p>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 25px"></div>
    @endforeach
</div>
@endsection
