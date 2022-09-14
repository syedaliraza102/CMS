<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fragment extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_fragment';
    protected $primaryKey = 'fragment_id'; // or null

    protected $appends = ['id'];

    protected $casts = ['package_ids' => 'json'];


    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->display_order = $model->fragment_id;
            $model->update();
        });
    }

    public function getIdAttribute($value)
    {
        return $this->attributes['fragment_id'];
    }

    public function vocabulary()
    {
        return $this->hasMany(ChapterVocabulary::class, 'fragment_id', 'fragment_id')->select(['vocabulary_english', 'vocabulary_korean', 'vocabulary_image_urls', 'fragment_id']);
    }

    public function qa()
    {
        return $this->hasMany(ChapterQA::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'qa_english_question', 'qa_english_answer', 'qa_korean_question', 'qa_korean_answer', 'qa_image_urls']);
    }

    public function role_play()
    {
        return $this->hasMany(ChapterRolePlay::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'role_play_english_A', 'role_play_english_B', 'role_play_korean_A', 'role_play_korean_B', 'role_play_image_urls']);
    }

    public function missing_words()
    {
        return $this->hasMany(ChapterMissingWord::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'missing_word_sentences', 'missing_word_image_urls']);
    }

    public function mc()
    {
        return $this->hasMany(ChapterMultipleChoice::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'choice_question', 'choice_a', 'choice_b', 'choice_c', 'choice_d', 'choice_e', 'choice_f', 'option_type', 'choice_image_urls']);
    }

    public function sentence()
    {
        return $this->hasMany(ChapterExtrawords::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'extrawords_sentence', 'extrawords_word1', 'extrawords_word2', 'extrawords_word3', 'extrawords_word4', 'extrawords_word5', 'extrawords_word6', 'extrawords_word7', 'extrawords_word8', 'extrawords_image_urls']);
    }

    public function english_words()
    {
        return $this->hasMany(ChapterEnglishWord::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'englishword_word', 'englishword_image_urls']);
    }

    public function english_sentences()
    {
        return $this->hasMany(ChapterEnglishSentence::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'englishsentence_sentence', 'englishsentence_image_urls', 'englishsentence_korean']);
    }

    public function grammer()
    {
        return $this->hasMany(ChapterGrammer::class, 'fragment_id', 'fragment_id')->select(['fragment_id', 'grammer_english', 'grammer_korean', 'grammer_var1', 'grammer_var2', 'grammer_var3', 'grammer_var4', 'grammer_var5', 'grammer_var6', 'grammer_var7', 'grammer_var8', 'grammer_var9', 'grammer_var10', 'grammer_var11', 'grammer_var12', 'grammer_var13', 'grammer_var14', 'grammer_var15', 'grammer_var16']);
    }
}
