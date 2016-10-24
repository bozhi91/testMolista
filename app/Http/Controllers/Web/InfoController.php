<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;

class InfoController extends \App\Http\Controllers\WebController
{

	public function getLegal()
	{
		return view('web.info.legal');
	}

}
