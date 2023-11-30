<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tournaments';

    protected $fillable = [
        'name',
        'datetime_start',
        'datetime_end',
        'matches',
        'duration_m',
        'type',
        'allow_singles',
        'time_between_matches_m',
        'owner_organization_id',
        'enroll_until',
        'max_players',
        'leaderboard',
        'description',
        'location',
        'location_link',
        'change_to_courts_rounds',
        'change_to_players',
        'public_link',
        'hidden',
    ];
}
