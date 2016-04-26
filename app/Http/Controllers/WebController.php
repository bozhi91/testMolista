<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WebController extends Controller
{


    public function __initialize() {
    	parent::__initialize();
    	
		$search_data = [
			'states' => \App\Models\Geography\State::enabled()->orderBy('name')->lists('name','slug')->toArray(),
			'modes' => \App\Property::getModeOptions(),
			'types' => \App\Property::getTypeOptions(),
			'prices' => \App\Property::getPriceOptions(),
			'sizes' => \App\Property::getSizeOptions(),
			'rooms' => \App\Property::getRoomOptions(),
			'baths' => \App\Property::getBathOptions(),
			'services' => \App\Models\Property\Service::enabled()->withTranslations()->orderBy('title')->lists('title','slug')->toArray(),
		];

		\View::share('search_data', $search_data);

		if ( $site_id = \App\Session\Site::get('site_id', false) )
		{
			$this->site = \App\Site::findOrFail( $site_id );
		}
    }

	public function index()
	{
		$properties = $this->site->properties()->enabled()->highlighted()->with('images')->with('state')->with('city')->orderByRaw("RAND()")->get();

		$latest = $this->site->properties()->enabled()->with('images')->with('state')->with('city')->orderBy('created_at','desc')->limit(3)->get();

		return view('web.index', compact('properties','latest'));
	}

}
