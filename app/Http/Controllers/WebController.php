<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WebController extends Controller
{


    public function __initialize() {
    	parent::__initialize();
    	
		$search_data = [
			'modes' => \App\Property::getModeOptions(),
			'types' => \App\Property::getTypeOptions(),
			'sizes' => \App\Property::getSizeOptions(),
			'rooms' => \App\Property::getRoomOptions(),
			'baths' => \App\Property::getBathOptions(),
			'services' => \App\Models\Property\Service::enabled()->withTranslations()->orderBy('title')->lists('title','slug')->toArray(),
		];

		if ( $site_id = \App\Session\Site::get('site_id', false) )
		{
			$this->site = \App\Site::withTranslations()->findOrFail( $site_id );
			$search_data['states'] = \App\Models\Geography\State::enabled()->whereIn('id', function($query) use ($site_id) {
				$query->distinct()->select('state_id')->from('properties')->where('site_id', 1)->where('enabled', $site_id);
			})->orderBy('name')->lists('name','slug')->all();
			$search_data['prices'] = \App\Property::getPriceOptions($site_id);
		}
		else
		{
			$search_data['states'] = \App\Models\Geography\State::enabled()->orderBy('name')->lists('name','slug')->all();
			$search_data['prices'] = \App\Property::getPriceOptions();
		}

		\View::share('search_data', $search_data);
    }

	public function index()
	{
		$properties = $this->site->properties()->enabled()->highlighted()->with('images')->with('state')->with('city')->withTranslations()->orderByRaw("RAND()")->get();

		$latest = $this->site->properties()->enabled()->with('images')->with('state')->with('city')->withTranslations()->orderBy('created_at','desc')->limit(3)->get();

		return view('web.index', compact('properties','latest'));
	}

}
