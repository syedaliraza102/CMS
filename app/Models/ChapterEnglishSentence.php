<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterEnglishSentence extends Model
{
    protected $table = 'tbl_chapter_englishsentence';
    protected $primaryKey = 'englishsentence_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['englishsentence_id'] ?? '';
    }
}
