<?php

namespace App\Http\Controllers\Admin\Utils;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LocaleController extends Controller
{

    public function getCheck($type=false)
	{
		$query = \App\Models\Locale::whereNotNull('id');

		switch ($type) 
		{
			default:
				$query->where($type, $this->request->get($type));
		}

		if ( $this->request->get('exclude') )
		{
			$query->where('id', '!=', $this->request->get('exclude'));
		}

		echo ( $query->count() < 1 ) ? 'true' : 'false';
	}

}
