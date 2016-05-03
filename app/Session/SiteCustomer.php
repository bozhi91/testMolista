<?php namespace App\Session;

class SiteCustomer extends Base {

	static public $session_name = 'SiteCustomerSession';

	static public function check()
	{
		$customer_id = session()->get( static::$session_name . '.id' );
		return $customer_id ? true : false;
	}

	static public function guest()
	{
		$customer_id = session()->get( static::$session_name . '.id' );
		return $customer_id ? false : true;
	}

	static public function login($email,$password,$site_id)
	{
		$customer = \App\Models\Site\Customer::where('email',$email)->where('site_id',$site_id)->first();

		if ( !$customer || !\Hash::check($password, $customer->password) )
		{
			return false;
		}

		static::replace([
			'id' => $customer->id,
			'locale' => $customer->locale,
			'first_name' => $customer->first_name,
			'last_name' => $customer->last_name,
			'full_name' => $customer->full_name,
			'email' => $customer->email,
			'phone' => $customer->phone,
			'created_at' => $customer->created_at->format('Y-m-d H:i:s'),
		]);

		return true;
	}
}
