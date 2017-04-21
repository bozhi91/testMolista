<?php namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class MarketplacesController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'marketplaces');
	}

	public function getIndex()
	{
		$query = \App\Models\Marketplace::enabled()
			->with('countries')
			->withSiteConfiguration($this->site->id)
			->withSiteProperties($this->site->id);

		// Filter by title
		if ( $this->request->input('title') )
		{
			$clean_filters = true;
			$query->where('marketplaces.name', 'LIKE', "%{$this->request->input('title')}%");
		}

		// Filter by country
		if ( $this->request->input('country') )
		{
			$clean_filters = true;
			$query->ofCountry($this->request->input('country'));
		}

		switch ( $this->request->input('order') )
		{
			case 'desc':
				$order = 'desc';
				break;
			default:
				$order = 'asc';
		}

		switch ( $this->request->input('orderby') )
		{
			case 'title':
			default:
				$query->orderBy('marketplaces.name', $order);
		}

		$marketplaces = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$total_properties = $this->site->properties()->enabled()->count();

		$countries = \App\Models\Geography\Country::withTranslations()->withMarketplaces()->orderBy('name')->get();

		$this->set_go_back_link();

		return view('account.marketplaces.index', compact('marketplaces','countries','clean_filters','total_properties'));
	}

	public function getConfigure($code)
	{
		$marketplace = \App\Models\Marketplace::enabled()->where('code',$code)->withSiteConfiguration($this->site->id)->first();
		if ( !$marketplace )
		{
			abort(404);
		}

		if ( $marketplace->requires_contact )
		{
			return $this->getContact($marketplace->code);
		}

		$configuration = @json_decode( $marketplace->marketplace_configuration );

		// Properties
		$query = $this->site->properties()->withMarketplaceEnabled($marketplace->id);

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
			case 'title':
				$query->orderBy('title', $order);
				break;
			case 'price':
				$query->orderBy('price', $order);
				break;
			case 'address':
				$query->orderBy('address', $order);
				break;
			case 'creation':
				$query->orderBy('created_at', $order);
				break;
			case 'exported':
				$query->orderBy('exported_to_marketplace', $order);
				break;
			case 'enabled':
				$query->orderBy('enabled', $order);
				break;
			case 'reference':
			default:
				$query->orderBy('ref', $order);
		}

		$properties = $query->orderBy('ref', 'asc')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$logs = \App\Models\Site\ApiPublication::where('site_id', $this->site->id)->where('marketplace_id', $marketplace->id)->orderBy('updated_at', 'desc')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$current_tab = session('current_tab', $this->request->input('current_tab','general'));

		return view('account.marketplaces.configure', compact('marketplace','configuration','properties','logs','current_tab'));
	}
	public function postConfigure($code)
	{
		$marketplace = \App\Models\Marketplace::enabled()->where('code',$code)->first();
		if ( !$marketplace )
		{
			abort(404);
		}

		if ( $this->site->marketplace_helper->saveConfiguration($marketplace->id, $this->request->all()) )
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('error', trans('general.messages.error'));
	}

	public function postForget($code)
	{
		$marketplace = \App\Models\Marketplace::enabled()->where('code',$code)->first();
		if ( !$marketplace )
		{
			abort(404);
		}

		$this->site->marketplace_helper->saveConfiguration($marketplace->id, [ 'marketplace_enabled'=>0 ]);

		return redirect()->back();
	}

	public function getContact($code)
	{
		$marketplace = \App\Models\Marketplace::enabled()->where('code',$code)->withSiteConfiguration($this->site->id)->first();
		if ( !$marketplace )
		{
			abort(404);
		}

		$contact_data = (object) [
			'name' => \Auth::user()->name,
			'email' => \Auth::user()->email,
		];

		if ( !$marketplace->requires_contact )
		{
			return redirect()->action('Account\MarketplacesController@getConfigure',$marketplace->code);
		}

		return view('account.marketplaces.contact', compact('marketplace','contact_data'));
	}
	public function postContact($code)
	{
		$marketplace = \App\Models\Marketplace::enabled()->where('code',$code)->withSiteConfiguration($this->site->id)->first();
		if ( !$marketplace )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$validator = \Validator::make($this->request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$data = array_merge($this->request->all(), [
			'marketplace' => $marketplace->name,
			'site_id' => $this->site->id,
			'site_url' => $this->site->main_url,
		]);

		\Mail::send('emails.corporate.marketplace', $data, function($message) use ($data) {
			$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
			$message->subject( trans('account/marketplaces.contact.title', [ 'marketplace' => $data['marketplace'] ]) );
			$message->to('info@molista.com');
		});

		if ( count(\Mail::failures()) > 0 )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->back()->with('success', trans('account/marketplaces.contact.success'));
	}

	public function getStatus($code)
	{
		$marketplace = \App\Models\Marketplace::enabled()->where('code',$code)->first();
		if ( !$marketplace )
		{
			return [ 'error' => 1 ];
		}

		$property = $this->site->properties()->find( $this->request->input('property_id') );
		if ( !$property || $property->export_to_all )
		{
			return [ 'error' => 1 ];
		}

		if ( $marketplace->properties->contains( $property->id ) )
		{
			$status = 0;
			$marketplace->properties()->detach( $property->id );
		}
		else
		{
			$status = 1;
			$marketplace->properties()->attach( $property->id );
		}

		\File::deleteDirectory($this->site->xml_path, true);

		return [
			'success' => true,
			'status' => $status,
		];
	}
}
