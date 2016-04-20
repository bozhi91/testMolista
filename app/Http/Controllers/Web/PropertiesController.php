<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;

class PropertiesController extends WebController
{

	public function index()
	{
		$query = $this->site->properties()->withTranslations()->enabled()
					->with('state')
					->with('city')
					->with('services')
					;

		// Term => in title
		if ( $this->request->get('term') )
		{
			$query->whereTranslationLike('title', "%{$this->request->get('term')}%");
		}

		// State
		if ( $this->request->get('state') )
		{
			$query->inState( $this->request->get('state') );
		}

		// City
		if ( $this->request->get('city') )
		{
			$query->inCity( $this->request->get('city') );
		}

		// Mode => sale, rent, etc
		if ( $this->request->get('mode') )
		{
			$query->where('mode', $this->request->get('mode'));
		}

		// Type => house, appartment, etc
		if ( $this->request->get('type') )
		{
			$query->where('type', $this->request->get('type'));
		}

		// New or used
		if ( $this->request->get('newly_build') && $this->request->get('second_hand') )
		{
			$query->where(function($query){
				$query->where('newly_build', 1)
						->orWhere('second_hand', 1);
			});
		} 
		// New
		elseif ( $this->request->get('newly_build') )
		{
			$query->where('newly_build', 1);
		}
		// Used
		elseif ( $this->request->get('second_hand') )
		{
			$query->where('second_hand', 1);
		}

		// Price
		if ( $this->request->get('price') )
		{
			$query->withRange('price', $this->request->get('price'));
		}

		// Size
		if ( $this->request->get('size') )
		{
			$query->withRange('size', $this->request->get('size'));
		}

		// Rooms
		if ( $this->request->get('rooms') )
		{
			$query->withRange('rooms', $this->request->get('rooms'));
		}

		// Baths
		if ( $this->request->get('baths') )
		{
			$query->withRange('baths', $this->request->get('baths'));
		}

		// Services
		if ( $this->request->get('services') )
		{
			$query->withServices($this->request->get('services'));
		}

		$properties = $query->orderBy('title')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$hide_advanced_search_modal = true;

		return view('web.properties.index', compact('properties','hide_advanced_search_modal'));
	}

	public function details($slug)
	{
		$property = $this->site->properties()->withTranslations()->enabled()
					->with('state')
					->with('city')
					->with('services')
					->with('images')
					->whereTranslation('slug', $slug)
					->first();
		if ( !$property )
		{
			abort(404);
		}

		// If slug is from another language, redirect
		if ( $slug != $property->slug )
		{
			return redirect()->to(action('Web\PropertiesController@details', $property->slug), 301); 
		}

		return view('web.properties.details', compact('property'));
	}

}
