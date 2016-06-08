<?php namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Requests;

class PaymentController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'home');
		\View::share('submenu_subsection', false);
	}

	public function getUpgrade()
	{
		return view('account.payment.upgrade');
	}
	public function postUpgrade()
	{
echo "<pre>";
print_r($this->request->all());
echo "</pre>";
die;
	}

	public function getMethod()
	{
		return view('account.payment.method');
	}
	public function postMethod()
	{
echo "<pre>";
print_r($this->request->all());
echo "</pre>";
die;
	}

}
