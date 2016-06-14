<?php

namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class InfoController extends \App\Http\Controllers\CorporateController
{

	public function getLegal()
	{
		return view('corporate.info.legal');
	}

	public function getPrivacy()
	{
		return view('corporate.info.privacy');
	}

	public function getContact()
	{
		return view('corporate.info.contact');
	}
	public function postContact()
	{
        $validator = \Validator::make($this->request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

		echo '<pre>';
		print_r($this->request->all());
		echo '</pre>';
	}

}
