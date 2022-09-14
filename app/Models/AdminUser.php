<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AdminUser extends Model
{
    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('user_type', function (Builder $builder) {
            $builder->where('user_type', 'a');
        });
    }

    public function classtbl()
    {
        return $this->hasOne(ClassTbl::class, 'class_id', 'class_id');
    }

    public function activeCard()
    {
        return $this->hasOne(Cards::class, 'student_id', 'id')->where('is_avatar', 'a');
    }

    public function unusedItem()
    {
        return $this->hasOne(UnusedItem::class, 'student_id', 'id')->orderBy('created_at', 'desc');
    }
}
