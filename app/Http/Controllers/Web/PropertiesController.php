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
			// Price
			if ( $this->request->input("price.{$this->request->get('mode')}") )
			{
				$query->withPriceBetween($this->request->input("price.{$this->request->get('mode')}"), $this->request->get('currency'));
			}
		}

		// Type => house, appartment, etc
		if ( $this->request->get('type') )
		{
			$query->where('type', $this->request->get('type'));
		}

		// New construction or used
		if ( $this->request->get('newly_build') && $this->request->get('second_hand') )
		{
			$query->where(function($query){
				$query->where('newly_build', 1)
						->orWhere('second_hand', 1);
			});
		} 
		// New construction
		elseif ( $this->request->get('newly_build') )
		{
			$query->where('newly_build', 1);
		}
		// Used
		elseif ( $this->request->get('second_hand') )
		{
			$query->where('second_hand', 1);
		}

		// New item
		if ( $this->request->get('new_item') )
		{
			$query->where('new_item', 1);
		}

		// Opportunity
		if ( $this->request->get('opportunity') )
		{
			$query->where('opportunity', 1);
		}

		// Size
		if ( $this->request->get('size') )
		{
			$query->withSizeBetween($this->request->get('size'), $this->request->get('size_unit'));
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

		// Sort order
		$order = $this->request->get('order');
		if ( $order && array_key_exists($order, \App\Property::getSortOptions()) ) 
		{
			list ($field,$sense) = explode('-',$order,2);
			$query->orderBy($field,$sense);
		}
		else
		{
			$query->orderBy('properties.highlighted','desc')->orderBy('title');
		}

		$properties = $query->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

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

		$this->set_seo_values([
			'title' => $property->title . (empty($this->site->title) ? '' : " - {$this->site->title}"),
			'description' => $property->description,
		]);

		return view('web.properties.details', compact('property'));
	}

	public function moreinfo($slug)
	{
		// Get property
		$property = $this->site->properties()->withTranslations()->enabled()
					->whereTranslation('slug', $slug)
					->first();
		if ( !$property )
		{
			return [ 'error'=>true ];
		}

		// Validate user
		$validator = \Validator::make($this->request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'message' => 'required'
		]);
		if ($validator->fails()) 
		{
			return [ 'error'=>true ];
		}

		// No customer, create
		$customer = $this->site->customers()->where('email', $this->request->get('email'))->first();
		if ( !$customer )
		{
			$customer = $this->site->customers()->create([
				'locale' => \LaravelLocalization::getCurrentLocale(),
				'first_name' => $this->request->get('first_name'),
				'last_name' => $this->request->get('last_name'),
				'email' => $this->request->get('email'),
				'phone' => $this->request->get('phone'),
			]);
			if ( !$customer )
			{
				return [ 'error'=>true ];
			}
		}

		// Assign customer to property
		if ( !$property->customers->contains( $customer->id ) )
		{
			$property->customers()->attach( $customer->id );
		}

		// Push job to queue
		$data = array_merge($this->request->all(), [
			'locale' => \LaravelLocalization::getCurrentLocale(),
		]);
		$job = ( new \App\Jobs\SendMoreInfoProperty($property, $customer, $data) )->onQueue('emails');
		$this->dispatch( $job );

		return [ 'success'=>true ];
	}

}
