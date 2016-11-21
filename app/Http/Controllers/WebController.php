<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WebController extends Controller
{


    public function __initialize() {
    	parent::__initialize();

		$search_data = [
			'sizes' => \App\Property::getSizeOptions(),
			'rooms' => \App\Property::getRoomOptions(),
			'baths' => \App\Property::getBathOptions(),
			'services' => \App\Models\Property\Service::enabled()->withTranslations()->orderBy('title')->lists('title','slug')->toArray(),
			'sort_options' => \App\Property::getSortOptions(),
		];

		if ( $this->site )
		{
			$search_data['states'] = \App\Models\Geography\State::enabled()->whereIn('id', function($query) {
				$query->distinct()->select('state_id')->from('properties')->where('site_id', $this->site->id)->where('enabled', 1);
			})->orderBy('name')->lists('name','slug')->all();
			$search_data['prices'] = \App\Property::getPriceOptions($this->site->id);
			$search_data['types'] = \App\Property::getTypeOptions($this->site->id);
			$search_data['modes'] = \App\Property::getModeOptions($this->site->id);
		}
		else
		{
			$search_data['states'] = \App\Models\Geography\State::enabled()->orderBy('name')->lists('name','slug')->all();
			$search_data['prices'] = \App\Property::getPriceOptions();
			$search_data['types'] = \App\Property::getTypeOptions();
			$search_data['modes'] = \App\Property::getModeOptions();
		}

		\View::share('search_data', $search_data);
    }

	public function index()
	{
        $sliders = false;
    	if(!empty($this->site->site_setup['widgets']['home']))
        {
    		foreach ($this->site->site_setup['widgets']['home'] as $widget)
            {
    			if ($widget['type'] == 'slider')
                {
    				$sliders = $widget;
    			}
    		}
    	}

        $main_property = false;
        if (!$sliders)
        {
            $main_property = $this->site->properties()->enabled()->inHome()->with('images')->with('state')->with('city')->orderByRaw("RAND()")->first();
            if ( !$main_property )
            {
                $main_property = $this->site->properties()->enabled()->highlighted()->with('images')->with('state')->with('city')->orderByRaw("RAND()")->first();
            }
        }

		$exclude = $main_property ? $main_property->id : 0;
		$highlighted = $this->site->properties()->where('properties.id', '!=', $exclude)->enabled()->highlighted()->with('images')->with('state')->with('city')->orderByRaw("RAND()")->get();

		$latest = $this->site->properties()->enabled()->with('images')->with('state')->with('city')->orderBy('created_at','desc')->limit(3)->get();

		return view('web.index', compact('main_property','highlighted','latest','sliders'));
	}

}
