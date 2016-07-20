<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExpirationsController extends Controller
{

	public function getIndex()
	{
		$query = \App\Site::with('plan')->whereNotNull('paid_until');

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

}
