<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfflineHomework extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_offline_homework';
    protected $primaryKey = 'homework_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['homework_id'];
    }

    public function classtbl()
    {
        return $this->hasOne(ClassTbl::class, 'class_id', 'class_id');
    }

    public function teacher()
    {
        return $this->hasOne(AdminUser::class, 'id', 'teacher_id');
    }
}
