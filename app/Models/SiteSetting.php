<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class SiteSetting extends Model
{
    protected $table = 'site_settings';


    public static function get_inputval($key = '')
    {
        $data = [];
        foreach (Self::all()->toArray() as $key => $value) {
            $data[$value['input_key']] = $value['config_value'];
        }
        return $data;
    }

    public static function get_configval($key = '')
    {
        $data = [];
        foreach (Self::all()->toArray() as $key => $value) {
            $data[$value['config_key']] = $value['config_value'];
        }
        return $data;
    }
}
