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
	public function getInfo()
	{
		return view('corporate.info.info');
	}
	public function getContact()
	{
		return view('corporate.info.contact');
	}
	public function postContact()
	{
		$validator = \Validator::make($this->request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'details' => 'required',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput()->with('contact_error',true);
		}

		\Mail::send('emails.corporate.contact', $this->request->only('name','email','phone','details'), function($message) {
			$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
			$message->subject( trans('corporate/general.contact.subject', [ 'webname'=>env('APP_URL') ]) );
			$message->to( env('MAIL_CONTACT') );
		});

		if ( count(\Mail::failures()) > 0 )
		{
			return redirect()->back()->with('error', trans('general.messages.error'))->withInput()->with('contact_error',true);
		}

		return redirect()->back()->with('success', trans('corporate/general.contact.success'))->with('contact_success',true);
	}

}
