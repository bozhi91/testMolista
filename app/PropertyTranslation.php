<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class PropertyTranslation extends Model implements SluggableInterface
{
    protected $table = 'properties_translations';

    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'title',
        'save_to'    => 'slug',
    ];

    protected $guarded = [];

    public $timestamps = false;
}
