<?php namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class DomainNameController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-domainname');
	}

	public function getIndex()
	{
		return view('account.site.domainname.index');
	}

	public function postIndex()
	{
		// Remove trailing slashes from domain
		$this->request->merge([
			'domain' => rtrim($this->request->input('domain'), '/'),
		]);
		// Validate general fields
		$fields = [
			'domain' => 'url|unique:sites_domains,domain,'.$this->site->id.',site_id',
		];

		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// remove trailing slashes
		$domain = $this->request->input('domain');

		// Get /create domain
		if ( $domain )
		{
			$item = $this->site->domains()->where('default',1)->first();
			if ( $item )
			{
				$item->update([
					'domain' => $domain,
				]);
			}
			else
			{
				$this->site->domains()->create([
					'domain' => $domain,
					'default' => 1,
				]);
			}
		}
		// Delete domain
		else
		{
			$this->site->domains()->delete();
		}

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}

	public function getCheck($type)
	{
		$error = true;

		switch ( $type ) {
			case 'domain':
				$domain = rtrim($this->request->input('domain'), '/');
				// If domain is same as molista domain, false
				$molista_domain = \Config::get('app.application_domain');
				if ( $molista_domain != 'localhost' && preg_match('#\.'.$molista_domain.'(\/)?$#', $domain) )
				{
					break;
				}
				$query = \App\SiteDomains::where('domain', $domain);
				if ( $this->request->input('id') )
				{
					$query->where('site_id', '!=', $this->request->input('id'));
				}
				$error = $query->count();
				break;
		}

		echo $error ? 'false' : 'true';
	}

}
