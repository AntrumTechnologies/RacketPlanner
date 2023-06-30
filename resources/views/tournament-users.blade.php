@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Tournament Players</h2>
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
                            <h4>Assign Player</h4>

                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <select class="form-select" id="name" name="name">
                                        <option value="">Select a user...</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Assign</button>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <h4>Assigned Players</h4>
                            
                            <div class="list-group">
                                @foreach ($tournamentUsers as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-start" title="{{ $user->email }}">
                                    <div class="ms-2 me-auto">
                                        {{ $user->name }} 
                                    </div>
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
