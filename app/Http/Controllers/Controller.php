<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $auth;
	protected $request;

    public function __construct(Guard $auth, Request $request)
    {
		$this->auth = $auth;
		$this->request = $request;

        $this->__initialize();
    }
    
    public function __initialize() {}

    protected function set_go_back_link() 
    {
    	// Get stored values
    	$nav = session()->get('molista_nav', []);

    	// Add current value to nav
    	$nav[ url()->current() ] = url()->full();

    	// Sort nav by key length
    	uksort($nav, function($a,$b){
    		return ( strlen($a) > strlen($b) ) ? -1 : 1;
    	});

    	$nav = session()->put('molista_nav', $nav);
    }
}
