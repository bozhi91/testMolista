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
		// Pending request
		$pending_request = $this->site->planchanges()->pending()->first();

		$plans = \App\Models\Plan::getEnabled( $this->site->payment_currency );

		// Current plan level && available plans
		$current_plan_level = @intval( $this->site->plan->level );
		$plan_options = $plans ? $plans->filter(function($value,$key) use ($current_plan_level) {
			return ($value->level > $current_plan_level);
		})->count() : 0;

		// Current planchange
		$current_plan = $this->site->planchanges()->active()->first();

		$current_tab = 'plan';
		return view('account.index', compact('pending_request','plans','plan_options','current_plan','current_tab'));
	}

}
