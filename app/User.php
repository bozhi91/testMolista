<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
	use EntrustUserTrait;

	protected static $thumb_width = 75;
	protected static $thumb_height = 75;

	protected $fillable = [
		'name', 'email', 'phone','password', 'locale',
	];

	protected $hidden = [
		'password', 'remember_token',
	];

	public function stats() {
		return $this->hasMany('App\Models\User\Stats');
	}

	public function sites() {
		return $this->belongsToMany('App\Site', 'sites_users', 'user_id', 'site_id')->withTranslations();
	}

	public function calendars() {
		return $this->belongsToMany('App\Models\Calendar', 'calendars_users', 'user_id', 'calendar_id');
	}

	public function sites_signatures() {
		return $this->hasMany('\App\Models\Site\UserSignature');
	}

	public function properties() {
		return $this->belongsToMany('App\Property', 'properties_users', 'user_id', 'property_id')->withTranslations();
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

		if ( true || ! \App\Session\User::has($property_permission_key) )
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

			\App\Session\User::put($property_permission_key, $granted);
		}

		return \App\Session\User::get($property_permission_key);
	}

	public function getImageDirectoryAttribute()
	{
		return "users/{$this->id}";
	}
	public function getImageUrlAttribute()
	{
		if ( $this->image )
		{
			return asset("{$this->image_directory}/{$this->image}");
		}

		return asset('images/users/default.png');
	}

	public function getSignaturePartsAttribute()
	{
		return [
			'name' => $this->name,
			'email' => $this->email,
			'phone' => $this->phone,
			'linkedin' => $this->linkedin,
			'image' => $this->image ? $this->image_url : false,
		];
	}

	public function getRoleLevelAttribute()
	{
		$level = 1000;

		foreach ($this->roles as $role) 
		{
			if ( $level > $role->level )
			{
				$level = $role->level;
			}
		}

		return $level;
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

	public function scopeWithMinLevel($query, $min_level)
	{
		return $query->whereIn('id', function($query) use ($min_level) {
			$query->select('role_user.user_id')
					->from('role_user')
					->join('roles', 'role_user.role_id', '=', 'roles.id')
					->where('roles.level', '>=', $min_level);
			});
	}

	public function getUpdatedAutologinToken()
	{
		$this->autologin_token = sha1( $this->id . uniqid() );
		while ( \App\User::where('autologin_token', $this->autologin_token)->count() > 0 )
		{
			$this->autologin_token = sha1( $this->id . uniqid() . rand(1000, 9999) );
		}

		$this->save();

		return $this->autologin_token;
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

	public static function getFields($id=false)
	{
		$fields = [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . intval($id) . ',id',
			'locale' => 'required|string|in:'.implode(',',\LaravelLocalization::getSupportedLanguagesKeys()),
			'password' => $id ? 'min:6' : 'required|min:6',
			'phone' => '',
			'linkedin' => 'url',
			'image' => 'image|max:' . \Config::get('app.property_image_maxsize', 2048),
		];

		if ( $id )
		{
			$user = self::findOrFail($id);
			// Image require ?
			if ( $user->image )
			{
				// Only if none is defined
				$fields['image'] = 'image|max:' . \Config::get('app.property_image_maxsize', 2048);
			}
			// Email cannot be updated
			unset($fields['email']);
		}

		return $fields;
	}
	public static function saveModel($data,$id=false)
	{
		if ( $id )
		{
			$user = self::findOrFail($id);
		}
		else
		{
			$user = new \App\User();
		}


		foreach (self::getFields($id) as $key => $def)
		{
			switch ( $key )
			{
				case 'password':
					if ( @$data['password'] )
					{
						$user->password = bcrypt( $data['password'] );
					}
					break;
				case 'image':
					break;
				default:
					$user->$key = @$data[$key];
			}
		}

		if ( !$user->save() )
		{
			return false;
		}

		if ( @$data['image'] )
		{
			if ( $user->image )
			{
				\File::delete( public_path("{$user->image_directory}/{$user->image}") );
			}
			$user->image = $data['image']->getClientOriginalName();
			while ( file_exists( public_path("{$user->image_directory}/{$user->image}") ) )
			{
				$user->image = uniqid() . '_' . $data['image']->getClientOriginalName();
			}
			$data['image']->move(public_path($user->image_directory), $user->image);
			// Resize
			$image_path = public_path("{$user->image_directory}/{$user->image}");
			$thumb = \Image::make($image_path)->fit(self::$thumb_width,self::$thumb_height)->save($image_path);
			// Save changes
			$user->save();
		}

		return $user;
	}

}
