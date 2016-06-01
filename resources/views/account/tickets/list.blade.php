@if ( empty($tickets['items']) )
	<div class="alert alert-info">{{ Lang::get('account/tickets.empty') }}</div>


@else
	<table class="table table-striped">
		<thead>
			<tr>
				<th>{{ Lang::get('account/tickets.date') }}</th>
				<th>{{ Lang::get('account/tickets.contact.name') }}</th>
				<th>{{ Lang::get('account/tickets.contact.email') }}</th>
				<th class="text-nowrap">{{ Lang::get('account/tickets.assigned.to') }}</th>
				<th>{{ Lang::get('account/tickets.referer') }}</th>
				<th>{{ Lang::get('account/tickets.source') }}</th>
				<th class="text-center">{{ Lang::get('account/tickets.messages') }}</th>
				<th>{{ Lang::get("account/tickets.status") }}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($tickets['items'] as $ticket)
				<tr>
					<td>{{ date('d/m/Y', strtotime($ticket->created_at)) }}</td>
					<td>{{ @$ticket->contact->fullname }}</td>
					<td>{{ @$ticket->contact->email }}</td>
					<td>{{ @$ticket->user->name }}</td>
					<td>{{ $ticket->referer }}</td>
					<td>{{ Lang::get("account/tickets.source.{$ticket->source->code}") }}</td>
					<td class="text-center">{{ @number_format(count($ticket->messages), 0, ',', '.') }}</td>
					<td>{{ Lang::get("account/tickets.status.{$ticket->status->code}") }}</td>
					<td><a href="#" data-href="{{ action('Account\TicketsController@getShow', $ticket->id) }}" class="btn btn-primary btn-xs edit-ticket-trigger">{{ Lang::get('general.view') }}</a>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! drawTicketsPagination($pagination_url, $tickets) !!}


@endif
