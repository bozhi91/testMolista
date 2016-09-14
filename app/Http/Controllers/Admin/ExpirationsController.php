<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExpirationsController extends Controller
{

	public function getIndex()
	{
		$query = \App\Site::with('plan')->whereNotNull('paid_until');

		// Filter by plan
		if ( $this->request->input('plan_id') )
		{
			$query->where('plan_id', $this->request->input('plan_id'));
		}

		// Filter by domain
		if ( $this->request->input('domain') )
		{
			$query->withDomain($this->request->input('domain'));
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
			case 'paid_until':
			default:
				$query->orderBy('paid_until', $order);
				break;
		}

		if ( $this->request->input('csv') )
		{
			return $this->csv_output($query, [
				'main_url' => trans('admin/expirations.site'),
				'plan_name' => trans('admin/expirations.plan'),
				'interval' => trans('admin/expirations.payment.interval'),
				'method' => trans('admin/expirations.payment.method'),
				'paid_until' => trans('admin/expirations.paid.until'),
			]);
		}

		$expirations = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$plans = \App\Models\Plan::enabled()->orderBy('name')->lists('name','id')->all();

		$this->set_go_back_link();

		return view('admin.expirations.index', compact('expirations','plans'));
	}

	public function getExtend($id_site)
	{
		$site = \App\Site::with('plan')->findOrFail($id_site);
		if ( $site->payment_method != 'transfer' )
		{
			abort(404);
		}

		return view('admin.expirations.extend', compact('site'));
	}
	public function postExtend($id_site)
	{
		$site = \App\Site::with('plan')->findOrFail($id_site);
		if ( $site->payment_method != 'transfer' )
		{
			abort(404);
		}

		// Validate input
		$validator = \Validator::make($this->request->all(), [
			'payment_amount' => 'required|numeric|min:0',
			'paid_from' => 'required|date_format:"Y-m-d"',
			'paid_until' => 'required|date_format:"Y-m-d"',
		]);
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
		$payment = $site->preparePaymentData([
			'trigger' => 'Admin (Admin\ExpirationsController@postExtend)',
			'paid_from' => $this->request->input('paid_from'),
			'paid_until' => $this->request->input('paid_until'),
			'payment_method' => $site->payment_method,
			'payment_amount' => $this->request->input('payment_amount'),
			'created_by' => $this->auth->user()->id,
		]);

		// Validate pàyment data
		$validator = \App\Models\Site\Payment::getCreateValidator($payment);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Save payment
		\App\Models\Site\Payment::saveModel($payment);

		// Extend paid until
		$site->update([
			'paid_until' => $payment['paid_until'],
		]);

		return redirect()->action('Admin\ExpirationsController@getExtended', $site->id);
	}

	public function getExtended($id_site)
	{
		$site = \App\Site::with('plan')->findOrFail($id_site);
		if ( $site->payment_method != 'transfer' )
		{
			abort(404);
		}

		return view('admin.expirations.extended', compact('site'));
	}

	protected function csv_prepare_row($row)
	{
		$row->plan_name = @$row->plan->name;
		$row->interval = trans("web/plans.price.{$row->payment_interval}");
		$row->method = trans("account/payment.method.{$row->payment_method}");
		return $row;
	}

}
