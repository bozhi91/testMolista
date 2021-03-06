<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Property\Videos;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Jobs\modifyImages;


class PropertiesController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		$this->middleware([ 'permission:property-view' ], [ 'only' => [ 'index','show','postCatch' ] ]);
		$this->middleware([ 'permission:property-create', 'property.permission:create' ], [ 'only' => [ 'create','store' ] ]);
		$this->middleware([ 'permission:property-edit' ], [ 'only' => [ 'edit','update','getAssociate','postAssociate' ] ]);
		$this->middleware([ 'property.permission:edit' ], [ 'only' => [ 'update','getAssociate','postAssociate','getChangeHomeSlider','getChangeHighlight','getChangeStatus' ] ]);
		$this->middleware([ 'permission:property-delete', 'property.permission:delete' ], [ 'only' => [ 'destroy' ] ]);

		\View::share('submenu_section', 'properties');
	}

	public $updated = 0;
    public $property;

    public static function modifyImagesFreePlan(){

        $site = \App\Site::enabled()
            ->with('locales')
            ->with('infocurrency')
            ->current()->first();

        $properties = DB::table('properties')
            ->select('id')
            ->where('site_id',$site->id)
            ->get();

        if($site->plan_id==1){
            $watermark_image = public_path().'/sites/watermarks/molista.png';
            //get the images for each property
            foreach($properties as $property){
                $prop = DB::table('properties_images')
                    ->select('*')
                    ->where('property_id',$property->id)
                    ->get();

                $path           = public_path()."/sites/".$site->id."/properties/".$property->id;
                $path_watermark = public_path()."/sites/".$site->id."/properties/".$property->id."/watermark";

                //create a backupfolder for the images with watermark: /public/sites/site_id/watermark
                if(!is_dir($path_watermark)){
                    File::makeDirectory($path_watermark, 0700, true);
                }


                //get the images of the current property
                foreach($prop as $image){
                    $pos = strpos($image->image, "watermark");
                    $image = $image->image;
                    if($pos!=null){
                        $image = substr(strrchr($image, "/"), 1);
                    }

                    //copy the image to the watermark folder
                    copy($path."/".$image,$path_watermark."/".$image);

                    DB::table('properties_images')
                        ->where('property_id', $property->id)
                        ->where('image',$image)
                        ->update(['image' => "/watermark/".$image]);

                    // If the watermark is set to present, use the watermark image in the frontend(we're updating the Database)
                    $logo = Image::make($watermark_image);
                    $size = $logo->width()/$logo->height();

                    $logo_new_width  = $logo->width()+($size*100);
                    $logo_new_height = $logo_new_width/$size;

                    $logo->resize($logo_new_width,$logo_new_height);
                    $logo->opacity(75);

                    $img = Image::make($path."/".$image)
                        ->insert($logo,"center",50,50)
                        ->save($path."/watermark/".$image);
                }
            }
        }
        die;
    }

    public static function viewPropertyInWeb($id){
        $flatUrl = DB::table('properties_translations')
            ->select('slug')
            ->where('property_id',$id)
            ->where('locale','es')
            ->whereRaw('slug is not null')
            ->first();

        $subdomain = DB::table('sites')
            ->select('subdomain')
            ->where('id',session("SiteSetup")['site_id'])
            ->first();

        return env('APP_PROTOCOL')."://".$subdomain->subdomain.".".env('APP_DOMAIN')."/property/". $flatUrl->slug."/".$id;
    }

    public function publishProperty(){
        if(!empty($_POST['marketplace'])){
            foreach($_POST['marketplace'] as $marketplace){

                $getMarketplace = DB::table("sites_marketplaces")
                    ->select("*")
                    ->where('site_id',session("SiteSetup")['site_id'])
                    ->where('marketplace_id',$marketplace)
                    ->get();

                if($getMarketplace!=null){
                    // echo json_encode($getMarketplace);
                }
                else{/*
                DB::table('sites_marketplaces')
                    ->where('site_id', session("SiteSetup")['site_id'])
                    ->update(['blocked_site' => 0]);*/

                    DB::table('sites_marketplaces')->insert(
                        [
                            'site_id' => session("SiteSetup")['site_id'],
                            'marketplace_id' => $marketplace,
                            'marketplace_enabled' => 1,
                            'marketplace_export_all' => 1,
                        ]
                    );
                }
            }
        }

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $query = $this->site->properties()
            ->with('customers')
            ->with('state')
            ->with('city')
            ->leftJoin('cities','properties.city_id','=','cities.id')
            ->addSelect('cities.name AS city_name')
            ->leftJoin('properties_users', function($join){
                $join->on('properties.id', '=', 'properties_users.property_id');
                $join->on('properties_users.user_id', '=', \DB::raw($this->site_user->id) );
            })
            ->addSelect( \DB::raw('IF(properties_users.`property_id` IS NULL, 0, 1) AS is_manager') );

        $properties = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

        $this->set_go_back_link();
        $total_properties = $query->get()->count();
        return view('account.properties.index', compact('properties','clean_filters', 'total_properties'));
    }


    public static function getRecentProperties(){
        $recentProps = DB::table("properties")
            ->select("*")
            ->where('site_id',session("SiteSetup")['site_id'])
            ->where('enabled',1)
            ->orderByRaw('created_at DESC')
            ->get();


        $plans = DB::table('sites')
            ->join('plans', 'sites.plan_id', '=', 'plans.id')
            ->select('plans.max_properties','plan_id')
            ->where('sites.id',session("SiteSetup")['site_id'])
            ->first();


        //check if the site is blocked
        $isBlocked = DB::table('sites')
            ->select('blocked_site')
            ->where('id',session("SiteSetup")['site_id'])
            ->first();

        $max_properties=0;
        if($plans->max_properties==null || $plans->max_properties==0){
            $max_properties = 10000;
        }
        //Here we generate a list of the properties we must disable
        $propRef = "<br/>";
        $properties = array();

        //Disable the property if the customer has more than the allowed number of properties
        if( count($recentProps)>$max_properties){
            DB::table('sites')
                ->where('id', session("SiteSetup")['site_id'])
                ->update(['blocked_site' => 0]);
        }
       /* if( (count($recentProps)>$max_properties) && $plans->plan_id==1 && $isBlocked->blocked_site==0){
            for($i=$plans->max_properties;$i<count($recentProps);$i++){
                array_push($properties,$recentProps[$i]->ref);
                $propRef.= " - Reference: ".$recentProps[$i]->ref."<br/>";

                DB::table('properties')
                    ->where('id',$recentProps[$i]->id)
                    ->update(['enabled' => 0]);
            }

            //Mark the current site as blocked/limited. This means that the properties are disabled. The site is still running.
            DB::table('sites')
                ->where('id', session("SiteSetup")['site_id'])
                ->update(['blocked_site' => 1]);
        }
        else{
            //enable back the site
            DB::table('sites')
                ->where('id', session("SiteSetup")['site_id'])
                ->update(['blocked_site' => 0]);
        }*/
        return $propRef;
    }

    public function getSite(){
        return $this->site;
    }

    public static function getMarketplaces($property,$site){

        $properties = DB::select("
              select sm.site_id, sm.marketplace_id, m.logo, m.name, s.subdomain
              from sites_marketplaces sm, properties p, marketplaces m,sites s
              where m.id = sm.marketplace_id
              and p.site_id = sm.site_id
              and s.id = p.site_id
              and p.id = '".$property->id."'
              and sm.marketplace_enabled = 1");

            return  $properties;
    }


	public function index()
	{
        Session::put('property_stored', 'not_stored');
		$clean_filters = false;
		$query = $this->site->properties()
							->with('customers')
							->with('state')
							->with('city')
							->leftJoin('cities','properties.city_id','=','cities.id')
							->addSelect('cities.name AS city_name')
							->leftJoin('properties_users', function($join){
	                             $join->on('properties.id', '=', 'properties_users.property_id');
	                             $join->on('properties_users.user_id', '=', \DB::raw($this->site_user->id) );
							})
							->addSelect( \DB::raw('IF(properties_users.`property_id` IS NULL, 0, 1) AS is_manager') );

		if ( !$this->site_user->pivot->can_view_all )
		{
			$query->whereIn('properties.id', $this->auth->user()->properties()->lists('id'));
		}

		// Filter by reference
		if ( $this->request->input('ref') )
		{
			$clean_filters = true;
			$query->where('properties.ref', 'LIKE', "%{$this->request->input('ref')}%");
		}

		// Filter by title
		if ( $this->request->input('title') )
		{
			$clean_filters = true;
			$query->whereTranslationLike('title', "%{$this->request->input('title')}%");
		}

		// Filter by address
		if ( $this->request->input('address') )
		{
			$clean_filters = true;
			$query->leftJoin('states','properties.state_id','=','states.id');
			$query->where(function ($query) {
				$query->where('properties.address', 'LIKE', "%{$this->request->input('address')}%");
				$query->orWhere('states.name', 'LIKE', "%{$this->request->input('address')}%");
				$query->orWhere('cities.name', 'LIKE', "%{$this->request->input('address')}%");
			});
		}

		// Filter by mode
		if ( $this->request->input('mode') )
		{
			$clean_filters = true;
			$query->where('properties.mode', $this->request->input('mode'));
		}

		// Filter by highlighted
		if ( $this->request->input('highlighted') )
		{
			$clean_filters = true;
			$query->where('properties.highlighted', intval($this->request->input('highlighted'))-1);
		}

		// Filter by highlighted
		if ( $this->request->input('enabled') )
		{
			$clean_filters = true;
			$query->where('properties.enabled', intval($this->request->input('enabled'))-1);
		}

		//Filter by mode
		if($this->request->input('mode')) {
			$query->where('properties.mode', $this->request->input('mode'));
		}

		//Filter by price
		if($this->request->input('price')) {
			$query->where('properties.price'
					, $this->request->input('operation', '=')
					, $this->request->input('price'));
		}

		switch ( $this->request->input('order') )
		{
			case 'asc':
				$order = 'asc';
				break;
			default:
				$order = 'desc';
		}

		switch ( $this->request->input('orderby') )
		{
			case 'reference':
				$query->orderBy('ref', $order);
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
				$query->orderBy('title', $order);
				break;
			case 'price':
				$query->orderBy('price', $order);
				break;
			case 'creation':
			default:
				$query->orderBy('created_at', $order);
				break;
		}

		if ( $this->request->input('csv') )
		{
			return $this->exportCsv($query);
		}

		$total_properties = $query->get()->count();
		$properties = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );
		$this->set_go_back_link();


        ////////////////////////////////////////////////////////////////////////////////////////////////
        $marketplaces = $this->site->marketplaces()
            ->wherePivot('marketplace_enabled','=',1)
            ->withSiteProperties($this->site->id)
            ->enabled()->orderBy('name')->get();

        $myprop = array();
        foreach($properties as $property){
            $market = array();
            foreach($marketplaces as $marketplace){
                $publishable = $this->site->marketplace_helper->checkReadyProperty($marketplace,$property);
                if ( $publishable === true ){
                    array_push($market,array("market_id"=>$marketplace->id,"enabled"=>"1"));
                }
                else{
                    array_push($market,array("market_id"=>$marketplace->id,"enabled"=>"0"));
                }
            }
            array_push($myprop,array("property"=>$property->id,"market"=>$market));
        }
		return view('account.properties.index', compact('properties','clean_filters', 'total_properties','marketplaces','myprop'));
	}

	public function exportCsv($query)
	{
		$columns = [
			'ref' => trans('account/properties.ref'),
			'title' => trans('account/properties.column.title'),
			'creation' => trans('account/properties.column.created'),
			'location' => trans('account/properties.column.location'),
			'lead' => trans('account/properties.tab.lead'),
			'highlighted' => trans('account/properties.highlighted'),
			'enabled' => trans('account/properties.enabled'),
		];

		$csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
		$csv->setDelimiter(';');

		// Headers
		$csv->insertOne( array_values($columns) );

		// Lines
		foreach ($query->limit(9999)->get() as $property)
		{
			$data = [];

			foreach ($columns as $key => $value)
			{
				switch ($key)
				{
					case 'creation':
						$data[] = $property->created_at->format('d/m/Y');
						break;
					case 'location':
						$data[] = "{$property->city->name} / {$property->state->name}";
						break;
					case 'lead':
						$data[] = number_format($property->customers->count(), 0, ',', '.');
						break;
					case 'highlighted':
					case 'enabled':
						$data[] = $property->$key ? trans('general.yes') : trans('general.no');
						break;
					default:
						$data[] = $property->$key;
				}
			}

			$csv->insertOne( $data );
		}

		$csv->output('properties_'.date('Ymd').'.csv');
		exit;
	}

	public function create($slug = null)
	{
		$modes = \App\Property::getModeOptions();
		$types = \App\Property::getTypeOptions();
		$energy_types = \App\Property::getEcOptions();
		$services = \App\Models\Property\Service::withTranslations()->enabled()->orderBy('title')->get();

		$countries = $this->site->enabled_countries;
		if ( $country_id = old('country_id', $this->site->country_id) )
		{
			$states = \App\Models\Geography\State::enabled()->where('country_id', $country_id)->lists('name','id');
		}
		if ( old('state_id') )
		{
			$cities = \App\Models\Geography\City::enabled()->where('state_id', old('state_id'))->lists('name','id');
		}

		$managers = $this->site->users()->orderBy('name')->lists('name','id')->all();
		$districts = $this->site->districts()->orderBy('name')->lists('name','id');

		$current_tab = session('current_tab', 'general');

		if($slug) {
			$property = $this->site->properties()->whereTranslation('slug', $slug)
					->with([ 'catches' => function($query){
							$query->with('buyer');
					}])->first();

			$property->ref = '';
			$marketplaces = $this->site->marketplaces()
							->wherePivot('marketplace_enabled','=',1)
							->withSiteProperties($this->site->id)
							->enabled()->orderBy('name')->get();


			$states = \App\Models\Geography\State::enabled()->where('country_id', $property->country_id)->lists('name','id');
			$cities = \App\Models\Geography\City::enabled()->where('state_id', $property->state_id)->lists('name','id');
			$districts = $this->site->districts()->orderBy('name')->lists('name','id');
		}

		return view('account.properties.create', compact('modes','types','energy_types',
				'services','countries','states','cities','country_id','managers','current_tab', 'districts', 'property', 'marketplaces'));
	}

	public function customizePropertyImage($property){

        $watermark_present  = true;

        //if the plan is not free
        if($this->site->plan_id!=1){
            if(!empty($_POST['include_watermark'])){
                $watermark_present  = true;
            }
            else{
                $watermark_present = false;
            }
        }
        if($this->site->plan_id==1){
            if(empty($_POST['include_watermark'])){
                $watermark_present  = true;
            }
        }

        $siteId = $this->site->id;
        $propId = $property->id;

        //set the default properties for the watermark
        $watermark_pos_array = array("top-left","top-right","bottom-left","bottom-right","center");
        $watermark_pos       = 4;
        $watermark_rotate    = false;
        $watermark_image     = public_path().'/sites/watermarks/molista.png';
        $watermark_opacity   = 75;

        if(!empty( $_POST['image_orientation'])){
            $watermark_pos = $_POST['image_orientation'];
        }
        if(!empty( $_POST['rotated'])){
            $watermark_rotate = $_POST['rotated'];
        }
        if(!empty($_FILES['watermark_image'])){
            if(strlen($_FILES['watermark_image']['name'])>0){
                $watermark_image = public_path()."/sites/".$siteId."/properties/".$propId."/watermark/".$_FILES['watermark_image']['name'];
                copy($_FILES['watermark_image']['tmp_name'],$watermark_image);
            }
        }
        if(!empty($_POST['include_watermark'])){
            $watermark_present = $_POST['include_watermark'];
        }
        if($watermark_pos!=4){
            $watermark_rotate = false;
        }


        $path           = public_path()."/sites/".$siteId."/properties/".$propId;
        $path_watermark = public_path()."/sites/".$siteId."/properties/".$propId."/watermark";

        //create a backupfolder for the images with watermark: /public/sites/site_id/watermark
        if(!is_dir($path_watermark)){
            File::makeDirectory($path_watermark, 0700, true);
        }

        foreach($_POST['images'] as $image){
            $image = substr(strrchr($image, "/"), 1);

            if(empty($image))continue;

            //copy the image to the watermark folder
            copy($path."/".$image,$path_watermark."/".$image);

            // If the watermark is set to present, use the watermark image in the frontend(we're updating the Database)
            if($watermark_present){
                DB::table('properties_images')
                    ->where('property_id', $propId)
                    ->where('image',$image)
                    ->update(['image' => "/watermark/".$image]);
            }
            else{
                DB::table('properties_images')
                    ->where('property_id', $propId)
                    ->where('image',"/watermark/".$image)
                    ->update(['image' => $image]);
            }

            $logo = Image::make($watermark_image);
            $size = $logo->width()/$logo->height();

            $logo_new_width  = $logo->width()+($size*100);
            $logo_new_height = $logo_new_width/$size;

            $logo->resize($logo_new_width,$logo_new_height);
            $logo->opacity($watermark_opacity);
            if($watermark_rotate){
                $logo->rotate(-45);
            }

          $img = Image::make($path."/".$image)
              ->insert($logo,$watermark_pos_array[$watermark_pos],50,50)
              ->save($path."/watermark/".$image);
        }
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
			'commission_fixed' => 'numeric|min:0',
			'commission' => 'numeric|between:0,100',
		];
		$validator = \Validator::make($this->request->all(), $catch_fields);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($valid);
		}

		// Create element
		$property = $this->site->properties()->create([
			'enabled' => 0,
			'publisher_id' => $this->request->input('employee_id'),
			'published_at' => date('Y-m-d'),
		]);
		$this->property = $property;

		if ( empty($property->id) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Save
		if ( !$this->saveRequest($property,true) )
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
			'employee_id' => $this->request->input('employee_id'),
			'catch_date' => $property->created_at,
			'price_original' => $this->request->input('price'),
			'status' => 'active',
		]);

		$data = [];
		foreach ($catch_fields as $field => $def)
		{
			$data[$field] = $this->request->input($field);
		}
		$catch->update($data);

		//New video
		if($this->request->input('video_link')) {
			$link = $this->request->input('video_link');
			$saved = $this->saveVideoLink($property, $link);
			if(!$saved) {
				return redirect()->back()->withInput()
					->with('error', trans('account/properties.video.error'));
			}
		}

		//Duplicate videos
		$duplicateVideoIds = $this->request->input('duplicate_videos', []);
		foreach($duplicateVideoIds as $duplicateVideoId){
			$video = Videos::where('id', $duplicateVideoId)->first();
			if(!$video) {
				continue;
			}
			$this->saveVideoLink($property, $video->link);
		}

		$property = $this->site->properties()->find($property->id);

        if(!empty($_POST['desde'])) {
            DB::table('properties')
                ->where('id', $property['id'])
                ->update(['desde' => 1]);

        }
        //store the HTML text in the database
        DB::table('properties')
            ->where('id', $property['id'])
            ->where('ref', $property['ref'])
            ->update(['html_property' =>  $_POST['body']]);

        Session::put('property_stored', 'stored');
        //////////////////////////////////////////////////////////////
            //if the plan is Free, the watermark is mandatory
            $this->customizePropertyImage($property);
        //////////////////////////////////////////////////////////////
        return redirect()->action('Account\PropertiesController@edit', $property->slug)->with('current_tab', $this->request->input('current_tab'))->with('success', trans('account/properties.created'));
	}

	public function download($slug,$locale) {
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

	public function edit($slug)
	{
        if(!empty($_POST['uploadWatermark'])){
            echo $_POST['uploadWatermark'];
        }

		$query = $this->site->properties()
						->whereTranslation('slug', $slug)
						->withEverything();

		if (!$this->auth->user()->canProperty('edit_all')) {
			if ($this->auth->user()->canProperty('edit')) {
				$query = $query->whereIn('properties.id', $this->auth->user()->properties()->lists('id'));
			} else {
				$query = $query->where('properties.id', 0);
			}
		}

		$property = $query->first();
		if ( !$property )
		{
			abort(404);
		}

		$modes = \App\Property::getModeOptions();
		$types = \App\Property::getTypeOptions();
		$energy_types = \App\Property::getEcOptions();
		$services = \App\Models\Property\Service::withTranslations()->enabled()->orderBy('title')->get();

		$countries = $this->site->enabled_countries;
		$states = \App\Models\Geography\State::enabled()->where('country_id', $property->country_id)->lists('name','id');
		$cities = \App\Models\Geography\City::enabled()->where('state_id', $property->state_id)->lists('name','id');
		$districts = $this->site->districts()->orderBy('name')->lists('name','id');

		$marketplaces = $this->site->marketplaces()
							->wherePivot('marketplace_enabled','=',1)
							->withSiteProperties($this->site->id)
							->enabled()->orderBy('name')->get();

		$current_tab = session('current_tab', 'general');

		$updated = $this->updated;
		return view('account.properties.edit', compact('property','modes','types','energy_types','services','countries','states','cities','marketplaces','current_tab', 'districts','updated'));
	}

	public static function getCheckboxDesdeState($propertyId){
        $status = DB::table('properties')
            ->select('desde')
            ->where('id',$propertyId)
            ->first();

        if($status!=null){
            return $status->desde;
        }
        return 0;
    }

	public function update(Request $request, $slug)
	{
		// Get property
        $query = $this->site->properties()
            ->whereTranslation('slug', $slug)
            ->withEverything();

        DB::table('properties')
            ->where('id', $_POST['propertyId'])
            ->where('ref', $_POST['ref'])
            ->update(['html_property' =>  $_POST['body']]);

		//here we update the status of the checkbox 'desde'.

		if(!empty($_POST['desde'])){
            DB::table('properties')
                ->where('id',$_POST['propertyId'])
                ->update(array('desde' => 1));
        }
        else {
            DB::table('properties')
                ->where('id',$_POST['propertyId'])
                ->update(array('desde' => 0));
        }

		if (!$this->auth->user()->canProperty('edit_all')) {
			if ($this->auth->user()->canProperty('edit')) {
				$query = $query->whereIn('properties.id', $this->auth->user()->properties()->lists('id'));
			} else {
				$query = $query->where('properties.id', 0);
			}
		}

		$property = $query->first();
		if ( !$property )
		{
			abort(404);
		}

		$oldPrice = floatval($property->price);
		$newPrice = floatval($this->request->input('price'));
		$isPriceFall = $newPrice < $oldPrice;

		// Validate request
		$valid = $this->validateRequest($property->id);
		if ( $valid !== true )
		{
			return redirect()->back()->withInput()->withErrors($valid);
		}

		// Save
		if ( !$this->saveRequest($property,false) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		//New video
		if($this->request->input('video_link')) {
			$link = $this->request->input('video_link');
			$saved = $this->saveVideoLink($property, $link);
			if(!$saved) {
				return redirect()->back()->withInput()
					->with('error', trans('account/properties.video.error'));
			}
		}

		if($isPriceFall){
			if($this->site->alert_config === null ||
					$this->site->alert_config['bajada']['agentes']){
				$agents = $property->users()->withRole('employee')->get();
				foreach($agents as $agent){
					$job = (new \App\Jobs\SendNotificationPriceFall($property, $agent))->onQueue('emails');
					$this->dispatch($job);
				}
			}

			if($this->site->alert_config === null ||
					$this->site->alert_config['bajada']['customers']){
				foreach($property->customers as $customer){

					if($customer->alert_config === null ||
							$customer->alert_config['bajada']){
						$job = (new \App\Jobs\SendNotificationPriceFall($property, null, $customer))->onQueue('emails');
						$this->dispatch($job);
					}
				}
			}
		}

		// Save marketplaces
		$this->site->marketplace_helper->savePropertyMarketplaces($property->id, $this->request->input('marketplaces_ids'));

		// Get property, with slug
		$property = $this->site->properties()->find($property->id);

        //////////////////////////////////////////////////////////////
            //if the plan is Free, the watermark is mandatory
            $this->customizePropertyImage($property);
        //////////////////////////////////////////////////////////////

		return redirect()->action('Account\PropertiesController@edit', $property->slug)
            ->with('current_tab', $this->request->input('current_tab'))
            ->with('success', trans('account/properties.saved'))
            ->with('updated', true);
	}

	/**
	 * @param Property $property
	 * @param string $link
	 * @return boolean|null
	 */
	private function saveVideoLink($property, $link){
		$isVimeo = Videos::isVideoVimeo($link);
		$isYoutube = Videos::isVideoYoutube($link);

		if(!$isVimeo && !$isYoutube){
			return false;
		}

		$remoteThumbnail = $isVimeo ? Videos::getVimeoThumbnail($link) :
			Videos::getYoutubeThumbnail($link);

		if($remoteThumbnail) {
			$imageName = $isVimeo ? Videos::getVimeoCode($link) :
				Videos::getYouTubeCode($link);

			$localName = $property->video_path . '/' . $imageName . '.jpg';
			$thumbUploaded = copy($remoteThumbnail, $localName);

			if($thumbUploaded) {
				$video = new Videos();
				$video->property_id = $property->id;
				$video->link = $link;
				$video->thumbnail = $imageName . '.jpg';
				return $video->save();
			}
		}
	}

	public function show($slug)
	{
        // Get property
		$property = $this->site->properties()
						->withTrashed()
						->whereTranslation('slug', $slug)
						->with([ 'translations' => function($query){
							$query->with('logs');
						}])
						->with('logs')
						->with([ 'catches' => function($query){
							$query->with('buyer');
						}])
						->with('customers')
						->with('documents')
						->first();
		if ( !$property )
		{
			abort(404);
		}

		$current_tab = session('current_tab', old('current_tab', 'tab-general'));

		return view('account.properties.show', compact('property', 'current_tab'));
	}

	public function getLeads($slug)
	{
		// Get property
		$property = $this->site->properties()
						->withTrashed()
						->whereTranslation('slug', $slug)
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
			'cif' => '',
			'company_name' => '',
			'price_min' => 'numeric|min:1',
			'commission_fixed' => 'numeric|min:0',
			'commission' => 'numeric|between:0,100',
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
			$data[$field] = $this->request->input($field);
		}

		$item->update($data);

		return [ 'success'=>true ];
	}

	public function getCatchClose($id, $client_id = null)
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

		return view('account.properties.catch-close', compact('item','managers','customers', 'client_id'));
	}
	public function postCatchClose($id)
	{
		$item = \App\Models\Property\Catches::ofSite($this->site->id)->findOrFail($id);

		$fields = [
			'transaction_date' => 'required:date',
			'status' => 'required|in:sold,rent,transfer,other',
			'closer_id' => 'exists:users,id',
		];
		switch ( $this->request->input('status') )
		{
			case 'sold':
			case 'rent':
			case 'transfer':
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
			$data[$field] = $this->request->input($field);
		}

		$item->update($data);

		// Add KPIs
		switch ( $this->request->input('status') )
		{
			case 'sold':
			case 'rent':
			case 'transfer':
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

		// Disable property
		$item->property->update([
			'enabled' => 0,
		]);

		//Send notifications on property sell
		if(in_array($this->request->input('status'), ['sold'])){
			foreach($item->property->customers() as $customer){
				if($customer->alert_config === null ||
						$customer->alert_config['venta']){
					$job = (new \App\Jobs\SendNotificationOnPropertySale($customer, $item->property))->onQueue('emails');
					$this->dispatch($job);
				}
			}
		}

		return [ 'success'=>true ];
	}

	public function destroy($slug)
	{
		$query = $this->site->properties()
						->whereTranslation('slug', $slug)
						->withEverything();

		if (!$this->auth->user()->canProperty('edit_all')) {
			if ($this->auth->user()->canProperty('edit')) {
				$query = $query->whereIn('properties.id', $this->auth->user()->properties()->lists('id'));
			} else {
				$query = $query->where('properties.id', 0);
			}
		}

		$property = $query->first();
		if ( !$property )
		{
			abort(404);
		}

		// Remove image folder
		//\File::deleteDirectory($property->image_path);

		// Delete property
		$property->delete();

		return redirect()->action('Account\PropertiesController@index')->with('success', trans('account/properties.deleted'));
	}

	public function getAssociate($slug)
	{
		if ( !$this->request->input('id') )
		{
			return $this->getAssociateUsers($slug);
		}

		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		$employee = $this->site->users()->find( $this->request->input('id') );

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

		// If change to enabled
		if ( $property->enabled )
		{
			// Get site plan max_properties limitation
			$max_properties = @intval($property->site->plan->max_properties);

			// If limitation has been reached
			if ( $max_properties && $this->site->properties()->enabled()->count() >= $max_properties )
			{
				return [
					'error' => 1,
					'error_message' => strip_tags(str_replace("\n", ' ', trans('account/warning.export.limit', [ 'max_properties' => $max_properties ]))),
				];
			}
		}

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

	public function getChangeHomeSlider($slug)
	{
		$property = $this->site->properties()->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			return [ 'error'=>1 ];
		}

		$property->home_slider = $property->home_slider ? 0 : 1;
		$property->save();


		return [
			'success' => 1,
			'home_slider' => $property->home_slider,
		];
	}

	/* HELPER FUNCTIONS --------------------------------------------------------------------------- */

	protected function getRequestFields($id=false)
	{
		$fields = [
			'ref' => 'required|unique:properties,ref,'.$id.',id,site_id,'.$this->site->id.',deleted_at,NULL',
			'type' => 'required|in:'.implode(',', array_keys(\App\Property::getTypeOptions())),
			'mode' => 'required|in:'.implode(',', \App\Property::getModes()),
			'price' => 'required|numeric|min:0',
			'price_before' => 'numeric|min:0',
			'discount_show' => 'boolean',
			'currency' => 'required|exists:currencies,code',
			'size' => 'required|numeric|min:0',
			'size_unit' => 'required|in:'.implode(',', array_keys(\App\Property::getSizeUnitOptions())),
			'rooms' => 'required|integer|min:0',
			'baths' => 'required|integer|min:0',
			'services' => 'array',
			'enabled' => 'boolean',
			'home_slider' => 'boolean',
			'highlighted' => 'boolean',
			'ec' => 'in:'.implode(',', array_keys(\App\Property::getEcOptions())),
			'ec_pending' => 'boolean',
			'newly_build' => 'boolean',
			'second_hand' => 'boolean',
			'new_item' => 'boolean',
			'opportunity' => 'boolean',
			'private_owned' => 'boolean',
			'bank_owned' => 'boolean',
			'country_id' => 'required|exists:countries,id',
			'territory_id' => 'exists:territories,id',
			'state_id' => 'required|exists:states,id',
			'city_id' => 'required|exists:cities,id',
			'district_id' => '',
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
			'construction_year' => 'integer|min:0',
			'export_to_all' => 'boolean',
			'details' => '',
			'marketplace_attributes' => '',
			'url_3d' => ''
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
		$validator = \Validator::make($this->request->input('i18n'), $fields);
		if ($validator->fails())
		{
			return $validator;
		}

		$i18n = $this->request->input('i18n');

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

	protected function saveRequest($property,$is_new=false)
	{
		if ( $this->request->input('enabled') )
		{
			$fix_enable = false;
			if (
				$this->site->property_limit_remaining < 0
				||
				( !$property->enabled && $this->site->property_limit_remaining < 1 )
			)
			{
				$this->request->merge([
					'enabled' => 0,
				]);
			}
		}

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
				$property->$field = $this->request->input($field) ? 1 : 0;
			}
			elseif ( in_array($field, [ 'country_id','territory_id','state_id','city_id','construction_year','details', 'marketplace_attributes' ]) )
			{
				$property->$field = $this->request->input($field) ? $this->request->input($field) : null;
			}
			else
			{
				$property->$field = sanitize( $this->request->input($field) );
			}
		}

		//District data
		if($this->request->input('new_district') == 'true'){
			if(!$this->request->input('district')) {
				$property->district_id = null;
			} else {
				$district = $this->site->districts()
						->where('name', $this->request->input('district'))->first();

				if(!$district) {
					$district = $this->site->districts()->create([
						'name' => $this->request->input('district'),
					]);
				}

				$property->district_id = $district->id;
			}
		} else {
			$property->district_id = $this->request->input('district_id') ?
					$this->request->input('district_id') : null;
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

		if ( $this->request->input('services') )
		{
			foreach ($this->request->input('services') as $service_id)
			{
				$property->services()->attach($service_id);
			}
		}

		// Address parts
		$property->address_parts = $this->request->input('address_parts');

		$property->save();

		// Process images
		$position = 0;
		$preserve = [];

		$rotation = $this->request->input('rotation');

		// Update images position
		if ( $this->request->input('images') ) {
			foreach ($this->request->input('images') as $image_id)
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
						'created_at' => new \DateTime(),
						'updated_at' => new \DateTime(),
					]);

					// Check ok
					if ( !$new_image )
					{
						continue;
					}

					$newlocation = "{$dirpath}/{$filename}";

					// Move image to permanent location
					rename($filepath, $newlocation);

					//rotate image if necessary
					if(!empty($rotation[$image_id])){
						$degree = -(int)$rotation[$image_id];
						\Image::make($newlocation)->rotate($degree)->save($newlocation);
					}

					// Preserve
					$preserve[] = $new_image->id;
				}
				// Old image
				else
				{
					$image = $property->images()->find($image_id);

					if(!$image && $is_new) {//duplicate image
						$imageToCopy = \App\Models\Property\Images::find($image_id);
						$parentImagePath = $imageToCopy->property->image_path . '/' . $imageToCopy->image;

						$newImageName = $property->id."_{$imageToCopy->image}";
						$newImagepPath = $property->image_path . '/' . $newImageName;

						if(file_exists($parentImagePath)) {
							copy($parentImagePath, $newImagepPath);
						}

						//save image object even if copy failed. or file do not exist.
						$image = $property->images()->create([
							'image' => $newImageName,
							'position' => $position,
							'created_at' => new \DateTime(),
							'updated_at' => new \DateTime(),
						]);
					}

					$updateFields = [
						'default' => 0,
						'position' => $position,
					];

					//rotate image if necessary
					if(!empty($rotation[$image_id])){
						$degree = -(int)$rotation[$image_id];
						$path = public_path("sites/{$property->site_id}/properties/{$property->id}/{$image->image}");
						\Image::make($path)->rotate($degree)->save($path);

						//delete thumbnail
						$thumbPath = public_path("sites/{$property->site_id}/properties/{$property->id}/thumbnail/{$image->image}");
						\File::delete($thumbPath);

						$updateFields['updated_at'] = new \DateTime();
					}

					// Update position
					$image->update($updateFields);

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
			if ( !in_array($image->id, $preserve) && !$is_new)
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

		$videoIds = $this->request->input('videos', []);

		foreach($property->videos as $video) {
			if(!in_array($video->id, $videoIds)){
				$videoThumbPath = $property->video_path . '/' . $video->thumbnail;
				@unlink( $videoThumbPath );
				$video->delete();
			}
		}

		return true;
	}

	public function postComment($slug)
	{
		$property = $this->site->properties()->whereTranslation('slug', $slug)->first();
		if ( !$property )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$property->update([
			'comment' => $this->request->input('comment')
		]);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}

	public function postNota($slug)
	{
		$property = $this->site->properties()->whereTranslation('slug', $slug)->first();

		if ( !$property )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$property->update([
			'nota' => $this->request->input('nota')
		]);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
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
			// Resize targets
			$target_width = 1920;
			$target_height = 1080;

			// Create thumb
			$thumb = \Image::make( public_path("{$dir}/{$filename}") );

			// Change extension and encode as jpg
			if ( preg_match('#\.[^.]+$#', $filename, $matches) )
			{
				$ofilename = $filename;

				$filename = preg_replace('#\.[^.]+$#', '.jpg', $filename); // Change extension
				$thumb->encode('jpg'); // Encode

				// Resize
				$thumb->resize($target_width, $target_height, function($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->save( public_path("{$dir}/{$filename}") );

				if ( $matches[0] != '.jpg' )
				{
					@unlink( public_path("{$dir}/{$ofilename}") );
				}
			}

			// Define flags
			@list($w, $h) = @getimagesize( public_path("{$dir}/{$filename}") );
			$is_vertical = ( $w && $h && $w < $h ) ? true : false;
			$has_size = ( $w && $w < 1280 ) ? false : true;

			return response()->json([
				'success' => true,
				'directory' => $dir,
				'filename' => $filename,
				'html' => view('account.properties.form-image-thumb',[
										'image_url' => "/{$dir}/{$filename}",
										'image_id' => "new_/{$dir}/{$filename}",
										'warning_orientation' => $is_vertical,
										'warning_size' => $has_size ? 0 : 1,
									])->render(),
			], 200);
		}

		return response()->json([
			'error' => true,
		], 400);
	}

}
