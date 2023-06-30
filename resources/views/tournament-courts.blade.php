@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournament Courts</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="me-auto">
                        {{ $tournament->name }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Assign Court</h4>

                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="court" class="form-label">Court</label>
                                    <select class="form-select" id="court" name="court">
                                        <option value="">Select a court...</option>
                                        @foreach ($courts as $court)
                                            <option value="{{ $court->id }}">{{ $court->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Assign</button>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h4>Assigned Courts</h4>
                            
                            <div class="list-group">
                                @foreach ($tournamentCourts as $court)
                                <div href="#" class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        {{ $court->name }} 
                                    </div>
                                    @if ($court->type != '') <span class="badge bg-primary rounded-pill">{{ ucfirst($court->type) }}</span> @endif
                                   <a href="#" class="badge bg-danger rounded-pill">Remove</a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
