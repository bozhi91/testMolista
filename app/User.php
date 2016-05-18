<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
	use EntrustUserTrait;

	protected $fillable = [
		'name', 'email', 'password', 'locale',
	];

	protected $hidden = [
		'password', 'remember_token',
	];

	public function stats() {
		return $this->hasMany('App\Models\User\Stats');
	}

	public function sites() {
		return $this->belongsToMany('App\Site', 'sites_users', 'user_id', 'site_id');
	}

	public function properties() {
		return $this->belongsToMany('App\Property', 'properties_users', 'user_id', 'property_id');
	}

	public function translation_locales() {
		return $this->belongsToMany('App\Models\Locale', 'user_translation_locales', 'user_id', 'locale_id');
	}
	public function canTranslate($id_or_locale = false)
	{
		$auth_locales = $this->translation_locales;

		if ( count($auth_locales) < 1 )
		{
			return true;
		}

		foreach ($auth_locales as $auth_locale)
		{
			// check locale (iso)
			if ( $auth_locale->locale == $id_or_locale )
			{
				return true;
			}
			// check id
			if ( $auth_locale->id == $id_or_locale )
			{
				return true;
			}
		}

		return false;
	}

	public function canProperty($permission,$site_id=false)
	{
		if ( !$this->id )
		{
			return false;
		}

		if ( !$site_id )
		{
			$site_id = \App\Session\Site::get('site_id', false);
		}

		$permission_field = "can_{$permission}";

		$property_permission_key = "property-permission.{$site_id}.{$permission_field}";

		if ( ! \App\Session\User::has($property_permission_key) )
		{
			$granted = false;

			// Companies always have permission
			if ( $this->hasRole('company') )
			{
				$granted = true;
			// Employees might or might not
			} elseif ( $this->hasRole('employee') ) {
				$relation = $this->sites()->withPivot($permission_field)->find($site_id);
				$granted = empty($relation->pivot->$permission_field) ? false : true;
			}

			\App\Session\User::push($property_permission_key, $granted);
		}

		return \App\Session\User::get($property_permission_key);
	}

	public function scopeofSite($query, $site_id)
	{
		$query->whereIn('id', function($query) use ($site_id) {
			$query->distinct()->select('user_id')
					->from('sites_users')
					->where('site_id', $site_id);
			});
	}

	public function scopeWithRole($query, $roles)
	{
		return $query->whereIn('id', function($query) use ($roles) {
			if ( !is_array($roles) )
			{
				$roles = [ $roles ];
			}
			$query->select('role_user.user_id')
					->from('role_user')
					->join('roles', 'role_user.role_id', '=', 'roles.id')
					->whereIn('roles.name', $roles);
			});
	}

	public function scopeWithoutRole($query, $roles)
	{
		return $query->whereIn('id', function($query) use ($roles) {
			if ( !is_array($roles) )
			{
				$roles = [ $roles ];
			}
			$query->select('role_user.user_id')
					->from('role_user')
					->join('roles', 'role_user.role_id', '=', 'roles.id')
					->whereNotIn('roles.name', $roles);
			});
	}

	public function scopeAvailable($query, $site_id=0)
	{
		return $query->whereNotIn('id', function($query) use ($site_id) {
			$query->select('user_id')
					->from('sites_users')
					->where('site_id', '!=', $site_id);
			});
	}

	public function updateUserPropertiesRelations()
	{
		// If company
		if ( $this->hasRole('company') )
		{
			// Attach user to all properties of his sites
			foreach (\App\Property::whereIn('site_id', $this->sites()->lists('id'))->get() as $property)
			{
				if ( !$property->users->contains( $this->id ) )
				{
					$property->users()->attach($this->id);
				}

			}
			// Make user owner
			$this->properties()->update([
				'is_owner' => 1,
			]);
		}
		// If employee
		elseif ( $this->hasRole('employee') )
		{
			$this->properties()->update([
				'is_owner' => 0,
			]);
		}
		// Else
		else
		{
			$this->properties()->detach();
		}
	}

}
