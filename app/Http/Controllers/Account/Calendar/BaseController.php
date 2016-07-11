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
		return view('account.calendar.index', compact('tickets','employees','clean_filters'));
	}

	public function getEvent($id_event)
	{
		echo "View event {$id_event}";
	}

	public function getEvents()
	{
		return [
			[
				'id' => 1,
				'title' => 'Event 1',
				'start' => '2016-07-14 12:00:00',
				'end' => '2016-07-14 13:00:00',
				'className' => 'event-type-visit',
				'url' => action('Account\Calendar\BaseController@getEvent', 1),
				'editable' => false
			],
			[
				'id' => 2,
				'title' => 'Event 2',
				'start' => '2016-07-14 14:00:00',
				'end' => '2016-07-14 16:30:00',
				'className' => 'event-type-catch',
				'url' => action('Account\Calendar\BaseController@getEvent', 2),
				'editable' => false
			],
			[
				'id' => 3,
				'title' => 'Event 3',
				'start' => '2016-07-14 16:00:00',
				'end' => '2016-07-14 17:00:00',
				'className' => 'event-type-interview',
				'url' => action('Account\Calendar\BaseController@getEvent', 3),
				'editable' => false
			],
		];
	}
}
