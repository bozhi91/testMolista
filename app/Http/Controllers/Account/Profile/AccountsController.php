<?php namespace App\Http\Controllers\Account\Profile;

use Illuminate\Http\Request;

use App\Http\Requests;

class AccountsController extends \App\Http\Controllers\AccountController
{
	protected $accounts;

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'profile');
		\View::share('submenu_subsection', 'profile-accounts');

		$this->accounts = $this->site->ticket_adm->getEmailAccounts($this->site_user->ticket_user_id, $this->site_user->ticket_user_token);
		\View::share('accounts', $this->accounts);
	}

	public function getIndex()
	{
		return view('account.profile.accounts.index');
	}

	public function getCreate()
	{
		return view('account.profile.accounts.create');
	}
	public function postCreate()
	{
		$validator = \Validator::make($this->request->all(), $this->_getValidationFields());
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$data = $this->_prepareAccountData($this->request->all());

		if ( $id = $this->site->ticket_adm->saveEmailAccount($data) )
		{
			return redirect()->action('Account\Profile\AccountsController@getEdit', $id)->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	public function getEdit($id)
	{
		$account = $this->_getAccount($id);
		if ( !$account )
		{
			abort(404);
		}

		return view('account.profile.accounts.edit', compact('account'));
	}

	public function postEdit($id)
	{
		$account = $this->_getAccount($id);
		if ( !$account )
		{
			abort(404);
		}

		$validator = \Validator::make($this->request->all(), $this->_getValidationFields($id));
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$data = $this->_prepareAccountData($this->request->all(),$id);

		if ( $id = $this->site->ticket_adm->saveEmailAccount($data,$id) )
		{
			return redirect()->action('Account\Profile\AccountsController@getEdit', $id)->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	public function deleteRemove($id)
	{
		$account = $this->_getAccount($id);
		if ( !$account )
		{
			abort(404);
		}

		if ( $this->site->ticket_adm->deleteEmailAccount($id, $this->site_user->ticket_user_id, $this->site_user->ticket_user_token) )
		{
			return redirect()->action('Account\Profile\AccountsController@getIndex')->with('success', trans('account/profile.accounts.deleted'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	public function getTest($id)
	{
		$account = $this->_getAccount($id);
		if ( !$account )
		{
			return [ 'error' => true, ];
		}

		if ( $this->site->ticket_adm->testEmailAccount($id, $this->site_user->ticket_user_id, $this->site_user->ticket_user_token) )
		{
			return [ 'success' => true, ];
		}

		return [ 'error' => true, ];
	}

	protected function _getAccount($id)
	{
		foreach ($this->accounts as $account) 
		{
			if ( $account->id == $id )
			{
				return $account;
			}
		}

		return false;
	}

	protected function _prepareAccountData($data, $id=false)
	{
		$data = array_merge($this->request->all(), [
			'site_id' => $this->site->ticket_site_id,
			'user_id' => $this->site_user->ticket_user_id,
		]);

		if ( empty($data['password']) )
		{
			unset($data['password']);
		}

		return $data;
	}
	protected function _getValidationFields($id=false)
	{
		return [
			'protocol' => 'required',
			'from_name' => '',
			'from_email' => 'required|email',
			'host' => 'required',
			'username' => 'required',
			'password' => $id ? '' : 'required',
			'port' => 'required',
			'layer' => '',
		];
	}

}
