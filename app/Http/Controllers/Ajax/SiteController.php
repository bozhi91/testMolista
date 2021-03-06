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
				if ( ! $this->isInvalidSubdomain($this->request->input('subdomain')) )
				{
					echo 'false';
					exit;
				}

				$query = \App\Site::whereNotNull('id');
				if ( $this->request->input('subdomain') )
				{
					$query->where('subdomain', $this->request->input('subdomain'));
				}
				if ( $this->request->input('id') )
				{
					$query->where('id', '!=', $this->request->input('id'));
				}
				$response = $query->count() ? false : true;
				break;
			case 'domain':
				$query = \App\SiteDomains::whereNotNull('id');
				if ( $this->request->input('domain') )
				{
					$query->where('domain', $this->request->input('domain'));
				}
				if ( $this->request->input('id') )
				{
					$query->where('site_id', '!=', $this->request->input('id'));
				}
				$response = $query->count() ? false : true;
				break;
		}

		echo $response ? 'true' : 'false';
		exit;
	}

	protected function isInvalidSubdomain($subdomain = false)
	{
		if ( ! $subdomain = trim($subdomain) )
		{
			return true;
		}

		foreach (\App\Site::getInvalidSubdomains() as $invalid)
		{
			if ( $invalid == $subdomain )
			{
				return false;
			}
		}

		return true;
	}

}
