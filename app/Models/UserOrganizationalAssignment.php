<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrganizationalAssignment extends Model
{
    use HasFactory;

    protected $table = 'users_organizational_assignment';

    protected $fillable = [
        'organization_id',
        'user_id',
    ];
}
