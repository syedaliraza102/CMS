<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_school';
    protected $primaryKey = 'school_id'; // or null

    protected $appends = ['id'];

    protected $casts = [
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
    ];

    public function getIdAttribute($value)
    {
        return $this->attributes['school_id'];
    }
}
