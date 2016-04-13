<?php

namespace App\Http\Controllers\Admin\Properties;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

	public function __initialize() {
		$this->middleware([ 'permission:property-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:property-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:property-edit' ], [ 'only' => [ 'edit','update'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Property::with('site')->with('city')->withTranslations();

		// Filter by name
		if ( $this->request->get('title') )
		{
			$query->whereTranslationLike('title', "%{$this->request->get('title')}%");
		}

		$properties = $query->orderBy('title')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.properties.index', compact('properties'));
	}

	public function show($id)
	{
		$property = \App\Property::with('site')->withTranslations()->findOrFail($id);
		return redirect()->away( $property->full_url );
	}
}
