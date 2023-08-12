@extends('layouts.app', ['title' => 'Create user'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Create New User</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8 mt-4">
            <h4>User Details</h4>

            <form method="post" action="{{ route('store-user') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="@if(old('email')){{ old('email') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <input class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" type="number" value="@if(old('rating')){{ old('rating') }}@endif">
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Create</button>
            </form>
        </div>
</div>
@endsection
