@extends('layouts.app', ['title' => $court->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit Court</h2>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $court->name }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Update Court Details</h4>

                            <form method="post" action="{{ route('update-court') }}">
                                @csrf
                                
                                <input type="hidden" name="id" value="{{ $court->id }}" />

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $court->name }}@endif">
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h4>Delete Court</h4>

                            <form method="post" action="{{ route('delete-court') }}">
                                @csrf
                                
                                <input type="hidden" name="id" value="{{ $court->id }}" />
                                <input type="hidden" name="tournament_id" value="{{ $court->tournament_id }}" />

                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
