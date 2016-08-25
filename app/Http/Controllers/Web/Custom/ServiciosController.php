<?php

namespace App\Http\Controllers\Web\Custom;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;

class ServiciosController extends WebController
{

	public function getIndex()
	{
		return view("web.custom.servicios.index");
	}

}
