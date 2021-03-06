<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Log;

class PropertiesController extends WebController
{
    public function getAgencyById(){
        $value = session('SiteSetup');
        Log::Info($value['site_id']);
        return $value['site_id'];
    }
	public function index()
	{
		$query = $this->site->properties()->enabled()
					->with('state')
					->with('city')
					->with('services')
					;

		// Term
		if ( $this->request->input('term') )
		{
			$query->withTermLike($this->request->input('term'));
		}

		// State
		$search_data_cities = [];
		if ( $this->request->input('state') )
		{
			$query->inState( $this->request->input('state') );

			$cities = \App\Models\Geography\City::enabled();
			$cities->withStateSlug( $this->request->input('state') );
			$site_id = $this->site->id;
			$cities->whereIn('cities.id', function($query) use ($site_id) {
				$query->distinct()->select('city_id')->from('properties')->where('site_id', $site_id)->where('enabled', 1);
			});
			$search_data_cities = $cities->select('cities.id','cities.name AS label','cities.slug AS code')
				->orderBy('cities.name')
				->get()->pluck('label', 'code')->toArray();
		}

		// City
		if ( $this->request->input('city') )
		{
			$query->inCity( $this->request->input('city') );
		}

		// Mode => sale, rent, etc
		if ( $this->request->input('mode') )
		{
			$query->where('mode', $this->request->input('mode'));
			// Price
			if ( $this->request->input("price.{$this->request->input('mode')}") )
			{
				$query->withPriceBetween($this->request->input("price.{$this->request->input('mode')}"), $this->request->input('currency'));
			}
		}

		// Type => house, appartment, etc
		if ( $this->request->input('type') )
		{
			$query->where('type', $this->request->input('type'));
		}

		// New construction or used
		if ( $this->request->input('newly_build') && $this->request->input('second_hand') )
		{
			$query->where(function($query){
				$query->where('newly_build', 1)
						->orWhere('second_hand', 1);
			});
		}
		// New construction
		elseif ( $this->request->input('newly_build') )
		{
			$query->where('newly_build', 1);
		}
		// Used
		elseif ( $this->request->input('second_hand') )
		{
			$query->where('second_hand', 1);
		}

		// New item
		if ( $this->request->input('new_item') )
		{
			$query->where('new_item', 1);
		}

		// Opportunity
		if ( $this->request->input('opportunity') )
		{
			$query->where('opportunity', 1);
		}

		// Bank owned
		if ( $this->request->input('bank_owned') )
		{
			$query->where('bank_owned', 1);
		}

		// Private owned
		if ( $this->request->input('private_owned') )
		{
			$query->where('private_owned', 1);
		}

		// Size
		if ( $this->request->input('size') )
		{
			$query->withSizeBetween($this->request->input('size'), $this->request->input('size_unit'));
		}

		// Rooms
		if ( $this->request->input('rooms') )
		{
			$query->withRange('rooms', $this->request->input('rooms'));
		}

		// Baths
		if ( $this->request->input('baths') )
		{
			$query->withRange('baths', $this->request->input('baths'));
		}

		// Services
		if ( $this->request->input('services') )
		{
			$query->withServices($this->request->input('services'));
		}

		//Districts
		if($this->request->input('district')){
			$query->where('district_id', $this->request->input('district'));
		}

		// Sort order
		$order = $this->request->input('order');
		if ( $order && array_key_exists($order, \App\Property::getSortOptions()) )
		{
			list ($field,$sense) = explode('-',$order,2);
			$query->orderBy($field,$sense);
		}
		else
		{
			// Fincas Bellamar...
			if ($this->site->id == env('FINCAS_BELLAMAR_ID')) {
				$query->orderBy('price','desc');
			} else {
				$query->orderBy('properties.highlighted','desc')->orderBy('title');
			}
		}

		$properties = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$hide_advanced_search_modal = true;

		return view('web.properties.index', compact('properties','hide_advanced_search_modal','search_data_cities'));
	}

	public function details($slug, $id = false)
	{

		if ( !$id )
		{
			if ( $property = $this->site->properties()->enabled()->whereTranslation('slug', $slug)->first() )
			{
				return redirect()->to($property->full_url, 301);
			}

			abort(404);
		}

		$property = $this->site->properties()->enabled()
					->with('state')
					->with('city')
					->with('services')
					->with('images')
					->with('videos')
					->findOrFail($id);

		// If slug is from another language
		if ( $property->slug != $slug )
		{
			return redirect()->to($property->full_url, 301);
		}

		$media = $property->media();

		$this->set_seo_values([
			'title' => $property->title . (empty($this->site->title) ? '' : " - {$this->site->title}"),
			'description' => $property->description,
		]);

		$og = new \ChrisKonnertz\OpenGraph\OpenGraph();

		$og->title($property->title)
			->type('article')
			->image($property->mainImage)
			->description($property->description)
			->url($property->full_url);

        $agency = $this->getAgencyById();

		return view('web.properties.details', compact('property', 'og', 'media','agency'));
	}

	public function moreinfo($slug)
	{
		// Get property
		$property = $this->site->properties()->enabled()
					->whereTranslation('slug', $slug)
					->first();
		if ( !$property )
		{
			return [ 'error'=>true ];
		}

		$fields = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'message' => 'required'
		];

		if ($this->site->recaptcha_enabled) {
			$fiels['g-recaptcha-response'] = 'required|recaptcha';
		}

		// Validate user
		$validator = \Validator::make($this->request->all(), $fields);
		
		if ($validator->fails())
		{
			return [ 'error'=>true ];
		}

		// No robots
		if ( $this->request->input('accept_legal_terms') )
		{
			return [ 'error'=>true ];
		}

		// No customer, create
		$customer = $this->site->customers()->where('email', $this->request->input('email'))->first();
		if ( !$customer )
		{
			$customer = $this->site->customers()->create([
				'locale' => \LaravelLocalization::getCurrentLocale(),
				'first_name' => $this->request->input('first_name'),
				'last_name' => $this->request->input('last_name'),
				'email' => $this->request->input('email'),
				'phone' => $this->request->input('phone'),
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

	public function sharefriend($slug)
	{
		// Get property
		$property = $this->site->properties()->enabled()
					->whereTranslation('slug', $slug)
					->first();
		
		if ( !$property ){
			return [ 'error'=>true ];
		}
		
		if(!empty($this->request->input('f_email'))){
			return [ 'success'=>true ]; //honey pot
		}
		
		// Validate user
		$validator = \Validator::make($this->request->all(), [		
			'r_email' => 'required|email',
			'email' => 'required|email',
			'link' => 'required',
			'name' => 'required',
			'message' => 'required'
		]);
		
		if ($validator->fails()){
			return [ 'error'=>true ];
		}
		
		$data = array_merge($this->request->all(), [
			'locale' => \LaravelLocalization::getCurrentLocale(),
		]);
		$job = (new \App\Jobs\SendRecommendProperty($property, $data))->onQueue('emails');
		$this->dispatch($job);
				
		return [ 'success'=>true ];
	}
	
	
	public function downloads($slug,$locale)
	{
		// Get property
		$property = $this->site->properties()->enabled()
					->whereTranslation('slug', $slug)
					->first();
		if ( !$property )
		{
			abort(404);
		}

		$filepath = $property->getPdfFile( $locale );

		return response()->download($filepath, "property-{$locale}.pdf", [
			'Content-Type: application/pdf',
		]);
	}

}
