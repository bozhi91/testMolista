<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class LandingController extends \App\Http\Controllers\CorporateController
{
	public function getIndex()
	{
		return view('corporate.landing.index');
	}
}
