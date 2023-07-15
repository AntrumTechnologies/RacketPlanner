@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>All courts</h2>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <a class="btn btn-primary" href="{{ route('create-court') }}">Create new court</a>
        </div>
    </div>

    @foreach ($courts as $court)
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $court->name }}
                    </div>
                    <div class="ms-auto">
                         <a href="{{ route('court-details', $court->id) }}">Edit</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            Type
                        </div>
                        <div class="col-sm-10">
                            <span class="text-muted">{{ ucfirst($court->type) }}</span>
                        </div>
                        <div class="col-sm-2">
                            From
                        </div>
                        <div class="col-sm-10">
                            <span class="text-muted">{{ $court->availability_start }}</span>
                        </div>
                        <div class="col-sm-2">
                            To
                        </div>
                        <div class="col-sm-10">
                            <span class="text-muted">{{ $court->availability_end }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
