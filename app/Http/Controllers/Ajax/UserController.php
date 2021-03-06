<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

	public function getValidate($type)
	{
		$response = false;

		switch ($type)
		{
			case 'email':
				$query = \App\User::whereNotNull('id');
				if ( $this->request->input('email') )
				{
					$query->where('email', $this->request->input('email'));
				}
				if ( $this->request->input('not_employee') )
				{
					$query->withoutRole('employee');
				}
				if ( $this->request->input('id') )
				{
					$query->where('id', '!=', $this->request->input('id'));
				}
				$response = $query->count() ? false : true;
				break;
		}

		echo $response ? 'true' : 'false';
		exit;
	}

}
