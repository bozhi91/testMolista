<?php

namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;
use App\Http\Requests;

class DistribuitorController extends \App\Http\Controllers\CorporateController {

	public function getIndex() {
		return view('corporate.distribuitor.index');
	}

	public function postContact() {	
		$validator = \Validator::make($this->request->all(), [
					'name' => 'required',
					'company' => 'required',
					'email' => 'required|email',
					'phone' => 'required',
					'workers' => 'required',
					'mensaje' => 'required',
		]);
		
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)
					->withInput()->with('distribuitors_error', true);
		}
				
		$params = $this->request->only('name','company','email','phone', 'workers', 'mensaje');
						
		\Mail::send('emails.corporate.distribuitor_contact', $params, function($message) {
			$message->from(env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME'));
			$message->subject(trans('corporate/general.contact.subject', [ 'webname' => env('APP_URL')]));
			$message->to(env('MAIL_CONTACT'));
		});

		if (count(\Mail::failures()) > 0) {
			return redirect()->back()->with('error', trans('general.messages.error'))
					->withInput()->with('distribuitors_error', true);
		}

		return redirect()->back()->with('success', trans('corporate/general.contact.success'))
				->with('distribuitors_success', true);
	}

}
