<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WebController extends Controller
{

	public function index()
	{
		$properties = \App\Property::enabled()->highlighted()->with('images')->with('state')->with('city')->orderByRaw("RAND()")->get();

		$states = \App\Models\Geography\State::enabled()->orderBy('name')->lists('name','slug');

		$modes = \App\Property::getModeOptions();
		$types = \App\Property::getTypeOptions();

		return view('web.index', compact('properties','modes','states','types'));
	}

}
