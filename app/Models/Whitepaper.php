<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Whitepaper extends Model
{
    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $whitepaper_translation = $this->hasMany(WhitepaperTranslation::class)->where('lang', $lang)->first();
        return $whitepaper_translation != null ? $whitepaper_translation->$field : $this->$field;
    }

    public function whitepaper_translations(){
        return $this->hasMany(WhitepaperTranslation::class);
    }
}
