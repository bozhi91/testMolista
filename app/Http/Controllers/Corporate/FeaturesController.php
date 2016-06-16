<?php

namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class FeaturesController extends \App\Http\Controllers\CorporateController
{

	public function getIndex()
	{
		return view("corporate.features.features");
	}

}
