<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterQA extends Model
{
    protected $table = 'tbl_chapter_qa';
    protected $primaryKey = 'qa_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['qa_id'] ?? '';
    }
}
