<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\ValidatorTrait;

class Calendar extends Model
{
	use SoftDeletes;
	use ValidatorTrait;

	protected $guarded = [];

	protected $casts = [
		'data' => 'array',
	];

	protected $dates = ['deleted_at','start_time','end_time'];

	protected static $create_validator_fields = [
		'user_id' => 'required|exists:users,id',
		'site_id' => 'required|exists:sites,id',
		'property_id' => 'exists:properties,id',
		'customer_id' => 'exists:customers,id',
		'type' => 'required',
		'status' => '',
		'title' => 'required|string',
		'comments' => 'string',
		'location' => 'string',
		'data' => 'array',
		'start_time' => 'required|date_format:"Y-m-d H:i"',
		'end_time' => 'required|date_format:"Y-m-d H:i"',
	];

	protected static $update_validator_fields = [
		'user_id' => 'required|exists:users,id',
		'property_id' => 'exists:properties,id',
		'customer_id' => 'exists:customers,id',
		'type' => 'required',
		'status' => '',
		'title' => 'required|string',
		'comments' => 'string',
		'location' => 'string',
		'data' => 'array',
		'start_time' => 'required|date_format:"Y-m-d H:i"',
		'end_time' => 'required|date_format:"Y-m-d H:i"',
	];

	protected static $types = [
		'visit',
		'catch',
		'interview',
		'other',
	];

	protected static $calendar_id = '-//Molista//Molista Calendar//EN';

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function property()
	{
		return $this->belongsTo('App\Property')->with('infocurrency')->withTranslations();
	}

	public function customer()
	{
		return $this->belongsTo('App\Models\Site\Customer');
	}

	public static function saveModel($data, $id = null)
	{
		$update_notification = false;

		if ($id)
		{
			$item = $old_item = self::find($id);
			if (!$item)
			{
				return false;
			}
			$fields = array_keys(self::$update_validator_fields);
		}
		else
		{
			$item = new \App\Models\Calendar;
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field) 
			{
				case 'user_id':
				case 'site_id':
				case 'property_id':
				case 'customer_id':
					$value = empty($data[$field]) ? null : @$data[$field];
					break;
				case 'start_time':
				case 'end_time':
					$value = empty($data[$field]) ? '' : $data[$field] . ':00';
					break;
				case 'title':
				case 'comments':
					$value = empty($data[$field]) ? '' : sanitize($data[$field]);
					break;
				default:
					$value = @$data[$field];
			}

			if ( $id && @$old_item->$field != $value )
			{
				$update_notification = true;
			}

			$item->$field = $value;
		}

		$item->save();

		if ( !$id )
		{
			self::sendNotification('create',$item);
		}
		elseif ( $update_notification )
		{
			self::sendNotification('update',$item);
		}

		return $item;
	}

	public static function createCalendarEvent($item)
	{
		// Create calendar object
		$vCalendar = new \Eluceo\iCal\Component\Calendar(self::$calendar_id);
		// Create event object
		$vEvent = new \Eluceo\iCal\Component\Event();
		// Add information to the event
		$vEvent
			->setUniqueId( "calendar-event-{$item->id}@molista.com" )
			->setUseTimezone( $item->site->timezone )
			->setCreated( new \DateTime( $item->created_at->toAtomString()) )
			->setModified( new \DateTime( $item->updated_at->toAtomString()) )
			->setDtStart( new \DateTime( $item->start_time->toAtomString()) )
			->setDtEnd(new \DateTime(  $item->end_time->toAtomString()) )
			->setSummary( $item->title )
			->setDescription( $item->comments )
			;

		if ( $item->user )
		{
			$vEvent->setOrganizer(new \Eluceo\iCal\Property\Event\Organizer("mailto:{$item->user->email}", [ 
				'CN' => $item->user->name,
			]));
			$vEvent->addAttendee("mailto:{$item->user->email}", [ 
				'CN' => $item->user->name,
			]);
		}

		if ( $item->customer )
		{
			$vEvent->addAttendee("mailto:{$item->customer->email}", [ 
				'CN' => $item->customer->full_name,
			]);
		}

		if ( $item->location )
		{
			$vEvent->setLocation( $item->location );
		}

		if ( $item->deleted_at )
		{
			$vEvent->setCancelled( true );
		}

		// Add event to calendar
		$vCalendar->addComponent($vEvent);


		$tmp_folder = storage_path("app/tmp");
		if ( !is_dir($tmp_folder) )
		{
			\File::makeDirectory($tmp_folder, 0775, true);
		}

		$tmp_file = "{$tmp_folder}/_event_{$item->id}.ics";
		if ( \File::put($tmp_file, $vCalendar->render()) )
		{
			return $tmp_file;
		}

		return false;
	}

	public static function sendNotification($type,$event)
	{
		// Site has custom mailer?
		$site = $event->site;
		if ( @$site->mailer['service'] != 'custom' )
		{
			return false;
		}

		// Customer exists and has email?
		$user = $event->user;
		$customer = $event->customer;
		if ( !$user && !$customer )
		{
			return false;
		}

		switch ( $type )
		{
			case 'create':
			case 'update':
			case 'cancel':
				break;
			default:
				return false;
		}

		$filepath = self::createCalendarEvent($event);

		$subject = trans("account/calendar.email.title.{$type}", [
			'title' => $event->title,
		]);

		$content = view("emails.calendar.notify-{$type}", [
			'event' => $event,
		])->render();

		$css_path = base_path('resources/assets/css/emails/calendar.css');
		if ( file_exists($css_path) )
		{
			$emogrifier = new \Pelago\Emogrifier($content, file_get_contents($css_path));
			$content = $emogrifier->emogrify();
		}

		foreach ([$user, $customer] as $to)
		{
			if ( @$to->email )
			{
				$sent = $event->site->sendEmail([
					'to' => $to->email,
					'subject' => $subject,
					'content' => $content,
					'attachments' => [
						$filepath => [ 'as'=>"event.ics" ],
					]
				]);
			}
		}

		// Delete ics file
		\File::delete($filepath);

		return $sent;
	}

	public function scopeWithStatus($query, $status)
	{
		return $query->where("{$this->getTable()}.status", $status);
	}

	public static function getTypeOptions()
	{
		$types = [];

		foreach (self::$types as $type)
		{
			$types[$type] = trans("account/calendar.reference.type.{$type}");
		}

		return $types;
	}

}
