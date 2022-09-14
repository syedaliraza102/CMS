<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterVocabulary extends Model
{
    protected $table = 'tbl_chapter_vocabulary';
    protected $primaryKey = 'vocabulary_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['vocabulary_id'] ?? '';
    }
}
