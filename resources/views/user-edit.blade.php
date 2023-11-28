@extends('layouts.app', ['title' => $user->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
	<div class="col-md-8">
        <img src="/{{ $user->avatar }}" class="avatar mb-2" />
		<h2>{{ $user->name }}</h2>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 mt-4">
            <h4>Update User Details</h4>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form method="post" action="{{ route('update-user') }}" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="id" value="{{ $user->id }}" />

                <div class="mb-3">
                    <label for="avatar" class="form-label">Avatar</label>
                    <input class="form-control" type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg">
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $user->name }}@endif">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="@if(old('email')){{ old('email') }}@else{{ $user->email }}@endif">
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                    <input class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" type="number" value="@if(old('rating')){{ old('rating') }}@else{{ $user->rating }}@endif">
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Update</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
