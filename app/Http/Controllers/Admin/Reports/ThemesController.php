<?php namespace App\Http\Controllers\Admin\Reports;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ThemesController extends Controller
{

    public function getIndex()
	{
		$stats = \App\Site::select( \DB::raw("`theme`, COUNT(*) as total") )
						->groupBy('theme')
						->lists('total','theme')
						->all();

		$themes = [];
		foreach (\Config::get('themes.themes') as $key => $theme)
		{
			// Only public or custom themes
			if ( @$theme['public'] || @$theme['custom'] )
			{
				$themes[$key] = [
					'key' => $key,
					'title' => $theme['title'],
					'total' => @intval($stats[$key]),
				];
			}
		}

		uasort($themes, function ($a, $b) {
			if ( $a['total'] != $b['total'] )
			{
				return $a['total'] < $b['total'] ? 1 : -1;
			}

			return strcmp($a['title'], $b['title']);
		});

		return view('admin.reports.themes.index', compact('themes'));
	}

}
