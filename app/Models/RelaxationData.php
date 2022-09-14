<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelaxationData extends Model
{

    protected $table = 'tbl_relaxation_data';
    protected $primaryKey = 'relaxation_data_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['relaxation_data_id'];
    }


    public function game()
    {
        return $this->hasOne(Game::class, 'game_id', 'game_id')->select(['game_id', 'game_name', 'game_icon', 'game_URL']);
    }

    public function student()
    {
        return $this->hasOne(AdminUser::class, 'id', 'student_id')->select(['firstname', 'lastname', 'user_name', 'id']);
    }
}
