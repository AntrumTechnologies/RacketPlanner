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
        'max_diff_rating',
        'time_between_matches_m',
        'created_by',
    ];
}
