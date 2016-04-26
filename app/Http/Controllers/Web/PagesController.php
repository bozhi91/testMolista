<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;

class PagesController extends WebController
{

	public function show($slug)
	{
		$page = $this->site->pages()->withTranslations()->enabled()->whereTranslation('slug',$slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		return view('web.pages.show', compact('page'));
	}

}
