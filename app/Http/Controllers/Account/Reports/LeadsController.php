<?php

namespace App\Http\Controllers\Account\Reports;

use Illuminate\Http\Request;

use App\Http\Requests;

class LeadsController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'reports');
		\View::share('submenu_subsection', 'reports-leads');
	}

	public function getIndex()
	{
		$current_tab = 'general';

		$stats = [
			'7-days' => $this->site->stats()->selectRaw('SUM(`leads`) as total_leads')->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*7) ) )->value('total_leads'),
			'30-days' => $this->site->stats()->selectRaw('SUM(`leads`) as total_leads')->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*30) ) )->value('total_leads'),
			'60-days' => $this->site->stats()->selectRaw('SUM(`leads`) as total_leads')->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*60) ) )->value('total_leads'),
			'90-days' => $this->site->stats()->selectRaw('SUM(`leads`) as total_leads')->whereDate('date', '>=', date('Y-m-d', time() - (60*60*24*90) ) )->value('total_leads'),
			'year-to-date' => $this->site->stats()->selectRaw('SUM(`leads`) as total_leads')->whereDate('date', '>=', date('Y').'-01-01' )->value('total_leads'),
			'current' => $this->site->customers()->count(),
		];

		return view('account.reports.leads.index', compact('stats','current_tab'));
	}

}
