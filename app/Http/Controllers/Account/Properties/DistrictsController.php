<?php

namespace App\Http\Controllers\Account\Properties;

use Illuminate\Http\Request;
use App\Http\Requests;

class DistrictsController extends \App\Http\Controllers\AccountController {

	public function __initialize() {
		parent::__initialize();
		\View::share('submenu_section', 'properties');
	}

	public function getIndex() {
		
		
		
		//dd($this->site->districts()->get());
		
		return view('account.properties.districts.index');
	}

	
	public function getCreate(){
		
	}
}
