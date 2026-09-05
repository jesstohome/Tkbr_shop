<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhitepaperTranslation extends Model
{
    protected $fillable = ['whitepaper_id', 'lang', 'title', 'summary', 'content', 'pdf'];

    public function whitepaper(){
        return $this->belongsTo(Whitepaper::class);
    }
}
