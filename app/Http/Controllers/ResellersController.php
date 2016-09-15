<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ResellersController extends Controller
{

	public function getIndex()
	{
		$query = $this->reseller->payments()
						->with('site')
						->with('infocurrency')
						;

		$request_order = $this->request->input('order', 'desc');
		switch ( $request_order )
		{
			case 'desc':
				$order = 'desc';
				break;
			default:
				$order = 'asc';
		}

		switch ( $this->request->input('orderby') )
		{
			case 'created':
			default:
				$query->orderBy('created_at', $order);
				break;
		}

		$commissions = $query->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		return view('resellers.index', compact('commissions'));
	}

}
