<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class StripeController extends Controller
{

	// New subscription
	public function postSubscription($site_id,$planchange_id)
	{
		//\Log::info("StripeController postSubscription -> for site ID {$site_id} (planchange {$planchange_id})");

		// Get site
		$site = \App\Site::find($site_id);
		if ( !$site )
		{
			\Log::error("StripeController postSubscription: site ID {$site_id} not found");
			abort(404);
		}

		// Validate plan change
		$planchange = $site->planchanges()->with('plan')->pending()->first();
		if ( !$planchange || $planchange_id != $planchange->id )
		{
			\Log::error("StripeController postSubscription: planchange_id ID {$planchange_id} not found");
			abort(404);
		}

		// Is stripe ?
		if ( $planchange->payment_method != 'stripe' )
		{
			\Log::error("StripeController postSubscription: planchange_id ID {$planchange_id} is not stripe");
			abort(404);
		}

		// Already subscribed
		if ( $site->subscribed('main') )
		{
			\Log::error("StripeController postSubscription: site ID {$site_id} already subscribed");
			abort(404);
		}

		// Attempt to create subscription
		if ( ! $site->newSubscription('main', $planchange->stripe_plan_id)->create( $this->request->input('stripeToken') ) )
		{
			\Log::error("StripeController postSubscription: newSubscription failed for site ID {$site_id} (planchange {$planchange_id})");
			\Log::error($response);
			abort(404);
		}

		// Update site
		$site->updatePlan($planchange_id);

		return redirect()->action('Corporate\SignupController@getFinish', [ $site->id, $site->subdomain ]);
	}

}
