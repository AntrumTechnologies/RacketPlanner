<div class="col-md-8 mt-4">
    <div class="card">
        <div class="card-header">
            {{ $organization->name }}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    Contact person
                </div>
                <div class="col-md-3">
                    <span class="text-muted">{{ $organization->contact_person }}</span>
                </div>

                <div class="col-md-3">
                    Location<br />
                </div>
                <div class="col-md-3">
                    <span class="text-muted">{{ $organization->location }}</span>
                </div>
            </div>

            <div class="btn-group">
                @if ($organization->can_join == true)
                <a class="btn btn-primary" href="{{ route('organization-join', $organization->id) }}">Join</a>
                @endif
            </div>
        </div>
    </div>    
</div>