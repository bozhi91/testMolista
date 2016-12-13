<?php namespace App\Http\Controllers\Account\Profile;

use Illuminate\Http\Request;

use App\Http\Requests;

class PlanController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		\View::share('menu_section', 'account');
		\View::share('submenu_section', 'profile');
		\View::share('hide_advanced_search_modal', true);
	}

	public function getIndex()
	{
		$data = [
			'current_tab' => 'plan',
			'pending_request' => $this->site->planchanges()->pending()->first(),
			'plans' => \App\Models\Plan::getEnabled( $this->site->payment_currency ),
			'plan_options' => 0,
			'current_plan' => $this->site->planchanges()->active()->first(),
			'paid_until' => \App\Session\Site::get('plan.paid_until'),
			'past_due' => false,
			'payment_method' => \App\Session\Site::get('plan.payment_method'),
			'card_brand' => \App\Session\Site::get('plan.card_brand'),
			'card_last_four' => \App\Session\Site::get('plan.card_last_four'),
			'iban_account' => \App\Session\Site::get('plan.iban_account'),
		];

		// Available plans counter
		if ( $data['plans'] )
		{
			$data['plan_options'] = $data['plans']->filter(function($value,$key) {
				return ($value->level > @intval($this->site->plan->level));
			})->count();
		}

		// Paid until
		if ( $data['paid_until'] )
		{
			if ( $data['paid_until'] < \Carbon\Carbon::now()->format('Y-m-d 00:00:00') )
			{
				$data['past_due'] = true;
			}
		}

		return view('account.index', $data);
	}

}
