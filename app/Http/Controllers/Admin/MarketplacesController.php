<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;

class MarketplacesController extends \App\Http\Controllers\Controller
{

	public function __initialize() {
		$this->middleware([ 'permission:marketplace-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:marketplace-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:marketplace-edit' ], [ 'only' => [ 'edit','update'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Models\Marketplace::with('country');

		// Filter by name
		if ( $this->request->input('name') )
		{
			$query->where('name', 'LIKE', "%{$this->request->input('name')}%");
		}

		$marketplaces = $query->orderBy('name','asc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.marketplaces.index', compact('marketplaces'));
	}

	public function create()
	{
		$locales = \App\Models\Locale::orderBy('native')->lists('native','locale')->all();
		$countries = \App\Models\Geography\Country::withTranslations()->orderBy('name')->lists('name','id')->all();
		return view('admin.marketplaces.create', compact('locales','countries'));
	}

	public function store()
	{
		$data = $this->request->all();

		$validator = \App\Models\Marketplace::getValidator($data);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Marketplace::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		if ( $this->request->file('logo') )
		{
			\App\Models\Marketplace::saveLogo($item,$this->request);
		}

		return redirect()->action('Admin\MarketplacesController@edit', $item->id)->with('success', trans('admin/marketplaces.messages.created'));
	}

	public function edit($id)
	{
		$marketplace = \App\Models\Marketplace::withTranslations()->findOrFail($id);
		$locales = \App\Models\Locale::orderBy('native')->lists('native','locale')->all();
		$countries = \App\Models\Geography\Country::withTranslations()->orderBy('name')->lists('name','id')->all();
		return view('admin.marketplaces.edit', compact('marketplace','locales','countries'));
	}

	public function update($id)
	{
		$marketplace = \App\Models\Marketplace::findOrFail($id);

		$data = $this->request->all();
		$validator = \App\Models\Marketplace::getUpdateValidator($data, $id);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Marketplace::saveModel($data, $id);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		if ( $this->request->file('logo') )
		{
			\App\Models\Marketplace::saveLogo($item,$this->request);
		}

		return redirect()->action('Admin\MarketplacesController@edit', $id)->with('success', trans('admin/marketplaces.messages.updated'));
	}

	protected function getCheck($type) 
	{
		$error = true;

		switch ( $type ) {
			case 'code':
				$query = \App\Models\Marketplace::where('code',$this->request->input('code'));
				if ( $this->request->input('exclude') )
				{
					$query->where('id', '!=', $this->request->input('exclude'));
				}
				$error = $query->count();
				break;
		}

		echo $error ? 'false' : 'true';
	}

}
