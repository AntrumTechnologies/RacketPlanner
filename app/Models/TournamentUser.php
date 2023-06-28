<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentUser extends Model
{
    use HasFactory;

    protected $table = 'tournaments_users';

    protected $fillable = [
        'tournament',
        'user',
    ];
}
