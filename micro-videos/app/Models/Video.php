<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes, Traits\Uuid;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    protected $fillable = [
        'name',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
    ];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string',
        'year_launched' => 'integer',
        'opened' => 'boolean',
        'duration' => 'integer',
    ];

    public $incrementing = false;
}