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

		$this->_setStep('pack', $this->request->input('pack'));

		return redirect()->action('Corporate\SignupController@getPack');
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
