@extends('layouts.account')

@section('account_content')

	<div id="account-tickets" class="show-ticket">

		@if ( empty($ticket) )
			<div class="alert alert-danger">{{ Lang::get('general.messages.error') }}</div>

		@else

	        @include('common.messages', [ 'dismissible'=>true ])

			<h1 class="page-title">
				{{ Lang::get('account/tickets.view.title') }} #{{ $ticket->reference }}
				@if ( @$property )
					- {{ $property->title }} ({{ $property->ref }})
				@endif
			</h1>

			<div class="row">

				<div class="col-xs-8">

					@if ( Auth::user()->hasRole('company') || ( $ticket->user && $ticket->user->id == Auth::user()->ticket_user_id ) )
						<div class="text-right reply-form-trigger-area">
							<a href="#" class="btn btn-sm btn-primary btn-reply-form-trigger">{{ Lang::get('account/tickets.send') }}</a>
						</div>
						{!! Form::open([ 'id'=>'reply-form', 'action'=>[ 'Account\TicketsController@postReply', $ticket->id ], 'class'=>'reply-form ticket-message' ]) !!}
							<div class="row hide">
								<div class="col-xs-12">
									<div class="form-group error-container">
										{!! Form::label('subject', Lang::get('account/tickets.subject')) !!}
										{!! Form::text('subject', "RE: {$ticket->subject}", [ 'class'=>'form-control required' ]) !!}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group error-container">
										{!! Form::label('body', Lang::get('account/tickets.body')) !!}
										{!! Form::textarea('body', null, [ 'class'=>'form-control required', 'rows'=>3 ]) !!}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="_form-group error-container">
										{!! Form::select('private', [
											'0' => Lang::get('account/tickets.public'),
											'1' => Lang::get('account/tickets.internal'),
										], null, [ 'class'=>'form-control input-sm required' ]) !!}
									</div>
								</div>
								<div class="col-xs-6">
									<div class="text-right">
										{!! Form::button(Lang::get('account/tickets.send'), [ 'type'=>'submit', 'class'=>'btn btn-sm btn-primary' ]) !!}
									</div>
								</div>
							</div>
						{!! Form::close() !!}
					@endif

					@foreach ($ticket->messages as $message)
						<div class="ticket-message">
							<div class="row">
								<div class="col-xs-12">
									@if ( $message->user )
										<span style="background-image: url('{{ asset('images/tickets/agent.png') }}');" class="pull-left author-icon" title="{{ $message->user->name }}"></span>
									@else
										<span style="background-image: url('{{ asset('images/tickets/customer.png') }}');" class="pull-left author-icon" title="{{ $ticket->contact->fullname }}"></span>
									@endif
									<div>
										<strong>{{ $message->subject }}</strong>
									</div>
									@if ( $message->private )
										<span class="privacy-label pull-right label label-info">{{ Lang::get('account/tickets.internal') }}</span>
									@else
										<span class="privacy-label pull-right label label-warning">{{ Lang::get('account/tickets.public') }}</span>
									@endif
									<div class="help-block">
										@if ( $message->user )
											<strong>{{ $message->user->name }}</strong>
										@else
											<strong>{{ $ticket->contact->fullname }}</strong>
										@endif
										-
										{{ since_text($message->created_at) }}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="message-body">
										{!! nl2p(strip_tags($message->body)) !!}
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>

				<div class="col-xs-4">

					@if ( !empty($ticket->contact->fullname) )
						<div class="panel panel-default">
							<div class="panel-heading">{{ Lang::get('account/tickets.contact.name')}}</div>
							<div class="panel-body">
								<div>{{ $ticket->contact->fullname }}</div>
								<div>{{ $ticket->contact->company }}</div>
								<div class="text-ellipsis" title="{{ $ticket->contact->email }}">{{ $ticket->contact->email }}</div>
								<div>{{ $ticket->contact->phone }}</div>
							</div>
						</div>
					@endif

					<div class="panel panel-default">
						<div class="panel-heading">{{ Lang::get('account/tickets.assigned.to') }}</div>
						<div class="panel-body">
							@if ( !empty($ticket->user->name) )
								<div>{{ $ticket->user->name }}</div>
								<div class="text-ellipsis" title="{{ $ticket->user->email }}">{{ $ticket->user->email }}</div>
							@endif
							@if ( empty($ticket->user->name) || \Auth::user()->hasRole('company') )
								@if ( !empty($ticket->user->name) )
									<hr />
								@endif
								{!! Form::open([ 'id'=>'assign-form', 'action'=>[ 'Account\TicketsController@postAssign', $ticket->id ] ]) !!}
									<div class="form-group error-container">
										@if ( $ticket->user )
											{!! Form::select('user_id', [ ''=>'' ] + $employees, null, [ 'class'=>'form-control input-sm required' ]) !!}
										@else
											{!! Form::select('user_id', [ ''=>Lang::get('account/tickets.unassigned') ] + $employees, null, [ 'class'=>'form-control input-sm required' ]) !!}
										@endif
									</div>
									<div class="text-right">
										@if ( $ticket->user )
											{!! Form::button(Lang::get('account/tickets.agents.change'), [ 'type'=>'submit', 'class'=>'btn btn-sm btn-default' ]) !!}
										@else
											{!! Form::button(Lang::get('account/tickets.assign'), [ 'type'=>'submit', 'class'=>'btn btn-sm btn-default' ]) !!}
										@endif
									</div>
								{!! Form::close() !!}
							@endif
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">{{ Lang::get('account/tickets.details') }}</div>
						<div class="panel-body">
							<div>{{ Lang::get('account/tickets.reference') }}: {{ $ticket->reference }}</div>
							@if ( $ticket->source )
								<div>{{ Lang::get('account/tickets.source') }}: {{ Lang::get("account/tickets.source.{$ticket->source->code}") }}</div>
							@endif
							@if ( $ticket->referer )
								<div>{{ Lang::get('account/tickets.referer') }}: {{ $ticket->referer }}</div>
							@endif
							<div>
								{{ Lang::get('account/tickets.status') }}: {{ Lang::get("account/tickets.status.{$ticket->status->code}") }} 
								<small class="cursor-pointer status-change-trigger">[{{ Lang::get('account/tickets.status.change') }}]</small>
							</div>
							{!! Form::open([ 'id'=>'status-form', 'action'=>[ 'Account\TicketsController@postStatus', $ticket->id ], 'class'=>'form-inline status-form' ]) !!}
								<hr />
								<div class="form-group">
									{!! Form::select('status', $status, $ticket->status->code, [ 'class'=>'form-control input-sm required' ]) !!}
								</div>
								{!! Form::button(Lang::get('account/tickets.status.change'), [ 'type'=>'submit', 'class'=>'btn btn-sm btn-default' ]) !!}
							{!! Form::close() !!}
						</div>
					</div>

					@if ( @$property )
						<div class="panel panel-default">
							<div class="panel-heading">{{ Lang::get('account/properties.column.title') }}</div>
							<div class="panel-body">
								<div>{{ Lang::get('account/properties.ref')}}: <a href="{{ $property->full_url}}" target="_blank">{{ $property->ref }}</a></div>
								<div>{{ $property->title }}</div>
								<div>{{ implode(', ', $property->location_array) }}</div>
							</div>
						</div>
					@endif

				</div>

			</div>

		@endif
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#account-tickets');

			var assign_form = $('#assign-form');
			assign_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			var status_form = $('#status-form');
			status_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});
			cont.on('click', '.status-change-trigger', function(e){
				e.preventDefault();
				$(this).hide();
				status_form.show();
			});

			var reply_form = $('#reply-form');
			reply_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});
			cont.on('click', '.btn-reply-form-trigger', function(e){
				e.preventDefault();
				cont.find('.reply-form-trigger-area').hide();
				reply_form.show();
			});

			if ( cont.find('.alert-error').length ) {
				cont.find('.btn-reply-form-trigger').trigger('click');
			}

			if ( cont.find('.alert-success').length ) {
				if ( typeof window.parent != 'object' ) {
					return;
				} 
				if ( typeof window.parent.TICKETS != 'object' ) {
					return;
				}
				if ( typeof window.parent.TICKETS.reload != 'function' ) {
					return;
				}
				window.parent.TICKETS.reload();
			}

		});
	</script>

@endsection
