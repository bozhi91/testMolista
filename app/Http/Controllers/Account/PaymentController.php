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
		// Check if pending request
		if ( $this->site->has_pending_plan_request )
		{
			return redirect()->action('AccountController@index')->with('current_tab','plans');
		}

		$current_plan_level = @intval( $this->site->plan->level );


		if ( \App\Models\Plan::enabled()->where('level','>', $current_plan_level)->count() < 1 )
		{
			return redirect()->action('AccountController@index')->with('current_tab','plans');
		}

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
		// Check if pending request
		if ( $this->site->has_pending_plan_request )
		{
			return redirect()->action('AccountController@index')->with('current_tab','plans');
		}

		$current_plan_level = @intval( $this->site->plan->level );

		$plans = \App\Models\Plan::enabled()->where('level','>', $current_plan_level)->lists('code')->all();

		// Validation fields
		$fields = [
			'plan' => 'required|in:'.implode(',',$plans),
			"payment_interval.{$this->request->input('plan')}" => 'required|in:year,month',
			'invoicing' => 'required|array',
			'invoicing.type' => 'required|in:individual,company',
			'invoicing.company' => 'required_if:invoicing.type,company|string',
			'invoicing.first_name' => 'required|string',
			'invoicing.last_name' => 'required|string',
			'invoicing.email' => 'required|email',
			'invoicing.tax_id' => 'string',
			'invoicing.street' => 'required|string',
			'invoicing.zipcode' => 'string',
			'invoicing.city' => 'required|string',
			'invoicing.country_id' => 'required|exists:countries,id',
		];

		if ( $this->site->payment_method )
		{
			$fields['payment_method'] = 'required,in:'.implode(',', array_keys(\App\Models\Plan::getPaymentOptions()));
			if ( @$this->request->input('payment_method') == 'transfer' )
			{
				$fields['iban_account'] = 'required';
			}
		}

		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Get plan
		$plan = \App\Models\Plan::where('code', $this->request->input('plan'))->first();
		if ( !$plan || $plan->is_free )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Get payment interval
		$payment_interval = $this->request->input("payment_interval.{$this->request->input('plan')}");
		if ( !$payment_interval )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Set payment method && IBAN account
		$payment_method = $this->request->input('payment_method', $this->site->payment_method);
		$iban_account = $this->request->input('iban_account', $this->site->iban_account);
		if ( $payment_method != 'transfer' )
		{
			$iban_account = false;
		}

		// Prepare data
		$data = [
			'plan_id' => $plan->id,
			'payment_interval' => $payment_interval,
			'payment_method' => $payment_method,
			'old_data' => [
				'plan_id' => $this->site->plan_id,
				'payment_interval' => $this->site->payment_interval,
				'payment_method' => $this->site->payment_method,
				'iban_account' => $this->site->iban_account,
			],
			'new_data' => [
				'plan_id' => $plan->id,
				'payment_interval' => $payment_interval,
				'payment_method' => $payment_method,
				'iban_account' => $iban_account,
			],
			'invoicing' => $this->request->input('invoicing'),
			'locale' => \LaravelLocalization::getCurrentLocale(),
		];


		// Create sites_planchange
		$planchange = $this->site->planchanges()->create($data);
		if ( !$planchange )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// If stripe payment,
		if ( $planchange->new_data['payment_method'] == 'stripe' )
		{
			return redirect()->action('Account\PaymentController@getPay');
		}

		return redirect()->action('AccountController@index')->with('current_tab','plans')->with('success', trans('account/payment.invoicing.created'));
	}

	public function getPay()
	{
		$planchange = $this->site->planchanges()->pending()->first();
		if ( !$planchange )
		{
			return redirect()->action('AccountController@index')->with('current_tab','plans');
		}

		// If paymthod is stripe
		if ( $planchange->new_data['payment_method'] == 'stripe' )
		{
			// Site has stripe ID
			if ( $this->site->stripe_id )
			{
				// Attempt to switch
				$response = $this->site->subscription('main')->swap($planchange->stripe_plan_id);
				if ( !$response )
				{
					\Log::error("Account\PaymentController getPay: Error switching plan for site ID {$this->site->id}");
					\Log::error($response);
					abort(404);

				}

				$planchange->update([
					'response' => $response,
				]);

				$this->site->updatePlan($planchange->id);

				return redirect()->action('AccountController@index')->with('current_tab','plans')->with('success', trans('account/payment.invoicing.updated'));
			}
		}

		$data = $this->site->getSignupInfo();

		return view('account.payment.pay', $data);
	}
	public function postPay()
	{
		// Validate plan change
		$planchange = $this->site->planchanges()->pending()->first();
		if ( !$planchange )
		{
			\Log::error("Account\PaymentController postPay: planchange ID {$planchange->id} not found");
			abort(404);
		}

		// Is stripe ?
		if ( $planchange->payment_method != 'stripe' )
		{
			\Log::error("Account\PaymentController postPay: planchange ID {$planchange->id} is not stripe");
			abort(404);
		}

		// Already subscribed
		if ( $this->site->subscribed('main') )
		{
			\Log::error("Account\PaymentController postPay: site ID {$this->site->id} already subscribed");
			abort(404);
		}

		// Attempt to create subscription
		if ( ! $this->site->newSubscription('main', $planchange->stripe_plan_id)->create( $this->request->input('stripeToken') ) )
		{
			\Log::error("Account\PaymentController postPay: newSubscription failed for this->site ID {$this->site->id} (planchange {$planchange->id})");
			abort(404);
		}

		// Update site
		$this->site->updatePlan($planchange->id);

		return redirect()->action('AccountController@index')->with('current_tab','plans')->with('success', trans('account/payment.invoicing.updated'));
	}

	public function postCancel()
	{
		$pending_request = $this->site->planchanges()->pending()->first();

		if ( !$pending_request )
		{
			return redirect()->action('AccountController@index')->with('current_tab','plans')->with('error', trans('general.messages.error'));
		}

		$pending_request->status = 'canceled';
		$pending_request->save();
		$pending_request->delete();

		return redirect()->action('AccountController@index')->with('current_tab','plans')->with('success', trans('account/payment.plans.cancel.success'));
	}

}
