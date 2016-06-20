<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class SignupController extends \App\Http\Controllers\CorporateController
{
	protected $session_name = 'signup_form';

	public function getIndex()
	{
		session()->flush($this->session_name);
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
		switch ( $this->request->input('user.type') )
		{
			case 'new':
				$fields = [
					'name' => 'required|string',
					'email' => 'required|email|unique:users,email',
					'password' => 'required|string',
					'accept' => 'required|accepted',
				];
				break;
			case 'old':
				$fields = [
					'email' => 'required|email|exists:users,email',
					'password' => 'required|string|min:6|max:20',
				];
				break;
			default:
				return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
		}

		$data = $this->request->input("user.{$this->request->input('user.type')}");

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// If old, check user role and password
		if ( $this->request->input('user.type') == 'old' )	
		{
			$user = \App\User::where('email', $data['email'])->first();
			if ( !$user )
			{
				return redirect()->back()->withInput()->with('error',trans('corporate/signup.user.old.error.combination'));
			}
			$credentials = \Auth::validate([
				'email' => $data['email'],
				'password' => $data['password'],
			]);
			if ( !$credentials )
			{
				return redirect()->back()->withInput()->with('error',trans('corporate/signup.user.old.error.combination'));
			}
			if ( !$user->hasRole('company') )
			{
				if ( $user->hasRole('employee') )
				{
					return redirect()->back()->withInput()->with('error',trans('corporate/signup.user.old.error.employee'));
				}
				if ( $user->hasRole('admin') )
				{
					return redirect()->back()->withInput()->with('error',trans('corporate/signup.user.old.error.admin'));
				}
				return redirect()->back()->withInput()->with('error',trans('general.messages.error'));
			}
		}

		$this->_setStep('user', $this->request->input('user'));

		return redirect()->action('Corporate\SignupController@getPack');
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

		$plans = \App\Models\Plan::getEnabled();

		$fields = [
			'selected' => 'required|in:'.$plans->implode('code',','),
		];
		foreach ($plans as $plan)
		{
			$fields["payment_interval.{$plan->code}"] = "required_if:selected,{$plan->code}|in:year,month";
		}

		$data = $this->request->input('pack');

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$this->_setStep('pack',$data);

		$this->_setStep('plan', $plans->where('code', $this->request->input('pack.selected'))->first()->toArray());

		return redirect()->action('Corporate\SignupController@getSite');
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

		$fields = [
			'subdomain' => 'required|alpha_dash|max:255|unique:sites,subdomain',
		];

		$data = $this->request->input('site');

		if ( @$data['plan']['max_languages'] == 1 )
		{
			$fields['language'] = 'required|in:'.implode(',', array_keys(\LaravelLocalization::getSupportedLocales()));
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$this->_setStep('site', $data);

		return redirect()->action('Corporate\SignupController@getPayment');
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

		$plan = $this->_getStep('plan');

		if ( empty($plan['is_free']) )
		{
			$data = $this->request->input('payment');

			$fields['method'] = 'required|in:'.implode(',', array_keys(\App\Models\Plan::getPaymentOptions()));

			$validator = \Validator::make($data, $fields);
			if ( $validator->fails() ) 
			{
				return redirect()->back()->withInput()->withErrors($validator);
			}
		}
		else
		{
			$data = [ 'method' => 'none' ];
		}

		$this->_setStep('payment', $data);

		return redirect()->action('Corporate\SignupController@getInvoicing');
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
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// [TODO] Validate coupon
		if ( $this->request->input('invoicing.coupon') )
		{
			return redirect()->back()->withInput()->with('error', trans('corporate/signup.invoicing.coupon.error'));
		}

		$country = \App\Models\Geography\Country::withTranslations()->findOrFail( $data['country_id'] );
		$data['country'] = $country->name;

		$this->_setStep('invoicing', $data);

		return redirect()->action('Corporate\SignupController@getConfirm');
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
die('postConfirm');
		if ( !$this->_checkStep('site') )
		{
			return redirect()->action('Corporate\SignupController@getSite');
		}

		$fields = [
			'subdomain' => 'required|alpha_dash|max:255|unique:sites,subdomain',
		];

		$data = $this->request->input('site');

		if ( @$data['plan']['max_languages'] == 1 )
		{
			$fields['language'] = 'required|in:'.implode(',', array_keys(\LaravelLocalization::getSupportedLocales()));
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$this->_setStep('site', $this->request->input('site'));

		return redirect()->action('Corporate\SignupController@getSite');
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
