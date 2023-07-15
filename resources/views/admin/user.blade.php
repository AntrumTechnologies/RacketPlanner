@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>User Details</h2>

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
                        {{ $user->name }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h4>Assigned tournaments</h4>

                                    <span class="text-muted">
                                        @foreach ($tournaments as $tournament)
                                            <a href="{{ route('tournament-details', $tournament->id) }}">{{ $tournament->name }}</a><br />
                                        @endforeach
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <h4>Assigned matches</h4>

                                    <span class="text-muted">
                                        @foreach ($matches as $match)
                                            {{ $match->datetime }} ({{ $match->name }})<br />
                                        @endforeach
                                    </span>
                                </div>
                            </div>

                            <h4>Update User Details</h4>

                            <form method="post" action="{{ route('update-user-details') }}">
                                @csrf
                                
                                <input type="hidden" name="id" value="{{ $user->id }}" />

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $user->name }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="@if(old('email')){{ old('email') }}@else{{ $user->email }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rating</label>
                                    <input class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" type="number" value="@if(old('rating')){{ old('rating') }}@else{{ $user->rating }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="availability_start" class="form-label">Availability start</label>
                                    <input class="form-control @error('availability_start') is-invalid @enderror" id="availability_start" name="availability_start" type="text" placeholder="Y-m-d H:i" value="@if(old('availability_start')){{ old('availability_start') }}@else{{ $user->availability_start }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="availability_end" class="form-label">Availability end</label>
                                    <input class="form-control @error('availability_end') is-invalid @enderror" id="availability_end" name="availability_end" type="text" placeholder="Y-m-d H:i" value="@if(old('availability_end')){{old('availability_end')}}@else{{ $user->availability_end}}@endif">
                                </div>

                                <button type="submit" class="btn btn-primary" name="submit">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
