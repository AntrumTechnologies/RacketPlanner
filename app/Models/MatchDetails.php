<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'player1a_id',
        'player1b_id',
        'player2a_id',
        'player2b_id',
        'disabled',
        'priority',
        'datetime',
        'score1',
        'score2',
    ];
}
