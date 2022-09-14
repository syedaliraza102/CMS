<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterMultipleChoice extends Model
{
    protected $table = 'tbl_chapter_multiple_choice';
    protected $primaryKey = 'multiple_choice_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['multiple_choice_id'] ?? '';
    }
}
