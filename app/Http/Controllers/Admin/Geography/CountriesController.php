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
/*
	public function create()
	{
		return view('admin.config.plans.create');
	}

	public function store()
	{
		$data = $this->request->all();

		$validator = \App\Models\Plan::getValidator($data);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Plan::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->action('Admin\Config\PlansController@edit', $item->id)->with('success', trans('admin/config/plans.created'));
	}

	public function edit($id)
	{
		$plan = \App\Models\Plan::findOrFail($id);
		return view('admin.config.plans.edit', compact('plan'));
	}

	public function update($id)
	{
		$plan = \App\Models\Plan::findOrFail($id);

		$data = $this->request->all();

		$validator = \App\Models\Plan::getUpdateValidator($data, $id);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Plan::saveModel($data, $id);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->action('Admin\Config\PlansController@edit', $id)->with('success', trans('admin/config/plans.saved'));
	}

	public function getCheck($type=false)
	{
		$query = \App\Models\Plan::whereNotNull('id');

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
*/
}
