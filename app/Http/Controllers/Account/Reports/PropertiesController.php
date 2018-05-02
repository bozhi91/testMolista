<?php

namespace App\Http\Controllers\Account\Reports;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Log;

class PropertiesController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'reports');
		\View::share('submenu_subsection', 'reports-properties');
	}

	public function getIndex()
	{
		$current_tab = 'general';

		$yesterday =  time() - (60*60*24);
		$stats = $this->site->stats()->whereDate('date','=',date('Y-m-d',$yesterday))->first();

		return view('account.reports.properties.index', compact('stats','current_tab'));
	}

	public function getPeriod($period=false)
	{
		$current_tab = 'time';

		$fields = [
			'SUM(`sale`) as published_sale',
			'SUM(`rent`) as published_rent',
			'SUM(`sale_closed`) as total_sold',
			'SUM(`rent_closed`) as total_rented',
		];

		$query = $this->site->stats()->selectRaw( implode(', ',$fields) );

		switch ( $period )
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
			default:
				return redirect()->action('Account\Reports\PropertiesController@getPeriod', [ '7-days' ], 301);
		}

		$stats = $query->first();

		return view('account.reports.properties.index', compact('stats','period','current_tab'));
	}

}
