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

	public function getShow($ticket_id)
	{
		$ticket = $this->_getTicket($ticket_id);

		$employees = $this->site->users()->withRole('employee')->where('ticket_user_id','!=','')->orderBy('name')->lists('name','id');
		$status = $this->site->ticket_adm->getStatusOptions();

		return view('account.tickets.show', compact('ticket', 'employees','status'));
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

		$ticket_user_id = $this->site->users()->where('ticket_user_id','!=','')->where('id', $this->request->get('user_id'))->value('ticket_user_id');
		if ( !$ticket_user_id ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$ticket = $this->_getTicket($ticket_id);
		if ( !$ticket ) 
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$result = $this->site->ticket_adm->updateTicket($ticket_id, [
			'user_id' => $ticket_user_id
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

}
