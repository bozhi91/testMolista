<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class SignupController extends \App\Http\Controllers\CorporateController
{
	protected $session_name = 'signup_form';

	protected $validation_error;

	public function getIndex()
	{
		$data = session()->get($this->session_name);
		if ( $data && !old('user.type') )
		{
			session()->flash('_old_input', $data);
		}

		$plans = \App\Models\Plan::getEnabled( \App\Session\Currency::get('code') );

		if ( $this->request->input('plan') && $plan = @$plans[$this->request->input('plan')] )
		{
			return redirect()->action('Corporate\SignupController@getIndex')->with('plan_requested', $plan->code);
		}

		$plan_requested = session()->get('plan_requested');
		if ( $plan_requested && $plan = @$plans[$plan_requested] )
		{
			session()->flash('_old_input.pack', $plan->code);
		}

		$languages = [ \App::getLocale() => '' ];
		foreach (\LaravelLocalization::getSupportedLocales() as $locale => $def)
		{
			$languages[$locale] = $def['native'];
		}

		$countries = \App\Models\Geography\Country::withTranslations()->orderBy('name')->lists('name','id')->all();

		$paymethods = \App\Models\Plan::getPaymentOptions( $this->geolocation['config']['pay_methods'] );

		return view('corporate.signup.index', compact('plans','languages','countries','paymethods'));
	}
	public function postIndex()
	{
		$data = $this->request->all();

		if ( !$this->validateData( $data ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->back()->withInput()->withErrors($this->validation_error);
			}
			return redirect()->back()->withInput()->with('error', $this->validation_error);
		}

		session()->put($this->session_name, $data);

		return redirect()->action('Corporate\SignupController@getConfirm');
	}

	public function getConfirm()
	{
		$data = session()->get($this->session_name);

		if ( !$data )
		{
			return redirect()->action('Corporate\SignupController@getIndex');
		}

		$data = $this->prepareData($data);

		return view('corporate.signup.confirm', compact('data'));
	}
	public function postConfirm()
	{
		// Get data
		$data = session()->get($this->session_name);

		// Validate data
		if ( !$this->validateData( $data ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->action('Corporate\SignupController@getIndex')->withErrors($this->validation_error);
			}
			return redirect()->action('Corporate\SignupController@getIndex')->with('error', $this->validation_error);
		}

		// Prepare data
		$data = $this->prepareData($data);

		// Get user
		if ( $data['user']['type'] == 'old' )
		{
			$user = \App\User::where('email', $data['user']['old']['email'])->first();
			if ( !$user )
			{
				return redirect()->action('Corporate\SignupController@getIndex')->with('error', trans('general.messages.error'));
			}
		}
		// Or create new one
		else
		{
			$user = \App\User::create([
				'name' => $data['user']['new']['name'],
				'email' => $data['user']['new']['email'],
				'phone' => $data['user']['new']['phone'],
				'locale' => \LaravelLocalization::getCurrentLocale(),
				'password' => bcrypt($data['user']['new']['password']),
			]);
			if ( !$user )
			{
				return redirect()->action('Corporate\SignupController@getIndex')->with('error', trans('general.messages.error'));
			}
			// With company role
			$role = \App\Models\Role::where('name', 'company')->first();
			if ( !$role )
			{
				return redirect()->action('Corporate\SignupController@getIndex')->with('error', trans('general.messages.error'));
			}
			$user->roles()->attach( $role->id );
		}

		// Create site with free plan
		$free_plan = \App\Models\Plan::where('is_free',1)->first();
		$site = \App\Site::create([
			'subdomain' => $data['subdomain'],
			'theme' => 'default',
			'plan_id' => $free_plan->id,
			'invoicing' => $data['invoicing'],
			'web_transfer_requested' => empty($data['web_transfer_requested']) ? 0 : 1,
			'payment_currency' => $this->currency->code,
			'site_currency' => $this->currency->code,
			'country_code' => $this->geolocation['config']['code'],
			'country_id' => $this->geolocation['config']['id'],
		]);
		if ( !$site )
		{
			return redirect()->action('Corporate\SignupController@getIndex')->with('error', trans('general.messages.error'));
		}

		// Save owner
		$site->users()->attach($user->id);

		// Set plan type
		$plan_is_free = @$data['plan']['is_free'];

		// Save locales && title
		if ( $plan_is_free )
		{
			if ( empty($data['language']) )
			{
				return redirect()->action('Corporate\SignupController@getIndex')->with('error', trans('general.messages.error'));
			}

			$locales_codes = [ $data['language'] ];
			if ( !in_array(fallback_lang(), $locales_codes) )
			{
				$locales_codes[] = fallback_lang();
			}
			$locales = \App\Models\Locale::where('web',1)->whereIn('locale',$locales_codes)->lists('id','locale');
		}
		else
		{
			$locales = \App\Models\Locale::whereIn('locale', [ fallback_lang() ])->lists('id','locale');
		}

		foreach ($locales as $locale => $locale_id) 
		{
			$site->locales()->attach($locale_id);
			$site->translateOrNew($locale)->title = $data['subdomain'];
		}

		// Save site
		$site->save();

		// Create on ticket system
		$site->ticket_adm->createSite();

		$locale = \LaravelLocalization::getCurrentLocale();

		// If plan is not free
		if ( !$plan_is_free )
		{
			// Create sites_planchange
			$planchange = $site->planchanges()->create([
				'plan_id' => $data['plan']['id'],
				'payment_interval' => $data['payment_interval'][$data['plan']['code']],
				'payment_method' => $data['payment_method'],
				'old_data' => [
					'plan_id' => $site->plan_id ? $site->plan_id : $free_plan->id,
					'payment_interval' => $site->payment_interval,
					'payment_method' => $site->payment_method,
					'iban_account' => $site->iban_account,
				],
				'new_data' => [
					'plan_id' => $data['plan']['id'],
					'payment_interval' => $data['payment_interval'][$data['plan']['code']],
					'payment_method' => $data['payment_method'],
					'iban_account' => @$data['iban_account'],
				],
				'invoicing' => $data['invoicing'],
				'locale' => $locale,
			]);
		}

		// Delete session
		session()->forget($this->session_name);

		// Send welcome email
		$job = ( new \App\Jobs\SendWelcomeEmail($site, $locale) )->onQueue('emails');
		$this->dispatch( $job );

		// Redirect to finish
		return redirect()
					->action('Corporate\SignupController@getFinish')
					->with('site_id', $site->id)
					->with('site_subdomain', $site->subdomain)
					->with('show_signup_adwords_tracker',1);
	}

	public function getFinish($site_id=false,$site_subdomain=false)
	{
		if ( !$site_id )
		{
			$site_id = session()->get('site_id');
		}

		if ( !$site_subdomain )
		{
			$site_subdomain = session()->get('site_subdomain');
		}

		$site = \App\Site::findOrFail($site_id);
		if ( $site->subdomain != $site_subdomain )
		{
			abort(404);
		}

		$data = $site->getSignupInfo();
		$data['site'] = $site;
		$data['show_signup_adwords_tracker'] = session()->get('show_signup_adwords_tracker');

		return view('corporate.signup.finish', $data);
	}

	protected function prepareData($data=false)
	{
		$country = \App\Models\Geography\Country::withTranslations()->findOrFail( $data['invoicing']['country_id'] );
		$data['invoicing']['country'] = $country->name;

		$data['plan'] = \App\Models\Plan::where('code', $data['pack'])->first()->toArray();

		return $data;
	}

	protected function validateData($data=false)
	{
		$plans = \App\Models\Plan::getEnabled( \App\Session\Currency::get('code') );
		$plan_selected = @$plans[$data['pack']];

		$fields = [
			'user.type' => 'required|in:new,old',
			'pack' => 'required|in:'.$plans->implode('code',','),
			'subdomain' => 'required|alpha_dash|max:255|unique:sites,subdomain',
			'web_transfer_requested' => 'boolean',
			'iban_account' => 'required_if:payment_method,transfer',
			'invoicing.type' => 'required|in:individual,company',
			'invoicing.company' => 'required_if:invoicing.type,company',
			'invoicing.first_name' => 'required|string',
			'invoicing.last_name' => 'required|string',
			'invoicing.email' => 'required|email',
			'invoicing.tax_id' => '',
			'invoicing.street' => 'required|string',
			'invoicing.zipcode' => '',
			'invoicing.city' => 'required|string',
			'invoicing.country_id' => 'required|exists:countries,id',
			'accept' => 'accepted',
		];

		// User type related fields
		switch ( @$data['user']['type'] )
		{
			case 'new':
				$fields['user.new.name'] = 'required|string';
				$fields['user.new.email'] = 'required|email|unique:users,email';
				$fields['user.new.password'] = 'required|string';
				$fields['user.new.phone'] = 'required|string';
				break;
			case 'old':
				$fields['user.old.email'] = 'required|email|exists:users,email';
				$fields['user.old.password'] = 'required|string|min:6|max:20';
				break;
		}

		// Plans related fields
		foreach ($plans as $plan)
		{
			$fields["payment_interval.{$plan->code}"] = "required_if:pack,{$plan->code}|in:year,month";
		}

		// Language is required
		if ( $plan_selected && $plan_selected->max_languages == 1 )
		{
			$fields['language'] = 'required|in:'.implode(',', array_keys(\LaravelLocalization::getSupportedLocales()));
		}

		if ( $plan_selected && !$plan_selected->is_free )
		{
			$fields['payment_method'] = 'required|in:'.implode(',', $this->geolocation['config']['pay_methods']);
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			$this->validation_error = $validator;
			return false;
		}

		// Validate old user type = company
		if ($data['user']['type'] == 'old' )
		{
			$credentials = \Auth::validate([
				'email' => $data['user']['old']['email'],
				'password' => $data['user']['old']['password'],
			]);

			if ( !$credentials )
			{
				$this->validation_error = trans('corporate/signup.user.old.error.combination');
				return false;
			}

			$user = \App\User::where('email', $data['user']['old']['email'])->first();
			if ( !$user )
			{
				$this->validation_error = trans('corporate/signup.user.old.error.combination');
				return false;
			}

			if ( !$user->hasRole('company') )
			{
				if ( $user->hasRole('employee') )
				{
					$this->validation_error = trans('corporate/signup.user.old.error.employee');
					return false;
				}
				if ( $user->hasRole('admin') )
				{
					$this->validation_error = trans('corporate/signup.user.old.error.admin');
					return false;
				}
				$this->validation_error = trans('general.messages.error');
				return false;
			}
		}

		return true;
	}

	public function getValidate($type)
	{
		$fields = false;
		$result = false;

		switch ($type)
		{
			case 'email':
				$fields = [
					'email' => 'required|email|unique:users,email',
				];
				break;
			default:
				break;
		}

		if ( $fields )
		{
			$validator = \Validator::make($this->request->all(), $fields);
			$result = !$validator->fails();
		}

		echo $result ? 'true' : 'false';
		exit;
	}

}
