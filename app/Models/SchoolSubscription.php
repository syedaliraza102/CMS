<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolSubscription extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_school_subscription';
    protected $primaryKey = 'school_subscription_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['school_subscription_id'];
    }
}
