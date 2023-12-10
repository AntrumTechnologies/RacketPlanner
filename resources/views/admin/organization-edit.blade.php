@extends('layouts.app', ['title' => 'Edit '. $organization->name])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit details</h2>

            <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
                <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('organizations') }}">Organizations</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('organization', $organization->id) }}">{{ $organization->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit details</li>
                </ol>
            </nav>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <h3>{{ $organization->name }}</h3>
            <form method="post" action="{{ route('update-organization') }}">
                @csrf
                
                <input type="hidden" name="id" value="{{ $organization->id }}" />

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="@if(old('name')){{ old('name') }}@else{{ $organization->name }}@endif">
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                    <input class="form-control @error('location') is-invalid @enderror" id="location" name="location" type="text" value="@if(old('location')){{ old('location') }}@else{{ $organization->location }}@endif">
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Update</button>
                <a href="{{ route('organization', $organization->id) }}" class="btn btn-danger">Cancel</a>
            </form>

            <hr class="mt-4 mb-4" />

            <h3>Admins</h3>

            @if (count($admins) == 0)
                <p>No admins are assigned yet.</p>
            @else
                @if (count($admins) == 1)
                    <p>In total {{ count($admins) }} admin is assigned to your organization.</p>
                @else 
                    <p>In total {{ count($admins) }} admins are assigned to your organization.</p>
                @endif
            @endif

            <ul class="list-group">
                @foreach ($admins as $admin)
                    <a href="{{ route('user', $admin->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto" id="admin{{ $admin->id }}">
                            {{ $admin->name }}
                        </div>

                        @if ($admin->id != Auth::id())
                        <form method="post" action="{{ route('remove-admin') }}">
                            @csrf
                        
                            <input type="hidden" name="organization_id" value="{{ $organization->id }}" />
                            <input type="hidden" name="id" value="{{ $admin->id }}" />
                            <input type="hidden" name="name" value="{{ $admin->name }}" />

                            <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Remove</button>
                        </form>
                        @else
                        <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" disabled>Remove</button>
                        @endif
                    </a>
                @endforeach

                    <div class="list-group-item">
                        <form method="post" action="{{ route('assign-admin') }}">
                            @csrf
                            <input type="hidden" name="organization_id" value="{{ $organization->id }}" />

                            <div class="d-flex justify-content-between align-items-start">
                                <input type="email" class="form-control form-control-sm" name="email" placeholder="Add new admin by email..." style="max-width: 250px; margin-right: 30px;" />
                        
                                <div class="ms-auto">
                                    <button type="submit" name="submit" class="btn btn-primary" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Add</button>
                                </div>
                            </div>
                        </form>    
                    </div>
            </ul>

            <hr class="mt-4 mb-4" />

            <h3>Users</h3>

            @if (count($users) == 0)
                <p>No users are assigned yet.</p>
            @else
                @if (count($users) == 1)
                    <p>In total {{ count($users) }} user is assigned to your organization.</p>
                @else 
                    <p>In total {{ count($users) }} users are assigned to your organization.</p>
                @endif
            @endif

            <div class="alert alert-info">Go to a tournament to invite or add new users. Users will be automatically added to your organization once they join a tournament.</div>

            <ul class="list-group">
                @foreach ($users as $user)
                    <a href="{{ route('user', $user->id) }}" class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto" id="user{{ $user->id }}">
                            {{ $user->name }} ({{ $user->rating }})
                        </div>

                        @if ($user->id != Auth::id())
                        <form method="post" action="{{ route('remove-user') }}">
                            @csrf
                        
                            <input type="hidden" name="organization_id" value="{{ $organization->id }}" />
                            <input type="hidden" name="id" value="{{ $user->id }}" />
                            <input type="hidden" name="name" value="{{ $user->name }}" />

                            <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Remove</button>
                        </form>
                        @else
                        <button type="submit" name="submit" class="btn btn-danger ms-2" style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" disabled>Remove</button>
                        @endif
                    </a>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
