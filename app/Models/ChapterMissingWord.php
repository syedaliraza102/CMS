<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterMissingWord extends Model
{
    protected $table = 'tbl_chapter_missing_word';
    protected $primaryKey = 'missing_word_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['missing_word_id'] ?? '';
    }
}
