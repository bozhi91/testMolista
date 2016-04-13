<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

// [TODO]
class ApiKeysController extends \App\Http\Controllers\AccountController
{
	protected $max_allowed;

	public function __initialize()
	{
		parent::__initialize();

		$this->max_allowed = \Config::get('app.apikeys_max_per_user', 10);

		\View::share('submenu_section', 'apikeys');
	}

	public function getIndex()
	{
		$query = $this->auth->user()->api_keys()->own();


		$api_keys = $query->orderBy('name')->get();

        $this->set_go_back_link();

        return view('account.apikeys.index', compact('api_keys'));    
	}

	/**
	 * Update / create API key process
	 *
	 * @return Response
	 */
	public function postStore($id=false) {
		// Validate request
		$validator = \Validator::make($this->request->all(), [
			'name' => [ 'string', 'required' ],
		]);
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator);
		}

		if ( $id )
		{
			$item = $this->auth->user()->api_keys()->own()->find( $id );
			if ($item) 
			{
				$item->update([
					'name' => $this->request->get('name'),
				]);
				return \Redirect::back()->with('status', \Lang::get('account/apikeys.success.update'));
			}
		}
		else
		{
			$total = $this->auth->user()->api_keys()->own()->count();
			if ( $total < $this->max_allowed )
			{
				$this->auth->user()->api_keys()->create([
					'name' => $this->request->get('name'),
					'key' => \App\Models\User\ApiKey::generateKey(),
					'created_by' => $this->auth->user()->id,
				]);
				return \Redirect::back()->with('status', \Lang::get('account/apikeys.success.create'));
			}
		}

		abort(404);
	}

	/**
	 * Delete API key process
	 *
	 * @return Response
	 */
	public function postDelete($id) {
		$item = $this->auth->user()->api_keys()->own()->find($id);

		if ($item) 
		{
			$item->delete();
			return \Redirect::back()->with('status', \Lang::get('account/apikeys.success.delete'));
		}

		abort(404);
	}

}
