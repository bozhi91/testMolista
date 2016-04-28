<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SitesController extends Controller
{

	public function __initialize() {
		$this->middleware([ 'permission:site-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:site-create' ], [ 'only' => [ 'create','store'] ]);
		$this->middleware([ 'permission:site-edit' ], [ 'only' => [ 'edit','update'] ]);
		$this->middleware([ 'permission:user-login' ], [ 'only' => [ 'show'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Site::withTranslations();

		// Filter by title
		if ( $this->request->get('title') )
		{
			$query->whereTranslationLike('title', "%{$this->request->get('title')}%");
		}

		$sites = $query->orderBy('title')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.sites.index', compact('sites'));
	}

	public function create()
	{
		$locales = \App\Models\Locale::getAdminOptions();

		$companies = \App\User::withRole('company')->orderBy('name')->lists('name','id')->toArray();
		$employees = \App\User::withRole('employee')->orderBy('name')->lists('name','id')->toArray();

		return view('admin.sites.create', compact('companies','locales','companies','employees'));
	}

	public function store()
	{
		$locales = \App\Models\Locale::where('admin',1)->lists('id','locale')->toArray();

		// Validate
		$validator = \Validator::make($this->request->all(), [
			'title' => 'required|string',
			'subdomain' => 'required|alpha_dash|max:255',
			'locales' => 'required|array',
			'locales.*' => 'required|in:'.implode(',',array_keys($locales)),
			'owners' => 'required|array',
			'owners.*' => 'required|exists:users,id',
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Validate subdomain
		if ( \App\Site::where('subdomain', $this->request->get('subdomain'))->count() )
		{
			return \Redirect::back()->withInput()->with('error', trans('admin/sites.subdomain.error.taken'));
		}

		// Validate owners
		$company_owners_total = \App\User::whereIn('id', $this->request->get('owners'))->withRole('company')->count();
		if ( $company_owners_total < count($this->request->get('owners')) )
		{
			return \Redirect::back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Create site
		$site = \App\Site::create([
			'subdomain' => $this->request->get('subdomain'),
			'custom_theme' => $this->request->get('custom_theme'),
			'theme' => $this->request->get('custom_theme', 'default'),
		]);

		if ( !$site )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Save owners
		foreach ($this->request->get('owners') as $owner_id)
		{
			$site->users()->attach($owner_id);
		}

		// Save locales && title
		foreach ($locales as $locale => $locale_id) 
		{
			if ( !in_array($locale, $this->request->get('locales')) && $locale != fallback_lang() )
			{
				continue;
			}
			// Save locale
			$site->locales()->attach($locale_id);
			// Prepare title
			$site->translateOrNew($locale)->title = $this->request->get('title');
		}

		// Save titles
		$site->save();

		return redirect()->action('Admin\SitesController@edit', $site->id)->with('success', trans('admin/sites.messages.created'));
	}

	public function edit($id)
	{
		$site = \App\Site::withTranslations()->with('users')->findOrFail($id);

		$locales = \App\Models\Locale::getAdminOptions();

		$companies = \App\User::withRole('company')->orderBy('name')->lists('name','id')->toArray();
		$employees = \App\User::withRole('employee')->orderBy('name')->lists('name','id')->toArray();

		return view('admin.sites.edit', compact('site','companies','locales','companies','employees'));
	}

	public function update($id)
	{
		$locales = \App\Models\Locale::where('admin',1)->lists('id','locale')->toArray();

		// Validate
		$fields = [
			'subdomain' => 'required|alpha_dash|max:255',
			'domains_array' => 'array',
			'domains_array.*' => 'url',
			'locales_array' => 'required|array',
			'locales_array.*' => 'required|in:'.implode(',',array_keys($locales)),
			'owners_ids' => 'required|array',
			'owners_ids.*' => 'required|exists:users,id',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->action('Admin\SitesController@edit', $id)->withInput()->withErrors($validator);
		}

		// Validate subdomain
		if ( \App\Site::where('subdomain', $this->request->get('subdomain'))->where('id','!=',$id)->count() )
		{
			return \Redirect::back()->withInput()->with('error', trans('admin/sites.subdomain.error.taken'));
		}

		// Clean domains
		$domains_array = $this->request->get('domains_array');
		foreach ($domains_array as $key => $value) 
		{
			$domains_array[$key] = rtrim($value,'/');
		}
		$this->request->merge([ 'domains_array'=>$domains_array ]);

		// Validate domains
		if ( \App\SiteDomains::whereIn('domain', $this->request->get('domains_array'))->where('site_id','!=',$id)->count() )
		{
			return \Redirect::back()->withInput()->with('error', trans('admin/sites.domain.error.taken'));
		}

		// Clean owners
		$this->request->merge([ 'owners_ids'=>array_unique($this->request->get('owners_ids')) ]);

		// Validate owners
		$company_owners_total = \App\User::whereIn('id', $this->request->get('owners_ids'))->withRole('company')->count();
		if ( $company_owners_total < count($this->request->get('owners_ids')) )
		{
			return \Redirect::back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Get site
		$site = \App\Site::findOrFail($id);

		// Update data
		$site->subdomain = $this->request->get('subdomain');
		$site->custom_theme = $this->request->get('custom_theme');
		$site->enabled = $this->request->get('enabled') ? 1 : 0;
		$site->save();

		// Update domains
		$preserve = [ 0 ];
		foreach ($this->request->get('domains_array') as $domain_id => $domain) 
		{
			if ( !$domain )
			{
				continue;
			}

			if ( $domain_id == 'new')
			{
				$site_domain = $site->domains()->create([
					'domain' => $domain,
					'default' => 1,
				]);
				$preserve[]	= $site_domain->id;
			}
			else
			{
				$site_domain = $site->domains()->find($domain_id)->update([
					'domain' => $domain,
				]);
				$preserve[]	= $domain_id;
			}

		}
		$site->domains()->whereNotIn('id',$preserve)->delete();

		// Save owners
		$site->users()->detach();
		foreach ($this->request->get('owners_ids') as $owner_id)
		{
			$site->users()->attach($owner_id);
		}

		// Save locales
		$site->locales()->detach();
		foreach ($locales as $locale => $locale_id) 
		{
			if ( in_array($locale, $this->request->get('locales_array')) || $locale == fallback_lang() )
			{
				$site->locales()->attach($locale_id);
			}
		}

		return redirect()->action('Admin\SitesController@edit', $id)->with('success', trans('admin/sites.messages.updated'));
	}

	public function show($id)
	{
		$site = \App\Site::findOrFail($id);

		if ( $autologin_url = $site->autologin_url )
		{
			return redirect()->away( $autologin_url );
		}

		$error = trans('admin/sites.messages.no.owner');
		return view('admin.error', compact('error'));
	}

}
