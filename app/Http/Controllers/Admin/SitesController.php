<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SitesController extends Controller
{

	public function __initialize() {
		$this->middleware([ 'permission:site-view' ], [ 'only' => [ 'index' ] ]);
		$this->middleware([ 'permission:site-create' ], [ 'only' => [ 'create','store' ] ]);
		$this->middleware([ 'permission:site-edit' ], [ 'only' => [ 'edit','update','getInvoice','postInvoice','deleteInvoice '] ]);
		$this->middleware([ 'permission:user-login' ], [ 'only' => [ 'show' ] ]);

		parent::__initialize();
	}

	public function index()
	{
		$query = \App\Site::withTranslations()
							->with('country')
							->with('properties')
							->with('users')
							;

		// Filter by title
		if ( $this->request->input('title') )
		{
			$query->whereTranslationLike('title', "%{$this->request->input('title')}%");
		}

		// Filter by web_transfer_requested
		if ( $this->request->input('transfer') )
		{
			$query->where('web_transfer_requested', intval($this->request->input('transfer'))-1);
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
			case 'id':
				$query->orderBy('id', $order);
				break;
			case 'created':
				$query->orderBy('created_at', $order);
				break;
			case 'transfer':
				$query->orderBy('web_transfer_requested', $order);
				break;
			case 'properties':
				$query
					->leftJoin('properties','properties.site_id','=','sites.id')
					->addSelect( \DB::raw('COUNT(properties.`id`) AS total_properties') )
					->orderBy('total_properties', $order)
					->groupBy('sites.id');
				break;
			case 'users':
				$query
					->leftJoin('sites_users','sites_users.site_id','=','sites.id')
					->addSelect( \DB::raw('COUNT(sites_users.`user_id`) AS total_users') )
					->orderBy('total_users', $order)
					->groupBy('sites.id');
				break;
			case 'title':
			default:
				$query->orderBy('title', $order);
				break;
		}

		$sites = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$this->set_go_back_link();

		return view('admin.sites.index', compact('sites'));
	}

	public function create()
	{
		$locales = \App\Models\Locale::getAdminOptions();

		$companies = \App\User::withRole('company')->orderBy('name')->lists('name','id')->toArray();
		$employees = \App\User::withRole('employee')->orderBy('name')->lists('name','id')->toArray();
		$resellers = \App\Models\Reseller::orderBy('name')->lists('name','id')->toArray();

		return view('admin.sites.create', compact('companies','locales','companies','employees','resellers'));
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
			'reseller_id' => 'exists:resellers,id',
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Validate subdomain
		if ( \App\Site::where('subdomain', $this->request->input('subdomain'))->count() )
		{
			return redirect()->back()->withInput()->with('error', trans('admin/sites.subdomain.error.taken'));
		}

		// Validate owners
		$company_owners_total = \App\User::whereIn('id', $this->request->input('owners'))->withRole('company')->count();
		if ( $company_owners_total < count($this->request->input('owners')) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Create site
		$site = \App\Site::create([
			'subdomain' => $this->request->input('subdomain'),
			'custom_theme' => $this->request->input('custom_theme'),
			'theme' => $this->request->input('custom_theme', 'default'),
			'payment_currency' => 'EUR',
			'site_currency' => 'EUR',
			'country_code' => 'ES',
			'country_id' => 68,
			'timezone' => 'Europe/Madrid',
			'reseller_id' => $this->request->input('reseller_id') ? $this->request->input('reseller_id') : null,
		]);

		if ( !$site )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Save owners
		foreach ($this->request->input('owners') as $owner_id)
		{
			$site->users()->attach($owner_id);
		}

		// Save locales && title
		foreach ($locales as $locale => $locale_id) 
		{
			if ( !in_array($locale, $this->request->input('locales')) && $locale != fallback_lang() )
			{
				continue;
			}
			// Save locale
			$site->locales()->attach($locale_id);
			// Prepare title
			$site->translateOrNew($locale)->title = $this->request->input('title');
		}

		// Save titles
		$site->save();

		// Create on ticket system
		$site->ticket_adm->createSite();

		return redirect()->action('Admin\SitesController@edit', $site->id)->with('success', trans('admin/sites.messages.created'));
	}

	public function edit($id)
	{
		$site = \App\Site::withTranslations()->with('users')->with('plan')->findOrFail($id);

		$plan_details = $site->planchanges()->active()->first();

		$locales = \App\Models\Locale::getAdminOptions();

		$owners = $site->users()->whereIn('id', $site->owners_ids)->orderBy('name')->lists('name','id')->all();
		$companies = \App\User::withRole('company')->whereNotIn('id', $site->owners_ids)->orderBy('name')->lists('name','id')->all();
		$resellers = \App\Models\Reseller::orderBy('name')->lists('name','id')->toArray();

		$invoices = $site->invoices()->orderBy('uploaded_at','desc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$current_tab = session('current_tab', $this->request->input('current_tab','site'));

		return view('admin.sites.edit', compact('site','locales','owners','companies','resellers','invoices','current_tab','plan_details'));
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
			'reseller_id' => 'exists:resellers,id',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->action('Admin\SitesController@edit', $id)->withInput()->withErrors($validator);
		}

		// Validate subdomain
		if ( \App\Site::where('subdomain', $this->request->input('subdomain'))->where('id','!=',$id)->count() )
		{
			return redirect()->back()->withInput()->with('error', trans('admin/sites.subdomain.error.taken'));
		}

		// Clean domains
		$domains_array = $this->request->input('domains_array');
		foreach ($domains_array as $key => $value) 
		{
			$domains_array[$key] = rtrim($value,'/');
		}
		$this->request->merge([ 'domains_array'=>$domains_array ]);

		// Validate domains
		if ( \App\SiteDomains::whereIn('domain', $this->request->input('domains_array'))->where('site_id','!=',$id)->count() )
		{
			return redirect()->back()->withInput()->with('error', trans('admin/sites.domain.error.taken'));
		}

		// Get site
		$site = \App\Site::withTranslations()->findOrFail($id);

		// Clean owners
		$this->request->merge([ 
			'owners_ids' => array_unique(array_merge($this->request->input('owners_ids'), $site->owners_ids) )
		]);

		// Validate owners
		$company_owners_total = \App\User::whereIn('id', $this->request->input('owners_ids'))->withRole('company')->count();
		if ( $company_owners_total < count($this->request->input('owners_ids')) )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		// Update data
		$site->subdomain = $this->request->input('subdomain');
		$site->custom_theme = $this->request->input('custom_theme');
		$site->reseller_id = $this->request->input('reseller_id') ? $this->request->input('reseller_id') : null;
		$site->enabled = $this->request->input('enabled') ? 1 : 0;
		$site->save();

		// Update domains
		$preserve = [ 0 ];
		foreach ($this->request->input('domains_array') as $domain_id => $domain) 
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
		$site->users()->detach( $site->owners_ids );
		foreach ($this->request->input('owners_ids') as $owner_id)
		{
			$site->users()->attach($owner_id);
		}

		// Save locales
		$site->locales()->detach();
		foreach ($locales as $locale => $locale_id) 
		{
			if ( in_array($locale, $this->request->input('locales_array')) || $locale == fallback_lang() )
			{
				$site->locales()->attach($locale_id);
			}
		}

		// Associate owners to tickets
		$site->ticket_adm->associateUsers( $site->users()->with('roles')->whereIn('id', $site->owners_ids)->get() );

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

	public function postInvoice($id)
	{
		$site = \App\Site::findOrFail($id);

		$data = array_merge($this->request->all(), [
			'site_id' => $site->id,
		]);

		// Validate
		$fields = [
			'title' => 'required|string',
			'amount' => 'required|numeric',
			'document.*' => 'mimes:pdf',
			'uploaded_at' => 'required|array',
		];
		$validator = \App\Models\Site\Invoice::getCreateValidator($data, false);
		if ($validator->fails())
		{
			return redirect()->back()->with('current_tab','invoices')->withInput()->withErrors($validator);
		}

		$invoice = \App\Models\Site\Invoice::saveModel($data);
		if ( !$invoice )
		{
			return redirect()->back()->with('current_tab','invoices')->withInput()->with('error', trans('general.messages.error'));
		}

		$invoice->saveInvoice($this->request->file('document'));

		return redirect()->back()->with('current_tab','invoices')->with('success', trans('admin/sites.invoices.message.success'));
	}
	public function deleteInvoice($invoice_id)
	{
		$invoice = \App\Models\Site\Invoice::findOrFail($invoice_id);

		if ( $invoice->document )
		{
			@unlink( $this->invoice_path );
		}

		$invoice->delete();

		return redirect()->back()->with('current_tab','invoices')->with('success', trans('admin/sites.invoices.message.deleted'));
	}
	public function getInvoice($invoice_id,$filename=false)
	{
		$invoice = \App\Models\Site\Invoice::findOrFail($invoice_id);

		if ( !$invoice->document )
		{
			abort(404);
		}

		return response()->download($invoice->invoice_path, $filename);
	}

}
