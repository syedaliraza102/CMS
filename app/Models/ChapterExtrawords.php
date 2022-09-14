<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterExtrawords extends Model
{
    protected $table = 'tbl_chapter_extrawords';
    protected $primaryKey = 'extra_word_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['extra_word_id'] ?? '';
    }
}
