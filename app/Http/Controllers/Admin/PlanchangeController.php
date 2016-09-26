<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PlanchangeController extends Controller
{

	public function getIndex()
	{
		$query = \App\Models\Site\Planchange::with('plan')->with('site')->pending();

		// Only paid by transfer
		$query->where('payment_method','transfer');

		if ( $this->request->input('plan_id') )
		{
			$query->where('plan_id', $this->request->input('plan_id'));
		}

		switch ( $this->request->input('order') )
		{
			case 'desc':
				$order = 'desc';
				break;
			default:
				$order = 'asc';
		}
		switch ( $this->request->input('orderby') )
		{
			case 'creation':
			default:
				$query->orderBy('created_at', $order);
				break;
		}

		$planchanges = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$plans = \App\Models\Plan::enabled()->orderBy('name')->lists('name','id')->all();

		$this->set_go_back_link();

		return view('admin.planchange.index', compact('planchanges','plans'));
	}

	public function getEdit($id)
	{
		$planchange = \App\Models\Site\Planchange::with('plan')->with('site')->findOrFail($id);
		$old_plan = \App\Models\Plan::find( @$planchange->old_data['plan_id'] );
		$history = \App\Site::find( @$planchange->site_id )->planchanges()->withTrashed()->with('plan')->where('id','!=',$id)->orderBy('created_at','desc')->get();

		return view('admin.planchange.edit', compact('planchange','old_plan','history'));
	}
	public function postEdit($id)
	{
		$planchange = \App\Models\Site\Planchange::with('plan')->with('site')->findOrFail($id);

		switch ( $this->request->input('accept') )
		{
			case 1:
				return $this->acceptPlanchange($planchange);
			case 0:
				return $this->rejectPlanchange($planchange);
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	protected function acceptPlanchange($planchange)
	{
		// Verify payment_method == transfer
		if ( $planchange->payment_method != 'transfer' )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Validate input
		$fields = [
			'payment_amount' => 'required|numeric|min:0',
			'paid_from' => 'required|date_format:"Y-m-d"',
			'paid_until' => 'required|date_format:"Y-m-d"',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Validate date
		if ( strtotime($this->request->input('paid_from')) >= strtotime($this->request->input('paid_until')) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Prepare data
		$payment = $planchange->site->preparePaymentData([
			'trigger' => 'Admin (Admin\PlanchangeController@acceptPlanchange)',
			'paid_from' => $this->request->input('paid_from'),
			'paid_until' => $this->request->input('paid_until'),
			'plan_id' => $planchange->plan->id,
			'payment_method' => $planchange->payment_method,
			'payment_amount' => $this->request->input('payment_amount'),
			'payment_currency' => $planchange->plan->currency,
			'created_by' => $this->auth->user()->id,
		]);

		// Validate pàyment data
		$validator = \App\Models\Site\Payment::getCreateValidator($payment);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Add paid_until to new data
		$planchange->new_data = array_merge($planchange->new_data, [
			'paid_until' => $this->request->input('paid_until'),
		]);
		$planchange->save();

		// Get site
		$site = $planchange->site;

		// Update plan
		if ( $site->updatePlan($planchange->id) )
		{
			// Save payment
			\App\Models\Site\Payment::saveModel($payment);
		}
		else
		{
			\Log::error("Site -> updatePlan: error updating plan for site ID {$site->id} (planchange {$planchange->id})");
		}


		// Redirect to list
		return redirect()->action('Admin\PlanchangeController@getIndex')->with('success', trans('admin/planchange.message.accepted'));
	}

	protected function rejectPlanchange($planchange)
	{
		// Mark as rejected
		$planchange->update([
			'status' => 'rejected',
			'response' => sanitize( $this->request->input('response') ),
		]);

		// Send email to owners
		$locale_backup = \App::getLocale();
		$email_data = $planchange->site->getSignupInfo( $planchange->locale );
		if ( @$email_data['owner_email'] )
		{
			$email_data['reason'] = sanitize( $this->request->input('response') );
			\App::setLocale( $planchange->locale );
			$html = view('emails.planchange.reject', $email_data)->render();
			\Mail::send('dummy', [ 'content' => $html ], function($message) use ($email_data) {
				$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
				$message->subject( trans('admin/planchange.reject.subject') );
				$message->to( $email_data['owner_email'] );
			});
		}
		\App::setLocale( $locale_backup );

		// Delete
		$planchange->delete();

		// Redirect to list
		return redirect()->action('Admin\PlanchangeController@getIndex')->with('success', trans('admin/planchange.message.rejected'));
	}
}
