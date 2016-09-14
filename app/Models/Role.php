<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	protected $fillable = [ 'name', 'display_name', 'description' ];

	public function scopeWithMinLevel($query, $min_level)
	{
		return $query->where('roles.level', '>=', $min_level);
	}
}
