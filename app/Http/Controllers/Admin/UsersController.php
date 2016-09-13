<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{

	public function __initialize() {
		$this->middleware([ 'permission:user-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:user-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:user-edit' ], [ 'only' => [ 'edit','update'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\User::with('roles')->with('sites')->withMinLevel($this->auth->user()->role_level);

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

		// Filter by role
		if ( $this->request->input('role') )
		{
			$query->withRole( $this->request->input('role') );
		}

		$users = $query->orderBy('created_at','desc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$roles = \App\Models\Role::withMinLevel($this->auth->user()->role_level)->orderBy('display_name')->lists('display_name','name');

		$this->set_go_back_link();

		return view('admin.users.index', compact('users','roles'));
	}

	public function create()
	{
		$roles = \App\Models\Role::withMinLevel($this->auth->user()->role_level)->orderBy('display_name')->get();

		$locales = [];
		foreach (\LaravelLocalization::getSupportedLocales() as $iso => $def)
		{
			$locales[$iso] = $def['native'];
		}

		$translation_locales = \App\Models\Locale::where('admin',1)->orderBy('native')->get();

		return view('admin.users.create', compact('locales','roles','translation_locales'));
	}

	public function store()
	{
		// Validate
		$validator = \Validator::make($this->request->all(), [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|min:6',
			'locale' => 'required|string|in:'.implode(',',\LaravelLocalization::getSupportedLanguagesKeys()),
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Check email
		if ( \App\User::where('email', $this->request->input('email'))->count() )
		{
			return redirect()->back()->withInput()->with('error', trans('admin/users.email.used'));
		}

		// Get user
        $user = \App\User::create([
            'name' => $this->request->input('name'),
            'email' => $this->request->input('email'),
            'locale' => $this->request->input('locale'),
            'password' => bcrypt($this->request->input('password')),
        ]);

		if ( !$user )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return $this->update($user->id);
	}

	public function edit($id)
	{
		$user = \App\User::with('roles')->with('sites')->with('translation_locales')->WithMinLevel($this->auth->user()->role_level)->findOrFail($id);

		$roles = \App\Models\Role::orderBy('display_name')->get();

		$locales = [];
		foreach (\LaravelLocalization::getSupportedLocales() as $iso => $def)
		{
			$locales[$iso] = $def['native'];
		}

		$translation_locales = \App\Models\Locale::where('admin',1)->orderBy('native')->get();

		return view('admin.users.edit', compact('user','locales','roles','translation_locales'));
	}

	public function update($id)
	{
		// Validate
		$fields = [
			'name' => 'required|string',
			'email' => 'required|email',
			'locale' => 'required|string|in:'.implode(',',\LaravelLocalization::getSupportedLanguagesKeys()),
			'roles' => 'required|array',
			'locales' => 'array',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->action('Admin\UsersController@edit', $id)->withInput()->withErrors($validator);
		}

		// Check email
		if ( \App\User::where('email', $this->request->input('email'))->where('id','!=',$id)->count() )
		{
			return redirect()->action('Admin\UsersController@edit', $id)->withInput()->with('error', trans('admin/users.email.used'));
		}

		// Check roles
		if ( empty($this->request->input('roles')) || !is_array($this->request->input('roles')))
		{
			return redirect()->action('Admin\UsersController@edit', $id)->withInput()->with('error', trans('admin/users.roles.required'));
		}

		// Get user
		$user = \App\User::with('roles')->with('translation_locales')->WithMinLevel($this->auth->user()->role_level)->findOrFail($id);

		// Update data
		foreach ($fields as $field => $def)
		{
			if ( $field == 'roles' || $field == 'locales' || $field == 'site_id' )
			{
				continue;
			}
			$user->$field = $this->request->input($field);
		}
		$user->save();

		// Update roles
		$user->detachRoles();
		foreach ($this->request->input('roles') as $role_name) 
		{
			$role = \App\Models\Role::where('name', $role_name)->first();
			if ( !$role )
			{
				continue;
			}
			$user->roles()->attach( $role->id );
		}

		// Update translation locales
		foreach ($user->translation_locales as $translation_locale) 
		{
			$user->translation_locales()->detach($translation_locale->id);
		}
		if ( $user->hasRole('translator') && $this->request->input('locales') && !in_array('all', $this->request->input('locales')) )
		{
			foreach ($this->request->input('locales') as $locale_id) 
			{
				$user->translation_locales()->attach($locale_id);
			}
		}

		$user->updateUserPropertiesRelations();

		return redirect()->action('Admin\UsersController@edit', $id)->with('success', trans('admin/users.saved'));
	}

}
