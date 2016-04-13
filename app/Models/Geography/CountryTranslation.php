<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    protected $table = 'countries_translations';

    protected $guarded = [];

    public $timestamps = false;
}
