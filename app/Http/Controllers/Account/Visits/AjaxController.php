<?php namespace App\Http\Controllers\Account\Visits;

use Illuminate\Http\Request;

use App\Http\Requests;

class AjaxController extends \App\Http\Controllers\AccountController
{
	public function getTab()
	{
		// Only ajax requests
		if ( !$this->request->ajax() )
		{
			return redirect()->action('AccountController@index');
		}

		$query =  $this->site->events()->where('type','visit');

		if ( $this->request->input('customer_id') )
		{
			$query->where('customer_id', $this->request->input('customer_id'));
		}

		if ( $this->request->input('user_id') )
		{
			$query->ofUserId($this->request->input('user_id'));
		}

		if ( $this->request->input('property_id') )
		{
			$query->ofPropertyId($this->request->input('property_id'));
		}

		$visits = $query->orderBy('start_time','desc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		return [
			'success' => 1,
			'html' => view('account.visits.ajax-tab', compact('visits'))->render(),
		];
	}

}
