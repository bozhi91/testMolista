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

    public function getList($site_id)
	{
		if ( !$this->request->ajax() )
		{
			return redirect()->action('Admin\SitesController@edit', $site_id)->with('current_tab','payments');
		}

		$payments = \App\Models\Site\Payment::where('site_id', $site_id)
						->with('reseller')
						->with('infocurrency')
						->orderBy('created_at', 'desc')
						->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		return view('admin.sites.payments.list', compact('payments'));
	}

	public function getEdit($id)
	{
		if ( false && !$this->request->input('ajax') )
		{
			return redirect()->action('Admin\SitesController@index')->with('current_tab','payments');
		}

		$payment = \App\Models\Site\Payment::whereNotNull('reseller_id')
						->with('site')
						->with('plan')
						->with('reseller')
						->with('infocurrency')
						->findOrFail($id);

		$resellers = \App\Models\Reseller::orderBy('name')->lists('name','id')->all();

		return view('admin.sites.payments.edit', compact('payment','resellers'));
	}

	public function postSave($id)
	{
echo "<pre>";
print_r($this->request->all());
echo "</pre>";
die;

		$payment = \App\Models\Site\Payment::whereNotNull('reseller_id')
						->where('reseller_paid',0)
						->with('site')
						->with('plan')
						->with('reseller')
						->with('infocurrency')
						->find($id);
		if ( !$payment )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$validator = \Validator::make($this->request->all(), [
			'reseller_date' => 'required|date_format:"Y-m-d"',
		]);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$payment->update([
			'reseller_paid' => 1,
			'reseller_date' => $this->request->input('reseller_date'),
		]);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}
}
