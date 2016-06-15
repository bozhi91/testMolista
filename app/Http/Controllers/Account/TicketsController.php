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
		if ( !$this->request->get('limit') )
		{
			$this->request->merge([
				'limit' => \Config::get('app.pagination_perpage', 10),
			]);
		}

		$params = [
			'page' => $this->request->get('page',1),
			'limit' => $this->request->get('limit'),
			'orderby' => $this->request->get('orderby'),
			'order' => $this->request->get('order'),
		];

		if ( $this->request->get('status') )
		{
			$clean_filters = true;
			$params['status'] = $this->request->get('status');
		}

		if ( $this->request->get('user_id') == 'null' )
		{
			$clean_filters = true;
			$params['user_id'] = 'null';
		} 
		elseif ( \Auth::user()->hasRole('employee') )
		{
			if ( $this->request->get('user_id') ) 
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
		elseif ( $this->request->get('user_id') ) 
		{
			$clean_filters = true;
			$params['user_id'] = $this->request->get('user_id');
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
							->withTranslations()->orderBy('title')->lists('title','id')->all();
		return view('account.tickets.create', compact('employees','customers','properties'));
	}

	public function postCreate()
	{
		$validator = \Validator::make($this->request->all(), [
			'customer_id' => 'required|exists:customers,id,site_id,'.$this->site->id,
			'user_id' => 'exists:sites_users,user_id,site_id,'.$this->site->id,
			'property_id' => 'exists:properties,id,site_id,'.$this->site->id,
			'subject' => 'required|string',
			'body' => 'required|string',
		]);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$customer = $this->site->customers()->findOrFail( $this->request->get('customer_id') );

		$data = [
			'contact_id' => $customer->ticket_contact_id,
			'source' => 'backoffice',
			'subject' => $this->request->get('subject'),
			'body' => strip_tags( $this->request->get('body') ),
		];

		if ( $this->request->get('property_id') )
		{
			$property = $this->site->properties()->findOrFail( $this->request->get('property_id') );
			$data['item_id'] = $property->ticket_item_id;
		}

		if ( $this->request->get('user_id') )
		{
			$user = $this->site->users()->findOrFail( $this->request->get('user_id') );
			$data['user_id'] = $user->ticket_user_id;
		}

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
			$property = $this->site->properties()->where('ticket_item_id',$ticket->item->id)->withTranslations()->first();
		}

		return view('account.tickets.show', compact('ticket','property','employees','status'));
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

		$user = $this->site->users()->find($this->request->get('user_id'));

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
			'private' => 'numeric|in:0,1',
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

		$data['user_id'] = \Auth::user()->ticket_user_id;
		$data['source'] = 'backoffice';

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
			'status' => $this->request->get('status'),
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

		return $users_query->lists('name','id')->all();

	}

}