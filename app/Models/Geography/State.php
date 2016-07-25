<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;

use App\Traits\SluggableTrait;

class State extends Model implements SluggableInterface
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

    public function cities() {
        return $this->hasMany('App\Models\Geography\City');
    }

    public function scopeEnabled($query)
    {
    return $query->where('states.enabled', 1);
    }

}
