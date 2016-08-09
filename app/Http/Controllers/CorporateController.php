<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CorporateController extends Controller
{

	public function index()
	{
		return view('corporate.index');
	}

}
