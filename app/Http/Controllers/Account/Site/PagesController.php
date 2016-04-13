<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class PagesController extends \App\Http\Controllers\AccountController
{

    public function __initialize()
    {
        parent::__initialize();
    	\View::share('submenu_section', 'site');
    }

    public function index()
    {
		return view('account.site.pages.index');
    }

}
