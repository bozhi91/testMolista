<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class AdminController extends Controller
{

	public function index()
	{
		if ( !$this->request->input('daterange') )
		{
			$yesterday = time() - 86400;
			$this->request->merge([
				'daterange' => date('d/m/Y', $yesterday) . ' - ' . date('d/m/Y', $yesterday),
			]);
		}

		$query = \App\Models\Stats::with('site')->with('plan')->withDateRange( $this->request->input('daterange') );
		$query->whereNotIn('site_id', explode(',', env('EXCLUDE_SITES_FROM_STATS')));
		
		$query->whereIn('site_id', function($query){
			$subquery = $query->select('id')->from('sites');
			$subquery->where('enabled', 1);
			return $subquery;
		});
		
		
		$items = $query->get();
				
		$stats = \App\Models\Stats::getConsolidatedStats();
		
		$use_google_maps = 1;
		
		return view('admin.index', compact('stats','items','use_google_maps'));
	}

}
