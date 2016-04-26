<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;

class PagesController extends WebController
{

	public function show($slug)
	{
		$page = $this->site->pages()->withTranslations()->enabled()->whereTranslation('slug',$slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		$this->set_seo_values([
			'title' => $page->seo_title ? $page->seo_title : $page->title,
			'description' => $page->seo_description ? $page->seo_description : str_replace("\n", ' ', strip_tags($page->body)),
			'keywords' => $page->seo_keywords,
		]);

		return view("web.pages.{$page->type}", compact('page'));
	}

	public function post($slug)
	{
		$page = $this->site->pages()->withTranslations()->enabled()->whereTranslation('slug',$slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		switch ( $page->type )
		{
			case 'contact':
				$validator = \Validator::make($this->request->all(), [
					'name' => 'required|string',
					'email' => 'required|email',
					'body' => 'required|string',
				]);
				if ($validator->fails()) 
				{
					return redirect()->back()->withInput()->withErrors($validator);
				}

				$result = \Mail::send('web.pages.email', $this->request->only('name','email','body'), function($message) use ($page)
				{
					$message->from( env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME') );
					$message->subject( \Lang::get('pages.contact.email.subject') );
					$message->to($page->configuration['contact']['email']);
				});

				return redirect()->back()->with('success', trans('web/pages.contact.email.sent'));
		}

		redirect()->action('Web\PagesController@show', $page->slug);
	}


}
