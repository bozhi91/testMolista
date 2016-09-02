<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExpirationsController extends Controller
{

	public function getIndex()
	{
		$query = \App\Site::with('plan')->whereNotNull('paid_until');

		// Filter by domain
		if ( $this->request->input('domain') )
		{
			$query->withDomain($this->request->input('domain'));
		}

		// Filter by plan
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
			case 'paid_until':
			default:
				$query->orderBy('paid_until', $order);
				break;
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
			'paid_from' => 'required|date_format:"d/m/Y"',
			'paid_until' => 'required|date_format:"d/m/Y"',
		]);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Validate dates
		if ( !preg_match('#^(\d{2})\/(\d{2})\/(\d{4})$#', $this->request->input('paid_from'), $paid_from) || !preg_match('#^(\d{2})\/(\d{2})\/(\d{4})$#', $this->request->input('paid_until'), $paid_until) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$payment = $site->preparePaymentData([
			'trigger' => 'Admin (Admin\ExpirationsController@postExtend)',
			'paid_from' => "{$paid_from[3]}-{$paid_from[2]}-$paid_from[1]",
			'paid_until' => "{$paid_until[3]}-{$paid_until[2]}-$paid_until[1]",
			'payment_method' => $site->payment_method,
			'payment_amount' => $this->request->input('payment_amount'),
			'created_by' => $this->auth->user()->id,
		]);

		// Validate period
		if ( strtotime($payment['paid_from']) >= strtotime($payment['paid_until']) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

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

}
