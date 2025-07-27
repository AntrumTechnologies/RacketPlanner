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
                    <label for="y_offset" class="form-label">Avatar crop offset</label>
                    <div class="row">
                        <div class="col-1 text-center">0 px</div>
                        <div class="col-10"><input type="range" class="form-range" id="y_offset" name="y_offset" min="0" max="90" step="5" value="45" oninput="document.getElementById('output').value = this.value"></div>
                        <div class="col-1 text-center">90 px</div>
                    </div>
                    <output id="output">45</output> px
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

                    <select id="rating" class="form-control @error('rating') is-invalid @enderror" name="rating" required>
                        <option value="" @if(empty($user->rating)) selected @endif>Please select...</option>
                        <option value="10" @if($user->rating == 10) selected @endif>10 (beginner)</option>
                        <option value="9" @if($user->rating == 9) selected @endif>9</option>
                        <option value="8" @if($user->rating == 8) selected @endif>8</option>
                        <option value="7" @if($user->rating == 7) selected @endif>7</option>
                        <option value="6" @if($user->rating == 6) selected @endif>6</option>
                        <option value="5" @if($user->rating == 5) selected @endif>5</option>
                        <option value="4" @if($user->rating == 4) selected @endif>4</option>
                        <option value="3" @if($user->rating == 3) selected @endif>3</option>
                        <option value="2" @if($user->rating == 2) selected @endif>2 (expert)</option>
                    </select>

                    @error('rating')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Update</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
