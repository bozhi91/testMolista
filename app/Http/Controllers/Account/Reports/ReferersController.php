<?php namespace App\Http\Controllers\Account\Reports;

use Illuminate\Http\Request;

use App\Http\Requests;

class ReferersController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'reports');
		\View::share('submenu_subsection', 'reports-referers');
	}

	public function getIndex()
	{

		switch ( $period = $this->request->input('period', 'year-to-date') )
		{
			case 'year-to-date':
				$date_query = date('Y').'-01-01';
				break;
			case '90-days':
				$date_query = date('Y-m-d', time() - (60*60*24*90) );
				break;
			case '60-days':
				$date_query = date('Y-m-d', time() - (60*60*24*60) );
				break;
			case '30-days':
				$date_query = date('Y-m-d', time() - (60*60*24*30) );
				break;
			case '7-days':
				$date_query = date('Y-m-d', time() - (60*60*24*7) );
				break;
			default:
				return redirect()->action('Account\Reports\ReferersController@getIndex', [ 'period'=>'year-to-date' ], 301);
		}

		$status = \App\Models\Property\Catches::select( \DB::raw("`status`, 0 as total") )
					->whereNotNull('transaction_date')
					->ofSite($this->site->id)
					->groupBy('status')
					->orderBy('status','asc')
					->lists('total','status')
					->all();

		$stats = [];
		foreach ($this->site->customers()->select('origin')->distinct()->orderBy('origin','asc')->get() as $item)
		{
			$stats[$item->origin] = array_merge([
				'origin' => $item->origin,
				'leads' => 0,
			], $status);
		}

		if ( $stats )
		{

			$query = $this->site->customers()->select( \DB::raw("`origin`, COUNT(*) as leads") )->whereDate('created_at', '>=', $date_query);

			foreach ($query->get() as $item)
			{
				if ( !array_key_exists($item->origin, $stats) )
				{
					continue;
				}

				$stats[$item->origin]['leads'] = $item->leads;
			}

			$query = \App\Models\Property\Catches::select( \DB::raw("properties_catches.`status`, customers.`origin`, COUNT(*) as total") )
						->whereNotNull('transaction_date')
						->ofSite($this->site->id)
						->whereDate('transaction_date', '>=', $date_query)
						->join('customers', 'properties_catches.buyer_id', '=', 'customers.id')
						->groupBy('customers.origin')->groupBy('properties_catches.status');
			foreach ($query->get() as $item)
			{
				if ( !array_key_exists($item->origin, $stats) )
				{
					continue;
				}

				$stats[$item->origin][$item->status] = $item->total;
			}

		}

		$current_tab = 'general';

		return view('account.reports.referers.index', compact('stats','period','current_tab'));
	}

}
