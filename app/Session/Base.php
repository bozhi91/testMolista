<?php namespace App\Session;

class Base {

	static public $session_name = 'BaseSession';

	static public function get($key, $default = false)
	{
		$full_key = static::$session_name . ".{$key}";
		return session()->get($full_key, $default);
	}

	static public function all()
	{
		return session( static::$session_name );
	}

	static public function has($key)
	{
		$full_key = static::$session_name . ".{$key}";
		return session()->has($full_key);
	}

	static public function put($key,$value)
	{
		$full_key = static::$session_name . ".{$key}";
		return session()->put($full_key, $value);
	}

	static public function push($key,$value)
	{
		$full_key = static::$session_name . ".{$key}";
		return session()->push($full_key, $value);
	}

	static public function pull($key, $default = false)
	{
		$full_key = static::$session_name . ".{$key}";
		return session()->pull($full_key, $default);
	}

	static public function forget($key)
	{
		$full_key = static::$session_name . ".{$key}";
		return session()->forget($full_key);
	}

	static public function flush()
	{
		return session()->forget( static::$session_name );
	}

	static public function replace($value)
	{
		return session()->put(static::$session_name, $value);
	}

}
