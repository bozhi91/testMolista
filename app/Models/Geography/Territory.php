<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Territory extends Model implements SluggableInterface
{
    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'name',
        'save_to'    => 'slug',
    ];

    protected $guarded = [];

    public function properties() {
        return $this->hasMany('App\Property');
    }

    public function scopeEnabled($query)
    {
        return $query->where('territories.enabled', 1);
    }

}
