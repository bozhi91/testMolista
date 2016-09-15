<?php namespace App\Http\Controllers\Admin\Sites;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
	}

	public function getList($site_id, $check_ajax = true)
	{
		if ( $check_ajax && !$this->request->ajax() )
		{
			return redirect()->action('Admin\SitesController@edit', $site_id)->with('current_tab','payments');
		}

		$payments = \App\Models\Site\Payment::where('site_id', $site_id)
						->with('reseller')
						->with('infocurrency')
						->orderBy('created_at', 'desc')
						->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$payments->setPath( action('Admin\Sites\PaymentsController@getList', $site_id) );

		return view('admin.sites.payments.list', compact('payments'));
	}

	public function getEdit($id)
	{
		$payment = \App\Models\Site\Payment::findOrFail($id);

		$resellers = \App\Models\Reseller::orderBy('name')->lists('name','id')->all();

		return view('admin.sites.payments.edit', compact('payment','resellers'));
	}

	public function postSave($id)
	{

		$payment = \App\Models\Site\Payment::find($id);
		if ( !$payment )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$validator = \Validator::make($this->request->all(), [
			'reseller_id' => 'exists:resellers,id',
			'reseller_fixed' => 'numeric|min:0',
			'reseller_variable' => 'numeric|min:0|max:100',
			'reseller_paid' => 'boolean',
			'reseller_date' => 'required_with_all:reseller_id,reseller_paid|date_format:"Y-m-d"',
		]);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$update = [
			'reseller_id' => $this->request->input('reseller_id') ? $this->request->input('reseller_id') : null,
			'reseller_fixed' => $this->request->input('reseller_id') ? floatval($this->request->input('reseller_fixed')) : 0,
			'reseller_variable' => $this->request->input('reseller_id') ? floatval($this->request->input('reseller_variable')) : 0,
			'reseller_paid' => $this->request->input('reseller_paid') ? 1 : 0,
			'reseller_date' => $this->request->input('reseller_paid') ? $this->request->input('reseller_date') : null,
		];

		$update['reseller_amount'] = $update['reseller_fixed'] + ( $payment->payment_amount * $update['reseller_variable'] / 100 );

		$payment->update($update);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}
}
