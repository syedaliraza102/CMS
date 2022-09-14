<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelaxationClass extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_relaxation_class';
    protected $primaryKey = 'relaxation_class_id'; // or null

    protected $appends = ['id'];
    protected $casts = ['student_ids' => 'json'];

    public function getIdAttribute($value)
    {
        return $this->attributes['relaxation_class_id'];
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->display_order = $model->relaxation_class_id;
            $model->update();
        });
    }

    public function activated_user()
    {
        return $this->hasOne(AdminUser::class, 'id', 'activated_by')->select(['firstname', 'lastname', 'user_name', 'id']);
    }

    public function deactivated_user()
    {
        return $this->hasOne(AdminUser::class, 'id', 'deactivated_by')->select(['firstname', 'lastname', 'user_name', 'id']);
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'game_id', 'game_id')->select(['game_id', 'game_name', 'game_icon', 'game_URL']);
    }

    public function fragment()
    {
        return $this->hasOne(Fragment::class, 'fragment_id', 'fragment_id');
    }
}
