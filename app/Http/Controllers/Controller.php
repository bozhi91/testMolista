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

	protected $site;
	protected $site_user;

	public function __construct(Guard $auth, Request $request)
	{
		$this->auth = $auth;
		$this->request = $request;

		$this->site = $this->request->get('site');
		$this->site_user = $this->request->get('site_user');

		$this->__initialize();
	}

	public function __initialize() {}

	protected function set_go_back_link() 
	{
		// Get stored values
		$nav = session()->get('SmartBackLinks', []);

		// Add current value to nav
		$nav[ url()->current() ] = url()->full();

		// Sort nav by key length
		uksort($nav, function($a,$b){
		return ( strlen($a) > strlen($b) ) ? -1 : 1;
		});

		$nav = session()->put('SmartBackLinks', $nav);
	}

	protected function set_seo_values($seo=false) 
	{
		if ( !$seo || !is_array($seo) )
		{
			return false;
		}

		// Prepare description
		$description = @str_replace("\n", " ", strip_tags($seo['description']) );
		if ( strlen($description) > 150 )
		{
			$parts = array_filter( explode(' ', $description) );

			$length = 0;
			$description = "";

			foreach ($parts as $part)
			{
				$description .= " {$part}";
				if ( strlen($description) > 150 )
				{
					$description = trim($description).'...';
					break;
				}
			}
		}

		\View::share('seo_title', @$seo['title']);
		\View::share('seo_description', $description);
		\View::share('seo_keywords', @$seo['keywords']);

		return true;
	}

}
