<div id="event-summary">

	<div class="title">{{ $event->title }}</div>

	@if ( $event->comments )
		<div class="comments">{!! nl2br($event->comments) !!}</div>
	@endif

	<ul class="details">
		<li>{{ Lang::get('account/calendar.start') }}: {{ $event->start_time->format("d/m/Y H:i") }}</li>
		<li>{{ Lang::get('account/calendar.end') }}: {{ $event->end_time->format("d/m/Y H:i") }}</li>
		<li>{{ Lang::get('account/calendar.type') }}: {{ Lang::get("account/calendar.reference.type.{$event->type}") }}</li>
		<li>{{ Lang::get('account/calendar.agent') }}: {{ @$event->users->implode('name',', ') }}</li>
		@if ( @$event->property->title )
			<li>{{ Lang::get('account/calendar.property') }}: {{ @$event->property->title }}</li>
		@endif
		@if ( @$event->customer->name )
			<li>{{ Lang::get('account/calendar.customer') }}: {{ @$event->customer->full_name }}</li>
		@endif
	</ul>

	<div class="more-info-area text-right">
		<a href="{{ action('Account\Calendar\BaseController@getEvent', $event->id) }}" class="btn btn-sm btn-primary">{{ Lang::get('account/calendar.moreinfo') }}</a>
	</div>

</div>
