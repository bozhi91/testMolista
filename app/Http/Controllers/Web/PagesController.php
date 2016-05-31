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

		// If slug is from another language, redirect
		if ( $slug != $page->slug )
		{
			return redirect()->to(action('Web\PagesController@show', $page->slug), 301); 
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
				return $this->postContact($page);
		}

		redirect()->action('Web\PagesController@show', $page->slug);
	}

	protected function postContact($page)
	{
		$validator = \Validator::make($this->request->all(), [
			'name' => 'required|string',
			'email' => 'required|email',
			'phone' => 'string',
			'interest' => 'required|in:buy,rent,sell',
			'body' => 'required|string',
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->where('email',$this->request->get('email'))->first();
		if ( !$customer )
		{
			$fullname = explode(" ",$this->request->get('name'),2);
			$customer = $this->site->customers()->create([
				'locale' => \LaravelLocalization::getCurrentLocale(),
				'first_name' => $fullname[0],
				'last_name' => $fullname[0],
				'email' => $this->request->get('email'),
				'phone' => $this->request->get('phone'),
			]);
			if ( !$customer )
			{
				return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
			}
		}

		$data = $this->request->only('name','email','phone','interest','body');

		if ( $this->site->ticketing_enabled )
		{
			$data['email_content_only'] = true;

			$res = $this->site->ticket_adm->createTicket([
				'contact_id' => $customer->ticket_contact_id,
				'source' => 'web',
				'subject' => trans('web/pages.contact.email.subject'),
				'body' => view('web.pages.email', $data)->render(),
			]);
		}
		else
		{
			$content = view('web.pages.email', $data)->render();
			$result = $this->site->sendEmail([
				'to' => $page->configuration['contact']['email'],
				'subject' => trans('web/pages.contact.email.subject'),
				'content' => $content,
			]);
		}

		return redirect()->back()->with('success', trans('web/pages.contact.email.sent'));
	}


}
