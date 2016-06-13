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
		$current_plan_level = @intval( $this->site->plan->level );

		$plans = \App\Models\Plan::getEnabled();

		if ( $this->request->input('plan') )
		{
			if ( empty($plans[$this->request->input('plan')]) || $plans[$this->request->input('plan')]->level <= $current_plan_level )
			{
				$this->request->merge([ 'plan'=>false ]);
			}
		}

		return view('account.payment.upgrade', compact('current_plan_level','plans'));
	}
	public function postUpgrade()
	{
		// Validate data
		$fields = [
			'plan' => 'required|exists:plans,code,enabled,1',
			"payment_interval.{$this->request->input('plan')}" => 'required|in:year,month',
		];
		// No payment_method
		if ( !\App\Session\Site::get('plan.payment_method') )
		{
			$fields['payment_method'] = 'required|in:'.implode(',', array_keys(\App\Models\Plan::getPaymentOptions()));
			$fields['iban_account'] = 'required_if:payment_method,transfer';
			$fields['stripe_token'] = 'required_if:payment_method,stripe';
		}
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$data = [
			'plan' => $this->request->input('plan'),
			'payment_interval' => $this->request->input("payment_interval.{$this->request->input('plan')}")
		];
		if ( !\App\Session\Site::get('plan.payment_method') )
		{
			$data['payment_method'] = $this->request->input('payment_method');
			$data['iban_account'] = $this->request->input('iban_account');
			$data['stripe_token'] = $this->request->input('stripe_token');
		}

		if ( $this->site->updatePlan($data) )
		{
			$this->site->updateSiteSetup();
			return redirect()->action('AccountController@index')->with('success', trans('account/payment.upgrade.success.plan'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
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
