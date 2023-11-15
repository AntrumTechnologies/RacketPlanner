@extends('layouts.app', ['title' => 'Create organization'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Create New Organization</h2>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        New Organization
                    </div>
                </div>

                <div class="card-body">
                    <h4>Organization Details</h4>

                    <form method="post" action="{{ route('store-organization') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input class="form-control @error('location') is-invalid @enderror" id="location" name="location" type="text" value="@if(old('location')){{ old('location') }}@endif">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Assign owner by email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="@if(old('email')){{ old('email') }}@endif">
                        </div>

                        <button type="submit" class="btn btn-primary" name="submit">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
