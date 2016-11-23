<?php namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class ReportsController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();

		\View::share('submenu_section', 'reports');
	}

	public function getIndex()
	{
		// Get last visit to this section
		$since = \App\Models\Site\UserSince::getSince($this->site->id, $this->site_user->id, 'reports');

		$stats = [
			'properties_active' => 0,
			'properties_price' => 0,
			'leads_total' => $this->site->customers()->count(),
			'leads_since' => $this->site->customers()->whereDate('created_at', '>=', $since)->count(),
			'tickets_open' => 0,
			'properties_top' => [],
		];

		// Properties stats
		$properties_stats = $this->site->properties()->enabled()
							->select('mode')
							->addSelect( \DB::raw("COUNT(*) as total_properties") )
							->addSelect( \DB::raw("SUM(`price`) as total_price") )
							->addSelect( \DB::raw("AVG(`price`) as average_price") )
							->groupBy('mode')
							->get()
							->keyBy('mode');
		foreach ($properties_stats as $key => $value) 
		{
			$stats['properties_active'] += $value->total_properties;
			$stats['properties_price'] += $value->total_price;
		}

		// Open tickets
		$tickets = $this->site->ticket_adm->getTickets([
			'limit' => 1,
			'status' => 'open',
		]);
		$stats['tickets_open'] = @intval($tickets['total_items']);

		// Properties with most leads
		$stats['properties_top'] = $this->site->properties()->enabled()
									->select('properties.*')
									->addSelect( \DB::raw("(SELECT COUNT(*) FROM properties_customers WHERE properties.id = properties_customers.property_id) AS total_customers") )
									->withEverything()
									->with('customers')
									->withTranslation()
									->orderBy('total_customers','desc')
									->limit(10)
									->get();


		// Set last visit to this section
		\App\Models\Site\UserSince::setSince($this->site->id, $this->site_user->id, 'reports');


		return view('account.reports.index', compact('stats'));
	}
	
	
	
	
	public function getTransactions($mode){
		return view('account.reports.agents.transaction');
	}
	
	
	

}
