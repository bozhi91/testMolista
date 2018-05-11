<?php namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Support\Facades\Log;

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
			return redirect()->action('Account\Profile\PlanController@getIndex');
		}

		$current_plan_level = @intval( $this->site->plan->level );

		if ( \App\Models\Plan::enabled()->where('plans.currency',$this->site->payment_currency)->where('level','>', $current_plan_level)->count() < 1 )
		{
			return redirect()->action('Account\Profile\PlanController@getIndex');
		}

		$plans = \App\Models\Plan::getEnabled( $this->site->payment_currency );

		if ( $this->request->input('plan') )
		{
			if ( empty($plans[$this->request->input('plan')]) || $plans[$this->request->input('plan')]->level <= $current_plan_level )
			{
				$this->request->merge([ 'plan'=>false ]);
			}
		}

		$payment_options = \App\Models\Plan::getPaymentOptions( $this->site->country->pay_methods );

		return view('account.payment.upgrade', compact('current_plan_level','plans','payment_options'));
	}
	public function postUpgrade()
	{
		// Check if pending request
		if ( $this->site->has_pending_plan_request )
		{
			return redirect()->action('Account\Profile\PlanController@getIndex');
		}

		$current_plan_level = @intval( $this->site->plan->level );

		$plans = [];
		foreach (\App\Models\Plan::getEnabled($this->site->payment_currency) as $key => $plan)
		{
			if ( $plan->level > $current_plan_level )
			{
				$plans[] = $plan->code;
			}
		}

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
			$fields['payment_method'] = 'required,in:'.implode(',', $this->site->country->pay_methods);
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

		return redirect()->action('Account\Profile\PlanController@getIndex')->with('success', trans('account/payment.invoicing.transfer.created'));
	}

	public function getPay()
	{
		$planchange = $this->site->planchanges()->pending()->first();
		if ( !$planchange )
		{
			return redirect()->action('Account\Profile\PlanController@getIndex');
		}

		// If paymethod is stripe && site has stripe ID
		if ( $planchange->new_data['payment_method'] == 'stripe' && $this->site->stripe_id )
		{
			if ( $this->site->subscribed('main') )
			{
				// Attempt to switch
				$response =  $this->site->subscription('main')->swap($planchange->stripe_plan_id);
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

				return redirect()->action('Account\Profile\PlanController@getIndex')->with('success', trans('account/payment.invoicing.updated'));
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

		return redirect()->action('Account\Profile\PlanController@getIndex')->with('success', trans('account/payment.invoicing.updated'));
	}

	public function postCancel()
	{
		$pending_request = $this->site->planchanges()->pending()->first();

		if ( !$pending_request )
		{
			return redirect()->action('Account\Profile\PlanController@getIndex')->with('error', trans('general.messages.error'));
		}

		$pending_request->status = 'canceled';
		$pending_request->save();
		$pending_request->delete();

		return redirect()->action('Account\Profile\PlanController@getIndex')->with('success', trans('account/payment.plans.cancel.success'));
	}

	public function getUpdateCreditCard()
	{
	    echo "DONE";

       /* //pass the user object to the gateway; it must implement BillableContract

        $gateway = new \Laravel\Cashier\StripeGateway($user);

        //manually create a new Customer instance with Stripe
        $customer = $gateway->createStripeCustomer($request->get('stripe_token'));

        //update the model's info
        $gateway->updateLocalStripeData($customer);
        */

        $stripe_customer = $this->site->stripe_customer;
		if ( !$stripe_customer )
		{
			abort(404);
		}
        echo "1";
		$user_email = $stripe_customer->email ? $stripe_customer->email : $this->site_user->email;

		return view('account.payment.update-credit-card', compact('user_email'));
	}
	public function postUpdateCreditCard()
	{
		$stripe_customer = $this->site->stripe_customer;
		if ( !$stripe_customer )
		{
			abort(404);
		}

		if ( !$this->request->input('stripeToken') )
		{
			return redirect()->back()->with('error', trans('general.messages.error'));
		}

		$this->site->updateCard($this->request->input('stripeToken'));
		$this->site->updateSiteSetup();

		return redirect()->action('Account\Profile\PlanController@getIndex')->with('success', trans('account/payment.cc.update.success'));
	}

	public function getRetryPayment()
	{
		$last_invoice = $this->site->stripe_invoice_last;

		if ( $last_invoice && !$last_invoice->paid )
		{
			\Stripe\Stripe::setApiKey( env('STRIPE_SECRET') );
			$invoice = \Stripe\Invoice::retrieve( $last_invoice->id );

			try {
				$payment_response = @$invoice->pay();
			// Card errors (declined, etc)
			} catch(\Stripe\Error\Card $e) {
				$response_error = $this->translateApiResponseError($e);
			// Too many requests made to the API too quickly
			} catch (\Stripe\Error\RateLimit $e) {
				$response_error = $this->translateApiResponseError($e);
			// Invalid parameters were supplied to Stripe's API
			} catch (\Stripe\Error\InvalidRequest $e) {
				$response_error = $this->translateApiResponseError($e);
			// Authentication with Stripe's API failed
			} catch (\Stripe\Error\Authentication $e) {
				$response_error = $this->translateApiResponseError($e);
			// Network communication with Stripe failed
			} catch (\Stripe\Error\ApiConnection $e) {
				$response_error = $this->translateApiResponseError($e);
			// Display a very generic error to the user, and maybe send yourself an email
			} catch (\Stripe\Error\Base $e) {
				$response_error = $this->translateApiResponseError($e);
			// Something else happened, completely unrelated to Stripe
			} catch (Exception $e) {
				$response_error = $this->translateApiResponseError($e);
			}
		}

		return view('account.payment.retry-payment', compact('last_invoice','payment_response','response_error'));
	}

	public function translateApiResponseError($e)
	{
		$body = @$e->getJsonBody();
		$error = @$body['error'];

		$response = [
			'status' => @$e->getHttpStatus(),
			'error' => $error,
			'type' => @$error['type'],
			'code' => @$error['code'],
			'param' => @$error['param'],
			'message' => @$error['message'],
		];

		if ( !$response['message'] )
		{
			$response['message'] = trans('general.messages.error');
		}

		return (object) $response;
	}

}
