<?php namespace App\Http\Controllers\Admin\Geography;

use Illuminate\Http\Request;

use App\Http\Requests;

class CountriesController extends \App\Http\Controllers\Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:geography-view' ], [ 'only' => [ 'index', 'show' ] ]);
		$this->middleware([ 'permission:geography-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:geography-edit' ], [ 'only' => [ 'edit','update'] ]);
		$this->middleware([ 'permission:geography-delete' ], [ 'only' => [ 'destroy'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Models\Geography\Country::withTranslations();

		// Filter by name
		if ( $this->request->get('name') )
		{
			$query->whereTranslationLike('name', "%{$this->request->get('name')}%");
		}

		$countries = $query->orderBy('name','asc')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.geography.countries.index', compact('countries'));
	}

	public function create()
	{
		$currencies = \App\Models\Currency::withTranslations()->orderBy('title')->lists('title','code')->all();
		$locales = \App\Models\Locale::whereIn('locale', \App\Models\Locale::getCorporateLocales())->orderBy('native')->lists('native','locale')->all();

		return view('admin.geography.countries.create', compact('currencies','locales'));
	}

	public function store()
	{
		$data = $this->request->all();

		$validator = \App\Models\Geography\Country::getCreateValidator($data,false);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Geography\Country::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		\App\Models\Geography\Country::saveImages($item,$this->request);

		return redirect()->action('Admin\Geography\CountriesController@edit', $item->id)->with('success', trans('admin/geography/countries.created'));
	}

	public function edit($id)
	{
		$country = \App\Models\Geography\Country::findOrFail($id);
		$currencies = \App\Models\Currency::withTranslations()->orderBy('title')->lists('title','code')->all();
		$locales = \App\Models\Locale::whereIn('locale', \App\Models\Locale::getCorporateLocales())->orderBy('native')->lists('native','locale')->all();

		return view('admin.geography.countries.edit', compact('country','currencies','locales'));
	}

	public function update($id)
	{
		$country = \App\Models\Geography\Country::findOrFail($id);

		$data = $this->request->all();

		$validator = \App\Models\Geography\Country::getUpdateValidator($data, $id);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Geography\Country::saveModel($data, $id);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		\App\Models\Geography\Country::saveImages($item,$this->request);

		return redirect()->back()->with('success', trans('admin/geography/countries.saved'));
	}

	public function getCheck($type=false)
	{
		$query = \App\Models\Geography\Country::whereNotNull('id');

		switch ($type) 
		{
			default:
				$query->where($type, $this->request->get($type));
		}

		if ( $this->request->get('exclude') )
		{
			$query->where('id', '!=', $this->request->get('exclude'));
		}

		echo ( $query->count() < 1 ) ? 'true' : 'false';
	}

}
