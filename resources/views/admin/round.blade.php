@extends('layouts.app', ['title' => $round->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit Round</h2>

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
                        {{ $round->name }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" action="{{ route('update-round') }}">
                                @csrf
                                
                                <input type="hidden" name="id" value="{{ $round->id }}" />

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $round->name }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="starttime" class="form-label">Start time</label>
                                    <input class="form-control @error('starttime') is-invalid @enderror" id="starttime" name="starttime" type="text" value="@if(old('starttime')){{ old('starttime') }}@else{{ $round->starttime }}@endif" placeholder="Y-m-d H:i">
                                </div>

                                <div class="mb-3">
                                    <label for="endtime" class="form-label">End time</label>
                                    <input class="form-control @error('endtime') is-invalid @enderror" id="endtime" name="endtime" type="text" value="@if(old('endtime')){{ old('endtime') }}@else{{ $round->endtime }}@endif" placeholder="Y-m-d H:i">
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
