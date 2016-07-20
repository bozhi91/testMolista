<?php

namespace App\Http\Controllers\Account\Reports;

use Illuminate\Http\Request;

use App\Http\Requests;

class AgentsController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'reports');
		\View::share('submenu_subsection', 'reports-agents');
	}

	public function getIndex()
	{
		$current_tab = 'general';

		$fields = [
			'SUM(`sale`) as published_sale',
			'SUM(`rent`) as published_rent',
			'SUM(`sale_closed`) as total_sold',
			'SUM(`rent_closed`) as total_rented',
			'SUM(`sale_visits`) as visits_sale',
			'SUM(`rent_visits`) as visits_rent',
		];

		$query = \App\Models\User\Stats::selectRaw( implode(', ',$fields) )->where('site_id', $this->site->id);

		if ( $this->request->input('agent') )
		{
			$query->where('user_id',$this->request->input('agent'));
		}

		switch ( $this->request->input('period', '7-days') )
		{
			case 'year-to-date':
				$query->whereDate('date', '>=', date('Y').'-01-01') ;
				break;
			case '90-days':
				$query->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*90) ) );
				break;
			case '60-days':
				$query->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*60) ) );
				break;
			case '30-days':
				$query->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*30) ) );
				break;
			case '7-days':
				$query->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*7) ) );
				break;
		}

		$stats = $query->groupBy('site_id')->first();

		$managers = $this->site->users()->orderBy('name')->lists('name','id')->all();

		return view('account.reports.agents.index', compact('stats','managers','current_tab'));
	}

}
