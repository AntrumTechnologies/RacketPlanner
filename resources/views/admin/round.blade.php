@extends('layouts.app', ['title' => $round->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit Round</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('tournaments') }}">Tournaments</a></li>
                    <li class="breadcrumb-item" aria-current="page">Tournament</li>
                    <li class="breadcrumb-item" aria-current="page">Manage Courts and Rounds</li>
                    <li class="breadcrumb-item active" aria-current="page">Edit round</li>
                </ol>
            </nav>

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
            <h4>Round details</h4>
            <form method="post" action="{{ route('update-round') }}">
                @csrf
                
                <input type="hidden" name="id" value="{{ $round->id }}" />

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $round->name }}@endif">
                </div>

                <div class="mb-3">
                    <label for="starttime" class="form-label">Start time <span class="text-danger">*</span></label>
                    <input class="form-control @error('starttime') is-invalid @enderror" id="starttime" name="starttime" type="time" value="@if(old('starttime')){{ old('starttime') }}@else{{ $round->starttime }}@endif" placeholder="Y-m-d H:i">
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
