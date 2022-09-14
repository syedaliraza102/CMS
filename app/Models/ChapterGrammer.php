<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChapterGrammer extends Model
{
    protected $table = 'tbl_chapter_grammer';
    protected $primaryKey = 'grammer_id'; // or null

    protected $appends = ['id'];

    public function getIdAttribute($value)
    {
        return $this->attributes['grammer_id'] ?? '';
    }

    public function getGrammerVar1Attribute($value)
    {
        if (empty($this->attributes['grammer_var1'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var1']);
    }

    public function setGrammerVar1Attribute($value)
    {
        $this->attributes['grammer_var1'] = !empty($value) ? implode('~', $value) : '';
    }


    public function getGrammerVar2Attribute($value)
    {
        if (empty($this->attributes['grammer_var2'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var2']);
    }

    public function setGrammerVar2Attribute($value)
    {
        $this->attributes['grammer_var2'] = !empty($value) ? implode('~', $value) : '';
    }

    public function getGrammerVar3Attribute($value)
    {
        if (empty($this->attributes['grammer_var3'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var3']);
    }

    public function setGrammerVar3Attribute($value)
    {
        $this->attributes['grammer_var3'] = !empty($value) ? implode('~', $value) : '';
    }

    public function getGrammerVar4Attribute($value)
    {
        if (empty($this->attributes['grammer_var4'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var4']);
    }

    public function setGrammerVar4Attribute($value)
    {
        $this->attributes['grammer_var4'] = !empty($value) ? implode('~', $value) : '';
    }

    public function getGrammerVar5Attribute($value)
    {
        if (empty($this->attributes['grammer_var5'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var5']);
    }

    public function setGrammerVar5Attribute($value)
    {
        $this->attributes['grammer_var5'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar6Attribute($value)
    {
        if (empty($this->attributes['grammer_var6'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var6']);
    }

    public function setGrammerVar6Attribute($value)
    {
        $this->attributes['grammer_var6'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar7Attribute($value)
    {
        if (empty($this->attributes['grammer_var7'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var7']);
    }

    public function setGrammerVar7Attribute($value)
    {
        $this->attributes['grammer_var7'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar8Attribute($value)
    {
        if (empty($this->attributes['grammer_var8'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var8']);
    }

    public function setGrammerVar8Attribute($value)
    {
        $this->attributes['grammer_var8'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar9Attribute($value)
    {
        if (empty($this->attributes['grammer_var9'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var9']);
    }

    public function setGrammerVar9Attribute($value)
    {
        $this->attributes['grammer_var9'] = !empty($value) ? implode('~', $value) : '';
    }


    public function getGrammerVar10Attribute($value)
    {
        if (empty($this->attributes['grammer_var10'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var10']);
    }

    public function setGrammerVar10Attribute($value)
    {
        $this->attributes['grammer_var10'] = !empty($value) ? implode('~', $value) : '';
    }


    public function getGrammerVar11Attribute($value)
    {
        if (empty($this->attributes['grammer_var11'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var11']);
    }

    public function setGrammerVar11Attribute($value)
    {
        $this->attributes['grammer_var11'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar12Attribute($value)
    {
        if (empty($this->attributes['grammer_var12'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var12']);
    }

    public function setGrammerVar12Attribute($value)
    {
        $this->attributes['grammer_var12'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar13Attribute($value)
    {
        if (empty($this->attributes['grammer_var13'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var13']);
    }

    public function setGrammerVar13Attribute($value)
    {
        $this->attributes['grammer_var13'] = !empty($value) ? implode('~', $value) : '';
    }




    public function getGrammerVar14Attribute($value)
    {
        if (empty($this->attributes['grammer_var14'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var14']);
    }

    public function setGrammerVar14Attribute($value)
    {
        $this->attributes['grammer_var14'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar15Attribute($value)
    {
        if (empty($this->attributes['grammer_var15'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var15']);
    }

    public function setGrammerVar15Attribute($value)
    {
        $this->attributes['grammer_var15'] = !empty($value) ? implode('~', $value) : '';
    }



    public function getGrammerVar16Attribute($value)
    {
        if (empty($this->attributes['grammer_var16'])) {
            return [];
        }
        return explode('~', $this->attributes['grammer_var16']);
    }

    public function setGrammerVar16Attribute($value)
    {
        $this->attributes['grammer_var16'] = !empty($value) ? implode('~', $value) : '';
    }
}
