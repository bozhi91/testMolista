<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GeographyController extends Controller
{

	public function getSuggest($type)
	{
		switch ($type)
		{
			case 'state':
				$query = \App\Models\Geography\State::enabled();
				if ( $this->request->get('country_code') )
				{
					$query->withCountryCode( $this->request->get('country_code') );
				}
				if ( $this->request->get('country_id') )
				{
					$query->where('country_id', $this->request->get('country_id'));
				}
				if ( $this->request->get('site_id') )
				{
					$site_id = $this->request->get('site_id');
					$query->whereIn('states.id', function($query) use ($site_id) {
						$query->distinct()->select('state_id')->from('properties')->where('site_id', 1)->where('enabled', $site_id);
					});
				}
				return $query->select('states.id','states.name AS label','states.slug AS code')->get()->toArray();
			case 'city':
				$query = \App\Models\Geography\City::enabled();
				if ( $this->request->get('state_slug') )
				{
					$query->withStateSlug( $this->request->get('state_slug') );
				}
				if ( $this->request->get('state_id') )
				{
					$query->where('state_id', $this->request->get('state_id'));
				}
				if ( $this->request->get('site_id') )
				{
					$site_id = $this->request->get('site_id');
					$query->whereIn('cities.id', function($query) use ($site_id) {
						$query->distinct()->select('city_id')->from('properties')->where('site_id', 1)->where('enabled', $site_id);
					});
				}
				return $query->select('cities.id','cities.name AS label','cities.slug AS code')->get()->toArray();
		}

		return [];
	}

}
