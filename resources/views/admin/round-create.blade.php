@extends('layouts.app', ['title' => 'Add round'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Add round</h2>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h4>Round details</h4>
            <form method="post" action="{{ route('store-round') }}">
                @csrf

                <input type="hidden" name="tournament_id" value="{{ $tournament_id }}" />
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@endif">
                </div>

                <div class="mb-3">
                    <label for="starttime" class="form-label">Start time <span class="text-danger">*</span></label>
                    <input class="form-control @error('starttime') is-invalid @enderror" id="starttime" name="starttime" type="text" value="@if(old('starttime')){{ old('starttime') }}@endif" placeholder="Y-m-d H:i">
                </div>

                <button type="submit" class="btn btn-primary">Add</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
