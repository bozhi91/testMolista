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
			->withSiteConfiguration($this->site->id)
			->withSiteProperties($this->site->id);

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

		$configuration = @json_decode( $marketplace->marketplace_configuration );

		// Properties
		$query = $this->site->properties()->withTranslations()->withMarkeplaceEnabled($marketplace->id);

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

		$current_tab = session('current_tab', $this->request->input('current_tab','general'));

		return view('account.marketplaces.configure', compact('marketplace','configuration','properties','current_tab'));
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

}
