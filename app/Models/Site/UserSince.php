<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class UserSince extends Model
{
    protected static $default_since = 5; // Days default since

    protected $table = 'sites_users_since';

	protected $guarded = [];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public static function getSince($site_id, $user_id, $section, $default_since=false)
	{
		$item = self::ofSite($site_id)->ofUser($user_id)->ofSection($section)->first();

		if ($item)
		{
			return $item->since;
		}

		$default_since = intval($default_since);
		if ( !$default_since )
		{
			$default_since = self::$default_since;
		}

		return date('Y-m-d', strtotime("-{$default_since} days"));
	}

	public static function setSince($site_id, $user_id, $section)
	{
		$item = self::firstOrCreate([
			'site_id' => $site_id,
			'user_id' => $user_id,
			'section' => $section,
		]);

		$item->update([
			'since' => date('Y-m-d'),
		]);
	}

	public function scopeOfSite($query, $site_id)
	{
		return $query->where("{$this->table}.site_id", $site_id);
	}

	public function scopeOfUser($query, $user_id)
	{
		return $query->where("{$this->table}.user_id", $user_id);
	}

	public function scopeOfSection($query, $section)
	{
		return $query->where("{$this->table}.section", $section);
	}

}
