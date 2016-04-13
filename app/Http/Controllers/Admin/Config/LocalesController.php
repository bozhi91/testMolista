<?php

namespace App\Http\Controllers\Admin\Config;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LocalesController extends Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:locale-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:locale-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:locale-edit' ], [ 'only' => [ 'edit','update'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Models\Locale::whereNotNull('id');

		// Filter by name
		if ( $this->request->get('name') )
		{
			$query->where(function($query) {
				$query->where('name', 'LIKE', "%{$this->request->get('name')}%")
					->orWhere('native', 'LIKE', "%{$this->request->get('name')}%");
			});
		}

		$locales = $query->orderBy('native','asc')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.config.locales.index', compact('locales'));    
	}

	public function create()
	{
		$scripts = \App\Models\Locale::getScriptOptions();

		return view('admin.config.locales.create', compact('scripts'));    
	}

	public function store()
	{
		$scripts = \App\Models\Locale::getScriptOptions();

		// Validate
		$fields = [
			'locale' => 'required|string|size:2',
			'flag' => 'required|image',
			'native' => 'required|string',
			'name' => 'required|string',
			'dir' => 'required|string|in:ltr,rtl',
			'script' => 'required|string|in:'.implode(',',array_keys($scripts)),
			'regional' => 'required|string',
			'web' => 'boolean',
			'admin' => 'boolean',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return \Redirect::back()->withInput()->withErrors($validator);
		}

		// Check if exists
		$locale = \App\Models\Locale::where([
			'locale' => $this->request->get('locale'),
		])->first();
		if ( $locale )
		{
			abort(403);
		}

		// Create element
		$locale = new \App\Models\Locale;

		// Update data
		foreach ($fields as $field => $def)
		{
			$def = explode('|', $def);

			if ( in_array('boolean', $def) )
			{
				$locale->$field = $this->request->get($field) ? 1 : 0;
			}
			elseif ( $field == 'flag' )
			{
				// Move new flag
				$locale->flag = $this->request->file('flag')->getClientOriginalName();
				while ( file_exists( public_path("flags/{$locale->flag}") ) )
				{
					$locale->flag = uniqid() . '_' . $this->request->file('flag')->getClientOriginalName();
				}
				$this->request->file('flag')->move( public_path('flags'), $locale->flag );
			}
			else
			{
				$locale->$field = $this->request->get($field);
			}
		}
		$locale->save();

		// Update config file
		\App\Models\Locale::saveConfig();

		return \Redirect::action('Admin\Config\LocalesController@edit', $locale->id)->with('success', trans('admin/config/locales.created'));
	}

	public function edit($id)
	{
		$locale = \App\Models\Locale::findOrFail($id);

		$scripts = \App\Models\Locale::getScriptOptions();

		return view('admin.config.locales.edit', compact('locale','scripts'));    
	}

	public function update($id)
	{
		$scripts = \App\Models\Locale::getScriptOptions();

		// Validate
		$fields = [
			'flag' => 'image',
			'native' => 'required|string',
			'name' => 'required|string',
			'dir' => 'required|string|in:ltr,rtl',
			'script' => 'required|string|in:'.implode(',',array_keys($scripts)),
			'regional' => 'required|string',
			'web' => 'boolean',
			'admin' => 'boolean',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return \Redirect::back()->withInput()->withErrors($validator);
		}

		// Get element
		$locale = \App\Models\Locale::findOrFail($id);

		// Update data
		foreach ($fields as $field => $def)
		{
			$def = explode('|', $def);

			if ( in_array('boolean', $def) )
			{
				$locale->$field = $this->request->get($field) ? 1 : 0;
			}
			elseif ( $field == 'flag' )
			{
				if ( $this->request->file('flag') )
				{
					// Delete old flag
					if ( $locale->flag )
					{
						@unlink( public_path("flags/{$locale->flag}") );
					}
					// Move new flag
					$locale->flag = $this->request->file('flag')->getClientOriginalName();
					while ( file_exists( public_path("flags/{$locale->flag}") ) )
					{
						$locale->flag = uniqid() . '_' . $this->request->file('flag')->getClientOriginalName();
					}
					$this->request->file('flag')->move( public_path('flags'), $locale->flag );
				}
			}
			else
			{
				$locale->$field = $this->request->get($field);
			}
		}

		$locale->save();

		// Update config file
		\App\Models\Locale::saveConfig();

		return \Redirect::back()->with('success', trans('admin/config/locales.saved'));
	}

}
