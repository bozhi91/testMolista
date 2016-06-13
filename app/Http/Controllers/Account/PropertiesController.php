<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class PropertiesController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		$this->middleware([ 'permission:property-view' ], [ 'only' => [ 'index','show','postCatch' ] ]);
		$this->middleware([ 'permission:property-create', 'property.permission:create' ], [ 'only' => [ 'create','store' ] ]);
		$this->middleware([ 'permission:property-edit' ], [ 'only' => [ 'edit','update','getAssociate','postAssociate' ] ]);
		$this->middleware([ 'property.permission:edit' ], [ 'only' => [ 'update','getAssociate','postAssociate','getChangeStatus' ] ]);
		$this->middleware([ 'permission:property-delete', 'property.permission:delete' ], [ 'only' => [ 'destroy' ] ]);

		\View::share('submenu_section', 'properties');
	}

	public function index()
	{
		$clean_filters = false;

		$query = $this->site->properties()
							->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))
							->with('customers')
							->with('state')
							->with('city')
							->withTranslations()
							->leftJoin('cities','properties.city_id','=','cities.id')
							->addSelect('cities.name AS city_name');

		// Filter by reference
		if ( $this->request->get('ref') )
		{
			$clean_filters = true;
			$query->where('properties.ref', 'LIKE', "%{$this->request->get('ref')}%");
		}

		// Filter by title
		if ( $this->request->get('title') )
		{
			$clean_filters = true;
			$query->whereTranslationLike('title', "%{$this->request->get('title')}%");
		}

		// Filter by highlighted
		if ( $this->request->get('highlighted') )
		{
			$clean_filters = true;
			$query->where('properties.highlighted', intval($this->request->get('highlighted'))-1);
		}

		// Filter by highlighted
		if ( $this->request->get('enabled') )
		{
			$clean_filters = true;
			$query->where('properties.enabled', intval($this->request->get('enabled'))-1);
		}

		switch ( $this->request->get('order') )
		{
			case 'desc':
				$order = 'desc';
				break;
			default:
				$order = 'asc';
		}

		switch ( $this->request->get('orderby') )
		{
			case 'reference':
				$query->orderBy('ref', $order);
				break;
			case 'creation':
				$query->orderBy('created_at', $order);
				break;
			case 'location':
				$query->orderBy('city_name', $order);
				break;
			case 'lead':
				$query->leftJoin('properties_customers','properties.id','=','properties_customers.property_id')
						->addSelect( \DB::raw('COUNT(properties_customers.customer_id) AS customers_total') )
						->groupBy('properties.id')
						->orderBy('customers_total', $order);
				break;
			case 'title':
			default:
				$query->orderBy('title', $order);
				break;
		}

		$properties = $query->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('account.properties.index', compact('properties','clean_filters'));
	}

	public function create()
	{
		$modes = \App\Property::getModeOptions();
		$types = \App\Property::getTypeOptions();
		$energy_types = \App\Property::getEcOptions();
		$services = \App\Models\Property\Service::withTranslations()->enabled()->orderBy('title')->get();

		$countries = \App\Models\Geography\Country::withTranslations()->enabled()->orderBy('name')->lists('name','id');
		if ( $country_id = old('country_id', \App\Models\Geography\Country::where('code','ES')->value('id')) )
		{
			$states = \App\Models\Geography\State::enabled()->where('country_id', $country_id)->lists('name','id');
		}
		if ( old('state_id') )
		{
			$cities = \App\Models\Geography\City::enabled()->where('state_id', old('state_id'))->lists('name','id');
		}

		$managers = $this->site->users()->orderBy('name')->lists('name','id')->all();

		return view('account.properties.create', compact('modes','types','energy_types','services','countries','states','cities','country_id','managers'));
	}

	public function store()
	{
		// Validate request
		$valid = $this->validateRequest();
		if ( $valid !== true ) 
		{
			return redirect()->back()->withInput()->withErrors($valid);
		}

		// Validate catch values
		$catch_fields = [
			'employee_id' => 'required|exists:users,id',
			'seller_first_name' => 'required',
			'seller_last_name' => 'required',
			'seller_email' => 'required|email',
			'seller_id_card' => '',
			'seller_phone' => '',
			'seller_cell' => '',
			'price_min' => 'numeric|min:1',
			'commission' => 'integer|between:0,100',
		];
		$validator = \Validator::make($this->request->all(), $catch_fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($valid);
		}

		// Create element
		$property = $this->site->properties()->create([
			'enabled' => 1,
			'publisher_id' => $this->request->get('employee_id'),
			'published_at' => date('Y-m-d'),
		]);

		if ( empty($property->id) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Save
		if ( !$this->saveRequest($property) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// If creator is employee
		if ( $this->auth->user()->hasRole('employee') )
		{
			$this->auth->user()->properties()->attach( $property->id );
		}

		// create catch
		$catch = $property->catches()->create([
			'employee_id' => $this->request->get('employee_id'),
			'catch_date' => $property->created_at,
			'price_original' => $this->request->get('price'),
			'status' => 'active',
		]);

		$data = [];
		foreach ($catch_fields as $field => $def) 
		{
			$data[$field] = $this->request->get($field);
		}
		$catch->update($data);

		$property = $this->site->properties()->withTranslations()->find($property->id);

		return redirect()->action('Account\PropertiesController@edit', $property->slug)->with('current_tab', $this->request->get('current_tab'))->with('success', trans('account/properties.created'));
	}

	public function edit($slug)
	{
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->withTranslations()->first();
		if ( !$property )
		{
			abort(404);
		}

		$modes = \App\Property::getModeOptions();
		$types = \App\Property::getTypeOptions();
		$energy_types = \App\Property::getEcOptions();
		$services = \App\Models\Property\Service::withTranslations()->enabled()->orderBy('title')->get();

		$countries = \App\Models\Geography\Country::withTranslations()->enabled()->orderBy('name')->lists('name','id');
		$states = \App\Models\Geography\State::enabled()->where('country_id', $property->country_id)->lists('name','id');
		$cities = \App\Models\Geography\City::enabled()->where('state_id', $property->state_id)->lists('name','id');

		return view('account.properties.edit', compact('property','modes','types','energy_types','services','countries','states','cities'));
	}

	public function update(Request $request, $slug)
	{
		// Get property
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			abort(404);
		}

		// Validate request
		$valid = $this->validateRequest($property->id);
		if ( $valid !== true ) 
		{
			return redirect()->back()->withInput()->withErrors($valid);
		}

		// Save
		if ( !$this->saveRequest($property) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$property = $this->site->properties()->withTranslations()->find($property->id);

		return redirect()->action('Account\PropertiesController@edit', $property->slug)->with('current_tab', $this->request->get('current_tab'))->with('success', trans('account/properties.saved'));
	}

	public function show($slug)
	{
		// Get property
		$property = $this->site->properties()
						->withTrashed()
						->whereTranslation('slug', $slug)
						->withTranslations()
						->with([ 'translations' => function($query){
							$query->with('logs');
						}])
						->with('logs')
						->with([ 'catches' => function($query){
							$query->with('buyer');
						}])
						->with('customers')
						->first();
		if ( !$property )
		{
			abort(404);
		}

		return view('account.properties.show', compact('property'));
	}

	public function getLeads($slug)
	{
		// Get property
		$property = $this->site->properties()
						->withTrashed()
						->whereTranslation('slug', $slug)
						->withTranslations()
						->with('customers')
						->first();
		if ( $property )
		{
			$customers = $property->customers->sortBy('full_name');
		}

		return view('account.properties.show-leads', compact('customers'));
	}

	public function getCatch($property_id, $id=false)
	{
		$property = $this->site->properties()->findOrFail( $property_id );

		if ( $id )
		{
			$item = \App\Models\Property\Catches::ofSite($this->site->id)->findOrFail($id);
		} 
		
		return view('account.properties.catch', compact('property','item'));
	}
	public function postCatch($property_id, $id=false)
	{
		$property = $this->site->properties()->findOrFail( $property_id );

		if ( $id )
		{
			$item = \App\Models\Property\Catches::ofSite($this->site->id)->findOrFail($id);
		}

		$fields = [
			'seller_first_name' => 'required',
			'seller_last_name' => 'required',
			'seller_email' => 'required|email',
			'seller_id_card' => '',
			'seller_phone' => '',
			'seller_cell' => '',
			'price_min' => 'numeric|min:1',
			'commission' => 'integer|between:0,100',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return [ 'error'=>true ];
		}

		if ( $id )
		{
			$item = \App\Models\Property\Catches::ofSite($this->site->id)->findOrFail($id);
		}
		else
		{
			$item = $property->catches()->create([
				'employee_id' => \Auth::user()->id,
				'catch_date' => date('Y-m-d H:i:s'),
				'price_original' => $property->price,
				'status' => 'active',
			]);
		}

		$data = [];
		foreach ($fields as $field => $def) 
		{
			$data[$field] = $this->request->get($field);
		}

		$item->update($data);

		return [ 'success'=>true ];
	}

	public function getCatchClose($id)
	{
		if ( $id )
		{
			$item = \App\Models\Property\Catches::ofSite($this->site->id)->with('property')->findOrFail($id);
		}

		$managers = $this->site->users()->orderBy('name')->lists('name','id')->all();

		$customers = [];
		foreach ($this->site->customers as $customer)
		{
			$customers[$customer->id] = $customer->fullname;
		}

		return view('account.properties.catch-close', compact('item','managers','customers'));
	}
	public function postCatchClose($id)
	{
		$item = \App\Models\Property\Catches::ofSite($this->site->id)->findOrFail($id);

		$fields = [
			'transaction_date' => 'required:date',
			'status' => 'required|in:sold,rent,other',
			'closer_id' => 'exists:users,id',
		];
		switch ( $this->request->get('status') )
		{
			case 'sold':
			case 'rent':
				$fields['buyer_id'] = 'exists:customers,id,site_id,'.$this->site->id;
				$fields['price_sold'] = 'numeric|min:1';
				break;
			case 'other':
				$fields['reason'] = 'required';
				break;
		}
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return [ 'error'=>true ];
		}

		$data = [];
		foreach ($fields as $field => $def) 
		{
			$data[$field] = $this->request->get($field);
		}

		$item->update($data);

		// Add KPIs
		switch ( $this->request->get('status') )
		{
			case 'sold':
			case 'rent':
				// Save current KPIs
				$data['leads_to_close'] = $item->leads_total;
				$data['discount_to_close'] = ( ($item->price_original - $data['price_sold']) / $item->price_original ) * 100;
				$data['days_to_close'] = ( strtotime($data['transaction_date']) - strtotime($item->catch_date->format('Y-m-d')) ) / (60*60*24);
				$item->update($data);
				// Save averages, including current KPIs
				$data['leads_average'] = @floatval( \App\Models\Property\Catches::ofSite($this->site->id)->whereNotNull('leads_to_close')->avg('leads_to_close') );
				$data['discount_average'] = @floatval( \App\Models\Property\Catches::ofSite($this->site->id)->whereNotNull('discount_to_close')->avg('discount_to_close') );
				$data['days_average'] = @floatval( \App\Models\Property\Catches::ofSite($this->site->id)->whereNotNull('days_to_close')->avg('days_to_close') );
				$item->update($data);
				break;
		}

		return [ 'success'=>true ];
	}

	public function destroy($slug)
	{
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			abort(404);
		}

		// Remove image folder
		\File::deleteDirectory($property->image_path);

		// Delete property
		$property->delete();

		return redirect()->action('Account\PropertiesController@index')->with('success', trans('account/properties.deleted'));
	}

	public function getAssociate($slug)
	{
		if ( !$this->request->get('id') )
		{
			return $this->getAssociateUsers($slug);
		}

		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		$employee = $this->site->users()->find( $this->request->get('id') );

		if ( !$property || !$employee )
		{
			return [ 'error'=>1 ];
		}

		if ( !$property->users->contains( $employee->id ) )
		{
			$property->users()->attach( $employee->id );
		}

		return [ 
			'success' => 1,
			'html' => view('account.properties.form-employees', [ 'employees'=>$property->users()->withRole('employee')->get(), 'property_id'=>$property->id ])->render(),
		];
	}
	public function getAssociateUsers($slug)
	{
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			return [];
		}

		$response = [
			'success' => true,
			'items' => [],
		];

		$employees = $this->site->users()
									->whereNotIn('id', $property->users->lists('id'))
									->withRole('employee')->orderBy('name')->get();
		foreach ($employees as $employee)
		{
			$response['items'][] = [
				'value' => $employee->id,
				'label' => "{$employee->name} ({$employee->email})",
			];
		}

		return $response;
	}

	public function getChangeStatus($slug)
	{
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			return [ 'error'=>1 ];
		}

		$property->enabled = $property->enabled ? 0 : 1;
		$property->save();


		return [
			'success' => 1,
			'enabled' => $property->enabled,
		];

	}

	public function getChangeHighlight($slug)
	{
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			return [ 'error'=>1 ];
		}

		$property->highlighted = $property->highlighted ? 0 : 1;
		$property->save();


		return [
			'success' => 1,
			'highlighted' => $property->highlighted,
		];

	}

	/* HELPER FUNCTIONS --------------------------------------------------------------------------- */

	protected function getRequestFields($id=false) 
	{
		$fields = [
			'ref' => 'required',
			'type' => 'required|in:'.implode(',', array_keys(\App\Property::getTypeOptions())),
			'mode' => 'required|in:'.implode(',', \App\Property::getModes()),
			'price' => 'required|numeric|min:0',
			'currency' => 'required|in:'.implode(',', array_keys(\App\Property::getCurrencyOptions())),
			'size' => 'required|numeric|min:0',
			'size_unit' => 'required|in:'.implode(',', array_keys(\App\Property::getSizeUnitOptions())),
			'rooms' => 'required|integer|min:0',
			'baths' => 'required|integer|min:0',
			'services' => 'array',
			'enabled' => 'boolean',
			'highlighted' => 'boolean',
			'ec' => 'in:'.implode(',', array_keys(\App\Property::getEcOptions())),
			'ec_pending' => 'boolean',
			'newly_build' => 'boolean',
			'second_hand' => 'boolean',
			'new_item' => 'boolean',
			'opportunity' => 'boolean',
			'country_id' => 'required|exists:countries,id',
			'territory_id' => 'exists:territories,id',
			'state_id' => 'required|exists:states,id',
			'city_id' => 'required|exists:cities,id',
			'district' => '',
			'address' => '',
			'address_parts' => 'array',
			'show_address' => 'boolean',
			'zipcode' => '',
			'lat' => 'required|numeric',
			'lng' => 'required|numeric',
			'i18n' => 'required|array',
			'i18n.title' => 'required|array',
			'i18n.description' => 'required|array',
			'images' => 'array',
			'new_images' => 'array',
			'label_color' => 'required',
			'i18n.label' => 'required|array',
		];

		return $fields;
	}

	protected function validateRequest($id=false) 
	{
		// General
		$validator = \Validator::make($this->request->all(), $this->getRequestFields($id));
		if ($validator->fails()) 
		{
			return $validator;
		}

		// i18n fields
		$fields = [
			'title' => 'required|array',
			'description' => 'required|array',
			'label' => 'required|array',
		];
		$validator = \Validator::make($this->request->get('i18n'), $fields);
		if ($validator->fails()) 
		{
			return $validator;
		}

		$i18n = $this->request->get('i18n');

		// Title
		$fields = [
			fallback_lang() => 'required|string'
		];
		$validator = \Validator::make($i18n['title'], $fields);
		if ($validator->fails()) 
		{
			return $validator;
		}

		return true;
	}

	protected function saveRequest($property) 
	{

		// Main data
		foreach ($this->getRequestFields() as $field => $def)
		{
			$def = explode('|', $def);

			if ( in_array('array', $def) )
			{
				continue;
			}

			if ( in_array('boolean', $def) )
			{
				$property->$field = $this->request->get($field) ? 1 : 0;
			}
			elseif ( in_array($field, [ 'country_id','territory_id','state_id','city_id' ]) )
			{
				$property->$field = $this->request->get($field) ? $this->request->get($field) : null;
			}
			else
			{
				$property->$field = sanitize( $this->request->get($field) );
			}
		}

		// Translatable fields
		foreach (\App\Session\Site::get('locales_tabs') as $locale => $locale_name)
		{
			$property->translateOrNew($locale)->title = sanitize( $this->request->input("i18n.title.{$locale}") );
			$property->translateOrNew($locale)->description = sanitize( $this->request->input("i18n.description.{$locale}") );
			$property->translateOrNew($locale)->label = sanitize( $this->request->input("i18n.label.{$locale}") );
		}

		// Services
		foreach ($property->services as $service) 
		{
			$property->services()->detach($service->id);
		}

		if ( $this->request->get('services') )
		{  
			foreach ($this->request->get('services') as $service_id) 
			{
				$property->services()->attach($service_id);
			}
		}

		// Address parts
		$property->address_parts = $this->request->get('address_parts');

		$property->save();

		// Process images
		$position = 0;
		$preserve = [];

		// Update images position
		if ( $this->request->get('images') ) {
			foreach ($this->request->get('images') as $image_id) 
			{
				// New image
				if ( preg_match('#^new_(.*)$#', $image_id, $matches) )
				{
					// Image exists ?
					$filepath = public_path($matches[1]);
					if ( !file_exists($filepath) ) {
						continue;
					}

					// Prepare directory
					$dirpath = $property->image_path;
					if ( !is_dir($dirpath))
					{
						\File::makeDirectory($dirpath, 0777, true, true);
					}

					// Prepare filename
					$filename = $ofilename = basename($filepath);
					while ( file_exists("{$dirpath}/{$filename}") )
					{
						$filename = uniqid()."_{$ofilename}";
					}

					// Associate to property
					$new_image = $property->images()->create([
						'image' => $filename,
						'position' => $position,
					]);

					// Check ok
					if ( !$new_image )
					{
						continue;
					}

					// Move image to permanent location
					rename($filepath, "{$dirpath}/{$filename}");

					// Preserve
					$preserve[] = $new_image->id;
				}
				// Old image
				else
				{
					// Update position
					$property->images()->find($image_id)->update([ 
						'default' => 0, 
						'position' => $position 
					]);
					// Preserve
					$preserve[] = $image_id;

				}

				// Increase position
				$position++;
			}
		}

		// Deleted images
		foreach ($property->images as $image) 
		{
			if ( !in_array($image->id, $preserve) )
			{
				@unlink( public_path("sites/{$property->site_id}/properties/{$property->id}/{$image->image}") );
				$image->delete();
			}
		}

		// New images
		if ( $this->request->file('new_images') )
		{
			foreach ($this->request->file('new_images') as $key => $tmp)
			{
				$img_key = "new_images.{$key}";

				// Validate image
				$validator = \Validator::make($this->request->all(), [
					$img_key => 'required|image|max:' . \Config::get('app.property_image_maxsize', 2048),
				]);
				if ($validator->fails()) 
				{
					continue;
				}

				$img_folder = $property->image_path;

				$img_name = $this->request->file($img_key)->getClientOriginalName();
				while ( file_exists("{$img_folder}/{$img_name}") )
				{
					$img_name = uniqid() . '_' . $this->request->file($img_key)->getClientOriginalName();
				}
				$this->request->file($img_key)->move($img_folder, $img_name);

				$property->images()->create([
					'image' => $img_name,
					'position' => $position,
				]);

				$position++;
			}
		}
		// Default image
		$default_image = $property->images()->orderBy('position')->first();
		if ( $default_image )
		{
			$default_image->update([ 'default'=>1 ]);
		}

		return true;
	}

	public function postUpload()
	{

		$file = \Input::file('file');

		$validator = \Validator::make($this->request->all(), [
			'file' => 'required|image|max:' . \Config::get('app.property_image_maxsize', 2048),
		]);
		$validator->setAttributeNames([
			'file' => ucfirst( trans('account/properties.images.dropzone.nicename') ),
		]);

		if ($validator->fails()) 
		{
			$errors = $validator->errors();
			return response()->json([
				'error' => true,
				'message' => $errors->first('file'),
			], 400);
		}

		$dir = 'sites/uploads/'.date('Ymd');
		$dirpath = public_path($dir);

		// If the uploads fail due to file system, you can try doing public_path().'/uploads' 
		$filename = $ofilename = preg_replace('#[^a-z0-9\.]#', '', strtolower($file->getClientOriginalName()));
		while ( file_exists("{$dirpath}/{$filename}") )
		{
			$filename = uniqid()."_{$ofilename}";
		}

		$upload_success = $file->move($dirpath, $filename);

		if( $upload_success ) 
		{
			return response()->json([
				'success' => true,
				'directory' => $dir,
				'filename' => $filename,
			], 200);
		}

		return response()->json([
			'error' => true,
		], 400);
	}

}
