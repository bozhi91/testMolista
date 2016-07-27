<?php namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class CountriesController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-countries');
	}

	public function getIndex()
	{
		$countries = \App\Models\Geography\Country::withTranslations()->orderBy('name')->lists('name','id')->all();
		return view('account.site.countries.index', compact('countries'));
	}

	public function postIndex()
	{
		// Validate general fields
		$fields = [
			'limit_countries' => 'boolean',
			'country_id' => 'required|exists:countries,id',
			'country_ids' => 'array',
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$this->site->country_id = $this->request->input('country_id');

		if ( $this->request->input('limit_countries') == 1 )
		{
			$this->site->country_ids = array_filter(array_unique(array_merge(
											$this->request->input('country_ids', []), 
											[ $this->site->country_id ]
										)));
		}
		else
		{
			$this->site->country_ids = [];
		}

		$this->site->save();

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}

}
