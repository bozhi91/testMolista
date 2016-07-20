<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class PricingController extends \App\Http\Controllers\CorporateController
{

	public function getIndex()
	{
		$plans = \App\Models\Plan::getEnabled( \App\Session\Currency::get('code') );
		return view('corporate.pricing.index', compact('plans'));
	}

}
