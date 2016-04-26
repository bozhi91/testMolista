<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class MenuItemTranslation extends Model
{
    protected $table = 'menus_items_translations';

    protected $guarded = [];

    public $timestamps = false;
}
