<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PropertiesController extends Controller
{

    public function index()
    {
		return view('web.properties.index', compact('properties'));
    }

    public function details($slug)
    {
        return view('web.properties.details', compact('property'));
    }

}
