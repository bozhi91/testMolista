<?php namespace App\Http\Controllers\Admin\Config;

use Illuminate\Http\Request;

use App\Http\Requests;

class CurrenciesController extends \App\Http\Controllers\Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:currency-view' ], [ 'only' => [ 'index', 'show' ] ]);
		$this->middleware([ 'permission:currency-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:currency-edit' ], [ 'only' => [ 'edit','update'] ]);
		$this->middleware([ 'permission:currency-delete' ], [ 'only' => [ 'destroy'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Models\Currency::withTranslations();

		// Filter by name
		if ( $this->request->input('title') )
		{
			$query->whereTranslationLike('title', "%{$this->request->input('title')}%");
		}

		$currencies = $query->orderBy('title','asc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.config.currencies.index', compact('currencies'));
	}

	public function create()
	{
		$locales = \App\Models\Translation::getCachedLocales();
		return view('admin.config.currencies.create', compact('locales'));
	}

	public function store()
	{
		$data = $this->request->all();
		
		$validator = \App\Models\Currency::getValidator($data);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Currency::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->action('Admin\Config\CurrenciesController@edit', $item->id)->with('success', trans('admin/config/currencies.created'));
	}

	public function edit($id)
	{
		$currency = \App\Models\Currency::findOrFail($id);
		$locales = \App\Models\Translation::getCachedLocales();
		return view('admin.config.currencies.edit', compact('currency','locales'));
	}

	public function update($id)
	{
		$currency = \App\Models\Currency::findOrFail($id);

		$data = $this->request->all();

		$validator = \App\Models\Currency::getUpdateValidator($data, $id);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\currency::saveModel($data, $id);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->action('Admin\Config\CurrenciesController@edit', $id)->with('success', trans('admin/config/currencies.saved'));
	}

	public function getCheck($type=false)
	{
		$query = \App\Models\Currency::whereNotNull('id');

		switch ($type) 
		{
			default:
				$query->where($type, $this->request->input($type));
		}

		if ( $this->request->input('exclude') )
		{
			$query->where('id', '!=', $this->request->input('exclude'));
		}

		echo ( $query->count() < 1 ) ? 'true' : 'false';
	}

}
