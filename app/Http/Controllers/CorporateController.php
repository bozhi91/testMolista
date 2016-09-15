<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CorporateController extends Controller
{

	public function index()
	{
		$marketplaces = \App\Models\Marketplace::enabled()->with('countries')->orderBy('name', 'asc')->get();

		$response = view('corporate.index', compact('marketplaces'))->render();

		require_once app_path('Http/minifier.php');
		return minify_html($response);
	}

}
