<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SiteController extends Controller
{

	public function getValidate($type)
	{
		$response = false;

		switch ($type)
		{
			case 'subdomain':
				$query = \App\Site::whereNotNull('id');
				if ( $this->request->get('subdomain') )
				{
					$query->where('subdomain', $this->request->get('subdomain'));
				}
				if ( $this->request->get('id') )
				{
					$query->where('id', '!=', $this->request->get('id'));
				}
				$response = $query->count() ? false : true;
				break;
			case 'domain':
				$query = \App\SiteDomains::whereNotNull('id');
				if ( $this->request->get('domain') )
				{
					$query->where('domain', $this->request->get('domain'));
				}
				if ( $this->request->get('id') )
				{
					$query->where('site_id', '!=', $this->request->get('id'));
				}
				$response = $query->count() ? false : true;
				break;
		}

		echo $response ? 'true' : 'false';
		exit;
	}

}
