<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterRolePlay extends Model
{
    protected $table = 'tbl_chapter_role_play';
    protected $primaryKey = 'role_play_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['role_play_id'] ?? '';
    }
}
