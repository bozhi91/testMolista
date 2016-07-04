<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class SignupController extends \App\Http\Controllers\CorporateController
{
	protected $session_name = 'signup_form';

	protected $validation_error;

	public function getIndex()
	{
		session()->flush($this->session_name);

		if ( $this->request->input('plan') && $plan = \App\Models\Plan::where('code', $this->request->input('plan'))->first() )
		{
			$this->_setStep('pack',[ 'selected' => $plan->code ]);
			$this->_setStep('plan', $plan);
		}

		return redirect()->action('Corporate\SignupController@getUser');
	}

	public function getUser()
	{
		if ( $this->auth->check() )
		{
			$this->auth->logout();
		}

		$data = session()->get($this->session_name);

		return view('corporate.signup.step-user', compact('data'));
	}
	public function postUser()
	{
		if ( !$this->validateUser($this->request->input('user')) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->back()->withInput()->withErrors($this->validation_error);
			}
			return redirect()->back()->withInput()->with('error', $this->validation_error);
		}

		$this->_setStep('user', $this->request->input('user'));

		return redirect()->action('Corporate\SignupController@getPack');
	}
	protected function validateUser($data=false)
	{
		$fields = [
			'type' => 'required|in:new,old',
		];

		switch ( @$data['type'] )
		{
			case 'new':
				$fields['new.name'] = 'required|string';
				$fields['new.email'] = 'required|email|unique:users,email';
				$fields['new.password'] = 'required|string';
				$fields['new.accept'] = 'required|accepted';
				break;
			case 'old':
				$fields['old.email'] = 'required|email|exists:users,email';
				$fields['old.password'] = 'required|string|min:6|max:20';
				break;
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			$this->validation_error = $validator;
			return false;
		}

		if ($data['type'] != 'old' )
		{
			return true;
		}

		$email = $data['old']['email'];
		$password = $data['old']['password'];

		// Check user role and password
		$user = \App\User::where('email', $email)->first();
		if ( !$user )
		{
			$this->validation_error = trans('corporate/signup.user.old.error.combination');
			return false;
		}

		$credentials = \Auth::validate([
			'email' => $email,
			'password' => $password,
		]);
		if ( !$credentials )
		{
			$this->validation_error = trans('corporate/signup.user.old.error.combination');
			return false;
		}

		if ( $user->hasRole('company') )
		{
			return true;
		}

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
	}

	public function getPack()
	{
		if ( !$this->_checkStep('user') )
		{
			return redirect()->action('Corporate\SignupController@getUser');
		}

		$data = session()->get($this->session_name);

		$plans = \App\Models\Plan::getEnabled();

		return view('corporate.signup.step-pack', compact('data','plans'));
	}
	public function postPack()
	{
		if ( !$this->_checkStep('user') )
		{
			return redirect()->action('Corporate\SignupController@getUser');
		}

		$data = $this->request->input('pack');

		if ( !$this->validatePack($data) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->back()->withInput()->withErrors($this->validation_error);
			}
			return redirect()->back()->withInput()->with('error', $this->validation_error);
		}

		$this->_setStep('pack',$data);

		$this->_setStep('plan', \App\Models\Plan::where('code', $this->request->input('pack.selected'))->first()->toArray());

		return redirect()->action('Corporate\SignupController@getSite');
	}
	protected function validatePack($data=false)
	{
		$plans = \App\Models\Plan::getEnabled();

		$fields = [
			'selected' => 'required|in:'.$plans->implode('code',','),
		];
		foreach ($plans as $plan)
		{
			$fields["payment_interval.{$plan->code}"] = "required_if:selected,{$plan->code}|in:year,month";
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			$this->validation_error = $validator;
			return false;
		}

		return true;
	}

	public function getSite()
	{
		if ( !$this->_checkStep('pack') )
		{
			return redirect()->action('Corporate\SignupController@getPack');
		}

		$data = session()->get($this->session_name);

		$languages = [ \App::getLocale() => '' ];
		foreach (\LaravelLocalization::getSupportedLocales() as $locale => $def)
		{
			$languages[$locale] = $def['native'];
		}

		return view('corporate.signup.step-site', compact('data','languages'));
	}
	public function postSite()
	{
		if ( !$this->_checkStep('pack') )
		{
			return redirect()->action('Corporate\SignupController@getPack');
		}

		$data = $this->request->input('site');

		if ( !$this->validateSite($data) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->back()->withInput()->withErrors($this->validation_error);
			}
			return redirect()->back()->withInput()->with('error', $this->validation_error);
		}

		$this->_setStep('site', $data);

		return redirect()->action('Corporate\SignupController@getPayment');
	}
	protected function validateSite($data)
	{
		$fields = [
			'subdomain' => 'required|alpha_dash|max:255|unique:sites,subdomain',
		];

		if ( session()->get("{$this->session_name}.plan.max_languages") == 1 )
		{
			$fields['language'] = 'required|in:'.implode(',', array_keys(\LaravelLocalization::getSupportedLocales()));
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			$this->validation_error = $validator;
			return false;
		}

		return true;
	}

	public function getPayment()
	{
		if ( !$this->_checkStep('site') )
		{
			return redirect()->action('Corporate\SignupController@getSite');
		}

		$data = session()->get($this->session_name);

		$plan = $this->_getStep('plan');

		if ( @$plan['is_free'] )
		{
			return $this->postPayment();
		}

		$paymethods = \App\Models\Plan::getPaymentOptions();

		return view('corporate.signup.step-payment', compact('data','plan','paymethods'));
	}
	public function postPayment()
	{
		if ( !$this->_checkStep('site') )
		{
			return redirect()->action('Corporate\SignupController@getSite');
		}

		$data = $this->request->input('payment');

		if ( !$this->validatePayment($data) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->back()->withInput()->withErrors($this->validation_error);
			}
			return redirect()->back()->withInput()->with('error', $this->validation_error);
		}

		if ( session()->get("{$this->session_name}.plan.is_free") )
		{
			$data = [ 'method' => 'none' ];
		}

		if ( @$data['method'] != 'transfer' )
		{
			$data['iban_account'] = '';
		}

		$this->_setStep('payment', $data);

		return redirect()->action('Corporate\SignupController@getInvoicing');
	}
	protected function validatePayment($data)
	{
		if ( session()->get("{$this->session_name}.plan.is_free") )
		{
			return true;
		}

		$fields = [
			'method' => 'required|in:'.implode(',', array_keys(\App\Models\Plan::getPaymentOptions()))
		];

		if ( @$data['method'] == 'transfer' )
		{
			$fields['iban_account'] = 'required';
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			$this->validation_error = $validator;
			return false;
		}

		return true;
	}

	public function getInvoicing()
	{
		if ( !$this->_checkStep('payment') )
		{
			return redirect()->action('Corporate\SignupController@getPayment');
		}

		$data = session()->get($this->session_name);

		$countries = \App\Models\Geography\Country::withTranslations()->orderBy('name')->lists('name','id')->all();

		return view('corporate.signup.step-invoicing', compact('data','countries'));
	}
	public function postInvoicing()
	{
		if ( !$this->_checkStep('payment') )
		{
			return redirect()->action('Corporate\SignupController@getPayment');
		}
		
		$data = $this->request->input('invoicing');

		if ( !$this->validateInvoicing($data) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->back()->withInput()->withErrors($this->validation_error);
			}
			return redirect()->back()->withInput()->with('error', $this->validation_error);
		}

		$country = \App\Models\Geography\Country::withTranslations()->findOrFail( $data['country_id'] );
		$data['country'] = $country->name;

		$this->_setStep('invoicing', $data);

		return redirect()->action('Corporate\SignupController@getConfirm');
	}
	protected function validateInvoicing($data)
	{
		$fields = [
			'type' => 'required|in:individual,company',
			'first_name' => 'required|string',
			'last_name' => 'required|string',
			'email' => 'required|email',
			'tax_id' => 'string',
			'street' => 'required|string',
			'zipcode' => 'string',
			'city' => 'required|string',
			'country_id' => 'required|exists:countries,id',
		];

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			$this->validation_error = $validator;
			return false;
		}

		// [TODO] Validate coupon
		if ( @$data['coupon'] )
		{
			$this->validation_error = trans('corporate/signup.invoicing.coupon.error');
			return false;
		}

		return true;
	}

	public function getConfirm()
	{
		if ( !$this->_checkStep('invoicing') )
		{
			return redirect()->action('Corporate\SignupController@getInvoicing');
		}

		$data = session()->get($this->session_name);

		return view('corporate.signup.step-confirm', compact('data'));
	}
	public function postConfirm()
	{
		$data = session()->get($this->session_name);

		// Validate groups
		if ( !$this->validateUser( @$data['user'] ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->action('Corporate\SignupController@getUser')->withInput()->withErrors($this->validation_error);
			}
			return redirect()->action('Corporate\SignupController@getUser')->withInput()->with('error', $this->validation_error);
		} 
		elseif ( !$this->validatePack( @$data['pack'] ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->action('Corporate\SignupController@getPack')->withInput()->withErrors($this->validation_error);
			}
			return redirect()->action('Corporate\SignupController@getPack')->withInput()->with('error', $this->validation_error);
		}
		elseif ( !$this->validateSite( @$data['site'] ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->action('Corporate\SignupController@getSite')->withInput()->withErrors($this->validation_error);
			}
			return redirect()->action('Corporate\SignupController@getSite')->withInput()->with('error', $this->validation_error);
		}
		elseif ( !$this->validatePayment( @$data['payment'] ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->action('Corporate\SignupController@getPayment')->withInput()->withErrors($this->validation_error);
			}
			return redirect()->action('Corporate\SignupController@getPayment')->withInput()->with('error', $this->validation_error);
		}
		elseif ( !$this->validateInvoicing( @$data['invoicing'] ) )
		{
			if ( gettype($this->validation_error) == 'object' )
			{
				return redirect()->action('Corporate\SignupController@getInvoicing')->withInput()->withErrors($this->validation_error);
			}
			return redirect()->action('Corporate\SignupController@getInvoicing')->withInput()->with('error', $this->validation_error);
		}

		// Get user
		if ( $data['user']['type'] == 'old' )
		{
			$user = \App\User::where('email', $data['user']['old']['email'])->first();
			if ( !$user )
			{
				return redirect()->back()->with('error', trans('general.messages.error'));
			}
		}
		// Or create new one
		else
		{
			$user = \App\User::create([
				'name' => $data['user']['new']['name'],
				'email' => $data['user']['new']['email'],
				'locale' => \LaravelLocalization::getCurrentLocale(),
				'password' => bcrypt($data['user']['new']['password']),
			]);
			if ( !$user )
			{
				return redirect()->back()->with('error', trans('general.messages.error'));
			}
			// With company role
			$role = \App\Models\Role::where('name', 'company')->first();
			if ( !$role )
			{
				return redirect()->back()->with('error', trans('general.messages.error'));
			}
			$user->roles()->attach( $role->id );
		}

		// Create site with free plan
		$free_plan = \App\Models\Plan::where('is_free',1)->first();
		$site = \App\Site::create([
			'subdomain' => $data['site']['subdomain'],
			'theme' => 'default',
			'plan_id' => $free_plan->id,
		]);
		if ( !$site )
		{
			return redirect()->back()->with('error', trans('general.messages.error'));
		}

		// Save owner
		$site->users()->attach($user->id);

		// Set plan type
		$plan_is_free = @$data['plan']['is_free'];

		// Save locales && title
		if ( $plan_is_free )
		{
			if ( empty($data['site']['language']) )
			{
				return redirect()->back()->with('error', trans('general.messages.error'));
			}

			$locales_codes = [ $data['site']['language'] ];
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
			$site->translateOrNew($locale)->title = $data['site']['subdomain'];
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
				'payment_interval' => $data['pack']['payment_interval'][$data['plan']['code']],
				'payment_method' => $data['payment']['method'],
				'old_data' => [
					'plan_id' => $site->plan_id ? $site->plan_id : $free_plan->id,
					'payment_interval' => $site->payment_interval,
					'payment_method' => $site->payment_method,
					'iban_account' => $site->iban_account,
				],
				'new_data' => [
					'plan_id' => $data['plan']['id'],
					'payment_interval' => $data['pack']['payment_interval'][$data['plan']['code']],
					'payment_method' => $data['payment']['method'],
					'iban_account' => @$data['payment']['iban_account'],
				],
				'invoicing' => $data['invoicing'],
				'locale' => \LaravelLocalization::getCurrentLocale(),
			]);
		}

		// Delete session
		session()->forget($this->session_name);

		// Send welcome email
		$job = ( new \App\Jobs\SendWelcomeEmail($site, $locale) )->onQueue('emails');
		$this->dispatch( $job );

		// Redirect to finish
		return redirect()->action('Corporate\SignupController@getFinish', [ $site->id, $site->subdomain ]);
	}

	public function getFinish($site_id,$site_subdomain)
	{
		$site = \App\Site::findOrFail($site_id);
		if ( $site->subdomain != $site_subdomain )
		{
			abort(404);
		}

		$data = $site->getSignupInfo();
		$data['site'] = $site;

		return view('corporate.signup.step-finish', $data);
	}

	protected function _checkStep($step)
	{
		return $this->_getStep($step) ? true : false;
	}
	protected function _setStep($step,$data)
	{
		return session()->put("{$this->session_name}.{$step}", $data);
	}
	protected function _getStep($step)
	{
		return session()->get("{$this->session_name}.{$step}");
	}

}
