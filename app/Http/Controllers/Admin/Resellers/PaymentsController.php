<?php namespace App\Http\Controllers\Admin\Resellers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:reseller-payments' ]);

		parent::__initialize();
	}

    public function getIndex()
	{
		$query = \App\Models\Site\Payment::whereNotNull('sites_payments.reseller_id')
						->with('site')
						->with('reseller')
						->with('infocurrency')
						;

		// Filter by site
		if ( $this->request->input('website') )
		{
			$query->whereIn('sites_payments.site_id', function($query) {
				$query->select('id')
					->from('sites')
					->whereIn('id', function($query){
						$query->select('site_id')
							->from('sites_domains')
							->where('domain', 'like', "%{$this->request->input('website')}%");
					})
					->orWhere('subdomain', 'like', "%{$this->request->input('website')}%");
			});
		}

		// Filter by reseller
		if ( $this->request->input('reseller') )
		{
			$query->whereIn('sites_payments.reseller_id', function($query) {
				$query->select('id')
					->from('resellers')
					->where('name', 'like', "%{$this->request->input('reseller')}%");
			});
		}

		// Filter by payment
		if ( $this->request->input('paid') )
		{
			$query->where('sites_payments.reseller_paid', intval($this->request->input('paid')-1));
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
			case 'reseller':
				$query
					->join('resellers', 'sites_payments.reseller_id', '=', 'resellers.id')
					->orderBy('resellers.name', $order);
				break;
			case 'amount_pending':
				$query->orderByRaw("IF (sites_payments.`reseller_paid`=1, 0, (sites_payments.`reseller_amount` * sites_payments.`reseller_rate`)) {$order}");
				break;
			case 'amount_paid':
				$query->orderByRaw("IF (sites_payments.`reseller_paid`=1, (sites_payments.`reseller_amount` * sites_payments.`reseller_rate`), 0) {$order}");
				break;
			case 'paid':
				$query->orderByRaw("IF (sites_payments.`reseller_paid`=1, sites_payments.`reseller_date`, '1970-01-01 00:00:00') {$order}");
				break;
			case 'created':
			default:
				$query->orderBy('created_at', $order);
		}

		$payments = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$comissions_currency = \App\Models\Currency::where('code', 'EUR')->first();
		$comissions_currency->decimals = 2;

		$this->set_go_back_link();

		return view('admin.resellers.payments.index', compact('payments', 'comissions_currency'));
	}

	public function getShow($id)
	{
		$payment = \App\Models\Site\Payment::whereNotNull('reseller_id')
						->with('site')
						->with('plan')
						->with('reseller')
						->with('infocurrency')
						->findOrFail($id);

		return view('admin.resellers.payments.show', compact('payment'));
	}

	public function postPay($id)
	{
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

	public function postPayBatch()
	{
		$validator = \Validator::make($this->request->all(), [
			'reseller_date' => 'required|date_format:"Y-m-d"',
			'payments' => 'required|array'
		]);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$payments = \App\Models\Site\Payment::whereNotNull('reseller_id')
			->where('reseller_paid', 0)
			->with('site')
			->with('plan')
			->with('reseller')
			->with('infocurrency')
			->whereIn('id', $this->request->get('payments'))
			->get();
		if ( !$payments )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		foreach ($payments as $payment) {
			$payment->update([
				'reseller_paid' => 1,
				'reseller_date' => $this->request->input('reseller_date'),
			]);
		}

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}
}
