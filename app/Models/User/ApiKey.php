<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $table = 'apikeys';
    protected $guarded = [];

    public function sites()
    {
        return $this->belongsTo('App\Site');
    }

    public static function generateKey()
    {
        do {
            $salt = sha1(time() . mt_rand());
            $newKey = substr($salt, 0, 40);
        } // Already in the DB? Fail. Try again
        while (self::keyExists($newKey));

        return $newKey;
    }

    private static function keyExists($key)
    {
        $apiKeyCount = self::where('key',$key)->limit(1)->count();

        return ( $apiKeyCount > 0 ) ? true : false;
    }

    public static function getCurrent()
    {
        $key = \Route::getCurrentRequest()->header('Api-Key');
        return self::getByKey($key);
    }

    public static function getByKey($key)
    {
        return self::where('key', '=', $key)->first();
    }
}
