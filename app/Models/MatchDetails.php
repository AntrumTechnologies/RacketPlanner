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
        'tournament',
        'player1',
        'player2',
        'player3',
        'player4',
        'court',
        'datetime',
        'score1_2',
        'score3_4',
    ];
}
