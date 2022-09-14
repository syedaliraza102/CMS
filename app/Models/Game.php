<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_games';
    protected $primaryKey = 'game_id'; // or null

    protected $appends = ['id'];

    protected $casts = ['school_id' => 'json'];

    public function getIdAttribute($value)
    {
        return $this->attributes['game_id'];
    }
}
