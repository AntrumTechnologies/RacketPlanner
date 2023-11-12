<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminOrganizationalAssignment extends Model
{
    use HasFactory;

    protected $table = 'admins_organizational_assignment';

    protected $fillable = [
        'organization_id',
        'user_id',
    ];
}
