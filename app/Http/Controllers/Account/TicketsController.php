<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class TicketsController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'tickets');
		\View::share('submenu_subsection', false);
	}

	public function getIndex()
	{
		if ( !$this->request->input('limit') )
		{
			$this->request->merge([
				'limit' => \Config::get('app.pagination_perpage', 10),
			]);
		}

		$params = [
			'page' => $this->request->input('page',1),
			'limit' => $this->request->input('limit'),
			'orderby' => $this->request->input('orderby'),
			'order' => $this->request->input('order'),
		];

		if ( $this->request->input('status') )
		{
			$clean_filters = true;
			$params['status'] = $this->request->input('status');
		}

		if ( $this->request->input('user_id') == 'null' )
		{
			$clean_filters = true;
			$params['user_id'] = 'null';
		} 
		elseif ( \Auth::user()->hasRole('employee') )
		{
			if ( $this->request->input('user_id') ) 
			{
				$clean_filters = true;
				$params['user_id'] = \Auth::user()->ticket_user_id;
			}
			else
			{
				$params['user_id'] = [
					\Auth::user()->ticket_user_id,
					'null',
				];
			}
		}
		elseif ( $this->request->input('user_id') ) 
		{
			$clean_filters = true;
			$params['user_id'] = $this->request->input('user_id');
		}

		$tickets = $this->site->ticket_adm->getTickets($params);

		if ( $this->request->ajax() )
		{
			$pagination_url = url()->full();
			return view('account.tickets.list', compact('pagination_url', 'tickets'));
		}

		$employees = $this->_getEmployeesOptions();

		return view('account.tickets.index', compact('tickets','employees','clean_filters'));
	}

	public function getCreate()
	{
		$employees = $this->_getEmployeesOptions();
		$customers = $this->site->customers_options;
		$properties = $this->site->properties()
							->whereIn('properties.id', $this->auth->user()->properties()->lists('id'))
							->orderBy('title')->lists('title','id')->all();
		$signatures = $this->site_user->sites_signatures()->ofSite($this->site->id)->orderBy('title')->get();
		return view('account.tickets.create', compact('employees','customers','properties','signatures'));
	}

	public function postCreate()
	{
		$data = $this->request->except(['_token']);

		$validator = \Validator::make($data, [
			'customer_id' => 'required|exists:customers,id,site_id,'.$this->site->id,
			'user_id' => 'exists:sites_users,user_id,site_id,'.$this->site->id,
			'property_id' => 'exists:properties,id,site_id,'.$this->site->id,
			'subject' => 'required|string',
			'body' => 'required|string',
			'cc' => 'array',
			'cc.*' => 'email',
			'bcc' => 'array',
			'bcc.*' => 'email',
			'signature_id' => "exists:sites_users_signatures,id,user_id,{$this->site_user->id},site_id,{$this->site->id}",
			'attachment' => 'max:' . \Config::get('app.property_image_maxsize', 2048),
		]);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->findOrFail( $this->request->input('customer_id') );

		$data['contact_id'] = $customer->ticket_contact_id;

		if ( $this->request->input('property_id') )
		{
			$property = $this->site->properties()->findOrFail( $this->request->input('property_id') );
			$data['item_id'] = $property->ticket_item_id;
		}

		if ( $this->request->input('user_id') )
		{
			$user = $this->site->users()->findOrFail( $this->request->input('user_id') );
			$data['user_id'] = $user->ticket_user_id;
		}

		$data = $this->_prepareMessageData($data);

		if ( $this->site->ticket_adm->createTicket($data) )
		{
			return redirect()->action('Account\TicketsController@getIndex')->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	public function getShow($ticket_id)
	{
		$ticket = $this->_getTicket($ticket_id);

		$employees = $this->_getEmployeesOptions();

		$status = $this->site->ticket_adm->getStatusOptions();

		if ( @$ticket->item->id )
		{
			$property = $this->site->properties()->where('ticket_item_id',$ticket->item->id)->first();
		}

		$signatures = $this->site_user->sites_signatures()->ofSite($this->site->id)->orderBy('title')->get();

		return view('account.tickets.show', compact('ticket','property','employees','status','signatures'));
	}

	public function postAssign($ticket_id)
	{
		$validator = \Validator::make($this->request->all(), [
			'user_id' => 'required|exists:sites_users,user_id,site_id,'.$this->site->id,
		]);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$user = $this->site->users()->find($this->request->input('user_id'));

		if ( !$user || !$user->ticket_user_id ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		if ( \Auth::user()->hasRole('employee') && \Auth::user()->id != $user->id )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$ticket = $this->_getTicket($ticket_id);
		if ( !$ticket ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$result = $this->site->ticket_adm->updateTicket($ticket_id, [
			'user_id' => $user->ticket_user_id
		]);

		if ( $result )
		{
			return redirect()->back()->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	public function postReply($ticket_id)
	{
		$data = $this->request->all();

		$validator = \Validator::make($data, [
			'subject' => 'required|string',
			'body' => 'required|string',
			'cc' => 'array',
			'cc.*' => 'email',
			'bcc' => 'array',
			'bcc.*' => 'email',
			'private' => 'numeric|in:0,1',
			'signature_id' => "exists:sites_users_signatures,id,user_id,{$this->site_user->id},site_id,{$this->site->id}",
			'attachment' => 'max:' . \Config::get('app.property_image_maxsize', 2048),
		]);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$ticket = $this->_getTicket($ticket_id);
		if ( !$ticket ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$data['user_id'] = $this->site_user->ticket_user_id;

		$data = $this->_prepareMessageData($data);

		$result = $this->site->ticket_adm->postMessage($ticket_id, $data);

		if ( $result )
		{
			return redirect()->back()->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	public function postStatus($ticket_id)
	{
		$validator = \Validator::make($this->request->all(), [
			'status' => 'required|in:'.implode(',',array_keys($this->site->ticket_adm->getStatusOptions())),
		]);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$ticket = $this->_getTicket($ticket_id);
		if ( !$ticket ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$result = $this->site->ticket_adm->updateTicket($ticket_id, [
			'status' => $this->request->input('status'),
		]);

		if ( $result )
		{
			return redirect()->back()->with('success', trans('general.messages.success.saved'));
		}

		return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
	}

	protected function _getTicket($ticket_id)
	{
		$ticket = $this->site->ticket_adm->getTicket($ticket_id);

		if ( !$ticket )
		{
			return false; false;
		}

		// Check ownership
		if ( $ticket->user && \Auth::user()->hasRole('employee') && $ticket->user->id != \Auth::user()->ticket_user_id )
		{
			return false;
		}

		return $ticket;
	}

	protected function _getEmployeesOptions()
	{
		$users_query = $this->site->users()
						->withRole('employee')
						->where('ticket_user_id','!=','')
						->orderBy('name')
						->orderBy('email');

		if ( \Auth::user()->hasRole('employee') )
		{
			$users_query->where('id', \Auth::user()->id);
		}

		return $users_query->lists('name','ticket_user_id')->all();

	}

	protected function _prepareMessageData($data)
	{
		// Set backoffice as source
		$data['source'] = 'backoffice';

		// Clean subject && body
		foreach (['subject','body'] as $field)
		{
			if ( !isset($data[$field]) )
			{
				continue;
			}

			$data[$field] = strip_tags($data[$field]);
		}


		// Clean email arrays
		foreach (['cc','bcc'] as $field)
		{
			if ( !isset($data[$field]) )
			{
				continue;
			}

			$data[$field] = array_values(array_unique($data[$field]));
		}

		// Default site signature
		$data['signature'] = false; //$this->site->ticket_adm->prepareSiteSignature($this->site_user->signature_parts, $this->site->signature_parts);

		// User signature
		if ( @$data['signature_id'] && $signature = \App\Models\Site\UserSignature::find($data['signature_id']) )
		{
			$data['signature'] = $signature->signature;
		}

		// Attachment
		$attachments = [];
		if ( $this->request->file('attachment') )
		{
			// Validate image
			$validator = \Validator::make($data, [
				'attachment' => 'required|max:' . \Config::get('app.property_image_maxsize', 2048),
			]);
			if ( !$validator->fails() ) 
			{
				$attach = [
					'site_id' => $this->site->id,
					'user_id' => $this->site_user->id,
					'title' => snake_case($this->request->file('attachment')->getClientOriginalName()),
					'filename' => str_random(40),
					'extension' => $this->request->file('attachment')->getClientOriginalExtension(),
					'folder' => "attachments/".date('Y/m/d'),
				];

				$i = 0;
				$attach_folder = public_path($attach['folder']);
				while ( file_exists("{$attach_folder}/{$attach['filename']}.{$attach['extension']}") )
				{
					$attach['filename'] = str_random(40);
					if ( $i > 100 )
					{
						\Log::error("Error creating ticket attachment: too many loops creating filename");
					}
				}
				$this->request->file('attachment')->move($attach_folder, "{$attach['filename']}.{$attach['extension']}");

				\App\Models\Attachment::create($attach);

				$attachments[] = [
					'url' => asset("{$attach['folder']}/{$attach['filename']}.{$attach['extension']}"),
					'title' => $attach['title'],
				];
			}
		}
		$data['attachments'] = $attachments;

		// Clean null values
		foreach (['contact_id','item_id','user_id'] as $field)
		{
			if ( isset($data[$field]) && $data[$field] )
			{
				continue;
			}

			unset($data[$field]);
		}
		return $data;
	}

}
