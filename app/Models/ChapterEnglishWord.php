<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterEnglishWord extends Model
{
    protected $table = 'tbl_chapter_englishword';
    protected $primaryKey = 'englishword_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['englishword_id'] ?? '';
    }
}
