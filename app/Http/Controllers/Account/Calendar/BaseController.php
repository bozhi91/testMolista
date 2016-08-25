<?php namespace App\Http\Controllers\Account\Calendar;

use Illuminate\Http\Request;

use App\Http\Requests;

class BaseController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		parent::__initialize();

		\View::share('submenu_section', 'calendar');
		\View::share('submenu_subsection', false);

		\View::share('fullcalendar_enabled', true);
	}

	public function getIndex()
	{
		$this->setViewValues();
		return view('account.calendar.index');
	}

	public function getCreate()
	{
		$start_time = date("Y-m-d H:00", time()+3600);
		if ( $this->request->input('calendar_defaultView') == 'agendaDay' && preg_match('#^\d{4}-\d{2}-\d{2}$#', $this->request->input('calendar_defaultDate')) )
		{
			$start_time = date("Y-m-d", strtotime($this->request->input('calendar_defaultDate'))) . ' '. date("H:00", time()+3600);
		}
		$end_time = date("Y-m-d H:00", strtotime($start_time)+3600);

		$defaults = (object) [
			'user_ids' => [ $this->site_user->id ],
			'start_time' => $start_time,
			'end_time' =>  $end_time,
		];

		$this->setViewValues();

		return view('account.calendar.create', compact('defaults'));
	}
	public function postCreate()
	{
		$data = array_merge($this->request->all(), [
			'site_id' => $this->site->id,
		]);

		$validator = \App\Models\Calendar::getCreateValidator($data, false, $this->_getCustomRules($data));
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Calendar::saveModel($data);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->to( $this->_getGoBackUrl() )->with('success', trans('general.messages.success.saved'));
	}

	public function getEvent($id_event)
	{
		$event = $this->site->events()->findOrFail($id_event);
		if ( !$this->site_user->hasRole('company') && $event->user_ids && !in_array($this->site_user->id, $event->user_ids) )
		{
			abort(404);
		}

		$this->setViewValues($event);

		return view('account.calendar.edit', compact('event'));
	}
	public function postEvent($id_event)
	{
		$event = $this->site->events()->findOrFail($id_event);
		if ( !$this->site_user->hasRole('company') && $event->user_ids && !in_array($this->site_user->id, $event->user_ids) )
		{
			abort(404);
		}

		$data = $this->request->all();

		$validator = \App\Models\Calendar::getUpdateValidator($data, $id_event, false, $this->_getCustomRules($data));
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$item = \App\Models\Calendar::saveModel($data, $id_event);
		if (!$item)
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		return redirect()->to( $this->_getGoBackUrl() )->with('success', trans('general.messages.success.saved'));
	}
	public function deleteEvent($id_event)
	{
		$event = $this->site->events()->findOrFail($id_event);

		$event->delete();

		\App\Models\Calendar::sendNotification('cancel',$event);

		return redirect()->action('Account\Calendar\BaseController@getIndex')->with('success', trans('account/calendar.delete'));
	}
	public function getEvents()
	{
		$response = [];

		$query = $this->site->events()->with('users')->with('property')->with('customer');

		if ( $this->request->input('start') )
		{
			$query->whereDate('start_time', '>=', $this->request->input('start'));
		}

		if ( $this->request->input('end') )
		{
			$query->whereDate('end_time', '<=', $this->request->input('end'));
		}

		if ( $this->request->input('agent') )
		{
			$query->ofUserId($this->request->input('agent'));
		}

		$events = $query->get();

		foreach ($events as $event)
		{
			$response[] = [
				'id' => $event->id,
				'title' => $event->title,
				'start' => $event->start_time->format("Y-m-d H:i:s"),
				'end' => $event->end_time->format("Y-m-d H:i:s"),
				'className' => "event-type-{$event->type}",
				'url' => action('Account\Calendar\BaseController@getEvent', $event->id),
				'editable' => false,
				'summary' => view('account.calendar.summary', compact('event'))->render(),
			];
		}

		return $response;
	}

	protected function _getCustomRules($data,$id=false)
	{
		$rules = [];

		// Property belongs to site
		if ( @$data['property_id'] )
		{
			$rules['property_id'] = 'required|exists:properties,id,site_id,'.$this->site->id;
		}

		// Customer belongs to site
		if ( @$data['customer_id'] )
		{
			$rules['customer_id'] = 'required|exists:customers,id,site_id,'.$this->site->id;
		}

		// Limit type options
		$rules['type'] = 'required|in:'.implode(',', array_keys(\App\Models\Calendar::getTypeOptions()));

		return $rules;
	}

	protected function _getGoBackUrl()
	{
		$params = [];

		if ( $this->request->input('calendar_defaultView') )
		{
			$params['calendar_defaultView'] = $this->request->input('calendar_defaultView');
		}

		if ( $this->request->input('calendar_defaultDate') )
		{
			$params['calendar_defaultDate'] = $this->request->input('calendar_defaultDate');
		}

		return action('Account\Calendar\BaseController@getIndex', $params);
	}
	protected function setViewValues($event=false)
	{
		\View::share('goback', $this->_getGoBackUrl());

		$query = $this->site->users();
		if ( $this->site_user->hasRole('employee') )
		{
			$user_ids = [ $this->site_user->id ];
			if ( $event )
			{
				$user_ids = array_merge($user_ids, $event->user_ids);
			}
			$query->whereIn('id', $user_ids);
		}
		\View::share('users', $query->orderBy('name')->lists('name','id')->all());

		$query = $this->site->properties()->withEverything();
		if ( !$this->site_user->pivot->can_view_all )
		{
			$property_ids = $this->site_user->properties()->lists('id')->all();
			if ( @$event->property_id )
			{
				$property_ids[] = $event->property_id;
			}
			$query->whereIn('properties.id', $property_ids);
		}
		\View::share('properties', $query->orderBy('ref')->orderBy('title')->get());

		$customers = $this->site->customers_options;
		\View::share('customers', $customers);

		$types = \App\Models\Calendar::getTypeOptions();
		\View::share('types', $types);
	}
}
