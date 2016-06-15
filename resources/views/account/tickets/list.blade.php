@if ( empty($tickets['items']) )
	<div class="alert alert-info">{{ Lang::get('account/tickets.empty') }}</div>


@else
	<div class="table-responsive tickets-list-container">
		<table class="table table-striped" style="font-size: 0.9em;">
			<thead>
				<tr>
					{!! drawSortableHeaders($pagination_url, [
						'reference' => [ 'title' => 'ID' ],
						'created_at' => [ 'title' => Lang::get('account/tickets.date') ],
						'contact.fullname' => [ 'title' => Lang::get('account/tickets.contact.name') ],
						'contact.email' => [ 'title' => Lang::get('account/tickets.contact.email') ],
						'user.name' => [ 'title' => Lang::get('account/tickets.assigned.to'), 'class'=>'text-nowrap' ],
						'referer' => [ 'title' => Lang::get('account/tickets.referer') ],
						'source' => [ 'title' => Lang::get('account/tickets.source') ],
						'messages.count' => [ 'title' => Lang::get('account/tickets.messages') ],
						'status' => [ 'title' => Lang::get('account/tickets.status') ],
						'action' => [ 'title' => '', 'sortable'=>false ],
					]) !!}
				</tr>
			</thead>
			<tbody>
				@foreach ($tickets['items'] as $ticket)
					<tr>
						<td>#{{ @$ticket->reference }}</td>
						<td>{{ date('d/m/Y', strtotime($ticket->created_at)) }}</td>
						<td>
							@if ( @$ticket->contact->email )
								<a href="{{ action('Account\CustomersController@show', urlencode($ticket->contact->email)) }}" target="_blank">{{ $ticket->contact->fullname }}</a>
							@else
								{{ $ticket->contact->fullname }}
							@endif
						</td>
						<td>{{ @$ticket->contact->email }}</td>
						<td>{{ @$ticket->user->name }}</td>
						<td>{{ $ticket->referer }}</td>
						<td>{{ @$ticket->source->name }}</td>
						<td class="text-center">{{ @number_format(count($ticket->messages), 0, ',', '.') }}</td>
						<td>{{ @$ticket->status->name }}</td>
						<td class="text-right">
							<a href="{{ action('Account\TicketsController@getShow', $ticket->id) }}" class="btn btn-primary btn-xs" {{ empty($ticket_list_target) ? '' : "target='{$ticket_list_target}'" }}>{{ Lang::get('general.view') }}</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	{!! drawTicketsPagination($pagination_url, $tickets) !!}


@endif
