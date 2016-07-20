<?php namespace App\Http\Controllers\Admin\Config;

use Illuminate\Http\Request;

use App\Http\Requests;

class PlansController extends \App\Http\Controllers\Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:pack-view' ], [ 'only' => [ 'index', 'show' ] ]);
		$this->middleware([ 'permission:pack-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:pack-edit' ], [ 'only' => [ 'edit','update'] ]);
		$this->middleware([ 'permission:pack-delete' ], [ 'only' => [ 'destroy'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Models\Plan::with('infocurrency');

		// Filter by name
		if ( $this->request->input('name') )
		{
			$query->where('name', 'LIKE', "%{$this->request->input('name')}%");
		}

		$plans = $query->orderBy('level','asc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.config.plans.index', compact('plans'));
	}

	public function create()
	{
		$currencies = \App\Models\Currency::withTranslations()->orderBy('code')->get();
		return view('admin.config.plans.create', compact('currencies'));
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
				$query->where($type, $this->request->input($type));
		}

		if ( $this->request->input('exclude') )
		{
			$query->where('id', '!=', $this->request->input('exclude'));
		}

		echo ( $query->count() < 1 ) ? 'true' : 'false';
	}

}
