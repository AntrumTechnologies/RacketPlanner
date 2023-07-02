@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Court Details</h2>

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
                        {{ $court->name }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Update Court Details</h4>

                            <form method="post" action="{{ route('update-court-details') }}">
                                @csrf
                                
                                <input type="hidden" name="id" value="{{ $court->id }}" />

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $court->name }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">Select a type...</option>
                                        <option value="tennis" @if (old('type') == "tennis") selected @elseif ($court->type == "tennis") selected @endif>Tennis</option>
                                        <option value="padel" @if (old('type') == "padel") selected @elseif ($court->type == "padel") selected @endif>Padel</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="availability_start" class="form-label">Availability start</label>
                                    <input class="form-control @error('availability_start') is-invalid @enderror" id="availability_start" name="availability_start" type="text" placeholder="Y-m-d H:i" value="@if(old('availability_start')){{ old('availability_start') }}@else{{ $court->availability_start }}@endif">
                                </div>

                                <div class="mb-3">
                                    <label for="availability_end" class="form-label">Availability end</label>
                                    <input class="form-control @error('availability_end') is-invalid @enderror" id="availability_end" name="availability_end" type="text" placeholder="Y-m-d H:i" value="@if(old('availability_end')){{ old('availability_end') }}@else{{ $court->availability_end }}@endif">
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
