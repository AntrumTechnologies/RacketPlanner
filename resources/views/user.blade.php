@extends('layouts.app', ['title' => $user->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
	<div class="col-md-8">
		<h2>{{ $user->name }}</h2>
            	<img src="/{{ $user->avatar }}" class="avatar" />
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Scheduled Matches</h4>

            @if (!isset($user_matches_per_tournament[0]) || count($user_matches_per_tournament[0]) == 0)
                <p>No matches have been scheduled for this user yet.</p>
            @else
                @include('layouts.user-matches')
            @endif
        </div>
    
        <div class="col-md-8 mt-4">
            <h4>User Details</h4>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

	    <p>Email<br />
	    <span class="text-muted">{{ $user->email }}</span></p>

            <p>Rating<br />
            <span class="text-muted">{{ $user->rating }}</span></p>
        </div>

        @can('admin')
        <div class="col-md-8 mt-4">
            <h4>Update User Details</h4>

            <form method="post" action="{{ route('update-user') }}" enctype="multipart/form-data">
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
                    <label for="avatar" class="form-label">Avatar</label>
                    <input class="form-control" type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg">
		</div>

		<div class="mb-3">
			<label for="yOffset" class="form-label">Avatar crop Y offset</label>
			<div class="row">
				<div class="col-1 text-center">0 px</div>
				<div class="col-10"><input type="range" class="form-range" id="yOffset" name="yOffset" min="0" max="60" step="5" value="20" oninput="document.getElementById('output').value = this.value"></div>
				<div class="col-1 text-center">60 px</div>
			</div>
			<output id="output">20</output> px
		</div>

                <button type="submit" class="btn btn-primary" name="submit">Update</button>
            </form>
        </div>
        @endcan
</div>
@endsection
