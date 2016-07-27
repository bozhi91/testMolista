@extends('emails.calendar.notify')

@section('content')

	<p><strong>{{ $event->title }}</strong></p>

	@if ( $event->comments )
		{!! nl2p($event->comments) !!}
	@endif

	<table>
		<tr>
			<td class="label">{{ Lang::get('account/calendar.email.when') }}</td>
			<td>{{ $event->start_time->format("d/m/Y H:i") }} ({{ $event->site->timezone }})</td>
		</tr>
		@if ( $event->location )
			<tr>
				<td class="label">{{ Lang::get('account/calendar.email.where') }}</td>
				<td>{{ $event->location }}</td>
			</tr>
		@endif
		<tr>
			<td class="label">{{ Lang::get('account/calendar.email.who') }}</td>
			<td>{{ $event->user->name }} ({{ $event->user->email }})</td>
		</tr>
	</table>

@endsection
