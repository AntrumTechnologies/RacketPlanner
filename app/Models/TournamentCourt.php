<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentCourt extends Model
{
    use HasFactory;

    protected $table = 'tournaments_courts';

    protected $fillable = [
        'tournament',
        'court',
    ];
}
