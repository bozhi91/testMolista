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
		$query = \App\Models\Marketplace::enabled()->withSiteConfiguration($this->site->id);

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
			default:
				$query->orderBy('marketplaces.name', $order);
		}

		$marketplaces = $query->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('account.marketplaces.index', compact('marketplaces'));
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
		
		return view('account.marketplaces.configure', compact('marketplace','configuration'));
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
			return redirect()->back()->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
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
}
