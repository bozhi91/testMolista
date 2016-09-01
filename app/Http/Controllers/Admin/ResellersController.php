<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ResellersController extends Controller
{

	public function __initialize() {
		$this->middleware([ 'permission:reseller-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:reseller-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:reseller-edit' ], [ 'only' => [ 'edit','update'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Models\Reseller::with('sites');

		// Filter by ref
		if ( $this->request->input('ref') )
		{
			$query->where('ref', 'LIKE', "%{$this->request->input('ref')}%");
		}

		// Filter by name
		if ( $this->request->input('name') )
		{
			$query->where('name', 'LIKE', "%{$this->request->input('name')}%");
		}

		// Filter by email
		if ( $this->request->input('email') )
		{
			$query->where('email', 'LIKE', "%{$this->request->input('email')}%");
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
			case 'ref':
				$query->orderBy('ref', $order);
				break;
			case 'email':
				$query->orderBy('email', $order);
				break;
			case 'enabled':
				$query->orderBy('enabled', $order);
				break;
			case 'name':
			default:
				$query->orderBy('name', $order);
				break;
		}
		$resellers = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.resellers.index', compact('resellers'));
	}

	public function create()
	{
		$this->_setViewValues();
		return view('admin.resellers.create', compact('plans'));
	}

	public function store()
	{
		$data = $this->request->all();

		$validator = \App\Models\Reseller::getCreateValidator($data);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator)->with('current_tab', $this->request->input('current_tab'));
		}

		$item = \App\Models\Reseller::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'))->with('current_tab', $this->request->input('current_tab'));
		}

		return redirect()->action('Admin\ResellersController@edit', $item->id)->with('success', trans('general.messages.success.saved'))->with('current_tab', $this->request->input('current_tab'));
	}

	public function edit($id)
	{
		$reseller = \App\Models\Reseller::with('plans')->with(['sites'=>function($query){
			$query->orderBy('title')
				->withTranslations()
				->with('country')
				->with('plan')
				;
		}])->findOrFail($id);

		$this->_setViewValues();

		return view('admin.resellers.edit', compact('reseller','plan'));
	}

	public function update($id)
	{
		$data = $this->request->all();

		$validator = \App\Models\Reseller::getUpdateValidator($data, $id, false, [
			'ref' => "required|alpha_num|unique:resellers,ref,{$id}",
			'email' => "required|email|unique:resellers,email,{$id}",
		]);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator)->with('current_tab', $this->request->input('current_tab'));
		}

		$item = \App\Models\Reseller::saveModel($data, $id);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'))->with('current_tab', $this->request->input('current_tab'));
		}

		return redirect()->action('Admin\ResellersController@edit', $item->id)->with('success', trans('general.messages.success.saved'))->with('current_tab', $this->request->input('current_tab'));
	}

	public function getValidate($type)
	{
		$response = false;

		$query = \App\Models\Reseller::whereNotNull('id');

		if ( $this->request->input('exclude') )
		{
			$query->where('id', '!=', $this->request->input('exclude'));
		}

		switch ($type)
		{
			case 'ref':
				$query->where('ref', $this->request->input('ref'));
				break;
			case 'email':
				$query->where('email', $this->request->input('email'));
				break;
		}

		$response = $query->count() ? false : true;

		echo $response ? 'true' : 'false';
		exit;
	}

	protected function _setViewValues($event=false)
	{
		$plans = \App\Models\Plan::with('infocurrency')->where('is_free',0)->orderby('level')->get();
		\View::share('plans', $plans);

		$locales = [];
		foreach (\LaravelLocalization::getSupportedLocales() as $item)
		{
			$locales[$item['locale']] = $item['native'];
		}
		asort($locales);
		\View::share('locales', $locales);
	}

}
