<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CorporateController extends Controller
{

	public function index()
	{
        view()->share('deferred_css_styles', 1);
		return view('corporate.index');
	}

}
