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

	protected function csv_prepare_row($row)
	{
		$row->plan_name = @$row->plan->name;
		$row->interval = trans("web/plans.price.{$row->payment_interval}");
		$row->method = trans("account/payment.method.{$row->payment_method}");
		return $row;
	}

}
