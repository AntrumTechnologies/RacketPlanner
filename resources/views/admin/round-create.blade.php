@extends('layouts.app', ['title' => 'Create round'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Add new round</h2>

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
                        New Round
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Round Details</h4>

                            <form method="post" action="{{ route('store-round') }}">
                                @csrf

                                <input type="hidden" name="tournament_id" value="{{ $tournament_id }}" />
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="starttime" class="form-label">Start time</label>
                                    <input class="form-control @error('starttime') is-invalid @enderror" id="starttime" name="starttime" type="text" value="@if(old('starttime')){{ old('starttime') }}@endif" placeholder="Y-m-d H:i">
                                </div>

                                <div class="mb-3">
                                    <label for="endtime" class="form-label">End time</label>
                                    <input class="form-control @error('endtime') is-invalid @enderror" id="endtime" name="endtime" type="text" value="@if(old('endtime')){{ old('endtime') }}@endif" placeholder="Y-m-d H:i">
                                </div>

                                <button type="submit" class="btn btn-primary">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
