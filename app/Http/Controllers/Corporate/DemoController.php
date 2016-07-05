<?php namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class DemoController extends \App\Http\Controllers\CorporateController
{

	public function getIndex()
	{
		return view('corporate.demo.index');
	}

}
