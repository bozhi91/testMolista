<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CorporateController extends Controller
{

	public function index()
	{
		$response = view('corporate.index')->render();

		require_once app_path('Http/minifier.php');
		return minify_html($response);
	}

}
