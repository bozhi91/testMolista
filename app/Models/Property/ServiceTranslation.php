<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;

use App\Traits\SluggableTrait;

class ServiceTranslation extends Model implements SluggableInterface
{
    protected $table = 'services_translations';

    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'title',
        'save_to'    => 'slug',
    ];

    protected $guarded = [];

    public $timestamps = false;
}
