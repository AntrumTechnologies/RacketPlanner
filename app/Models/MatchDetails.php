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
        'player1a',
        'player1b',
        'player2a',
        'player2b',
        'disabled',
        'priority',
        'datetime',
        'score1',
        'score2',
    ];
}
