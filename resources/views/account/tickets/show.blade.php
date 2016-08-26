@extends('layouts.account')

@section('account_content')

	<div id="account-tickets" class="show-ticket">

		@if ( empty($ticket) )
			<div class="alert alert-danger">{{ Lang::get('general.messages.error') }}</div>

		@else

	        @include('common.messages', [ 'dismissible'=>true ])

			<div class="pull-right hidden-xs">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-primary' ]) !!}
			</div>

			<h1 class="page-title">
				{{ Lang::get('account/tickets.view.title') }} #{{ $ticket->reference }}
				@if ( @$property )
					- {{ $property->title }} ({{ $property->ref }})
				@endif
			</h1>

			<div class="row">

				<div class="col-xs-8">

					@if ( $current_site_user->hasRole('company') || ( $ticket->user && $ticket->user->id == $current_site_user->ticket_user_id ) )
						<div class="text-right reply-form-trigger-area">
							<a href="#" class="btn btn-sm btn-primary btn-reply-form-trigger">{{ Lang::get('account/tickets.send') }}</a>
						</div>
						{!! Form::open([ 'id'=>'reply-form', 'action'=>[ 'Account\TicketsController@postReply', $ticket->id ], 'files'=>true, 'class'=>'reply-form ticket-message' ]) !!}
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
								<div class="col-xs-12">
									<div class="form-horizontal">
										<div class="non-private-info">
											<div class="form-group error-container">
												{!! Form::label(null, 'CC', [ 'class'=>'col-sm-2 control-label' ]) !!}
												<div class="col-sm-10">
													<div class="form-control labels-email-input" data-name="cc[]">
														<i class="fa fa-plus-square" aria-hidden="true"></i>
														<ul class="list-inline emails-list"></ul>
													</div>
												</div>
											</div>
											<div class="form-group error-container">
												{!! Form::label(null, 'BCC', [ 'class'=>'col-sm-2 control-label' ]) !!}
												<div class="col-sm-10">
													<div class="form-control labels-email-input" data-name="bcc[]">
														<i class="fa fa-plus-square" aria-hidden="true"></i>
														<ul class="list-inline emails-list"></ul>
													</div>
												</div>
											</div>
											<div class="form-group error-container">
												{!! Form::label('signature_id', Lang::get('account/tickets.signature'), [ 'class'=>'col-sm-2 control-label' ]) !!}
												<div class="col-sm-10">
													<select name="signature_id" class="form-control">
														<option value="">{{ Lang::get('account/tickets.signature.none') }}</option>
														@foreach ($signatures as $signature)
															<option value="{{ $signature->id }}" {{ $signature->default ? 'selected="selected"' : '' }}>{{ $signature->title }}</option>
														@endforeach
													</select>
												</div>
											</div>
											@if ( @count($accounts) > 0 )
												<div class="form-group error-container">
													{!! Form::label('email_account_id', Lang::get('account/tickets.account'), [ 'class'=>'col-sm-2 control-label' ]) !!}
													<div class="col-sm-10">
														<select name="email_account_id" class="form-control">
															<option value=""></option>
															@foreach ($accounts as $account)
																<option value="{{ $account->id }}">{{ $account->title }}</option>
															@endforeach
														</select>
													</div>
												</div>
											@endif
										</div>
										<div class="form-group">
											{!! Form::label('attachment', Lang::get('account/tickets.attachment'), [ 'class'=>'col-sm-2 control-label' ]) !!}
											<div class="col-sm-10">
												<div class="error-container">
													{!! form::file('attachment', [ 'class'=>'form-control' ]) !!}
												</div>
												<div class="help-block">{!! Lang::get('account/tickets.attachment.helper', [ 
													'maxsize'=>Config::get('app.property_image_maxsize', 2048) 
												]) !!}</div>
											</div>
										</div>
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
										<img src="{{ $message->user->image }}" class="pull-left author-icon" title="{{ $message->user->name }}" />
									@else
										<img src="{{ asset('images/tickets/customer.png') }}" class="pull-left author-icon" title="{{ $ticket->contact->fullname }}" />
									@endif
									<div>
										<strong>{{ $message->subject }}</strong>
									</div>
									@if ( $message->user )
										@if ( $message->private )
											<span class="privacy-label pull-right label label-info">{{ Lang::get('account/tickets.internal') }}</span>
										@else
											<span class="privacy-label pull-right label label-warning">{{ Lang::get('account/tickets.public') }}</span>
										@endif
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
							@if ( !empty($message->files) )
								<div class="row">
									<div class="col-xs-12">
										<ul class="message-attachments list-inline">
											@foreach ($message->files as $file)
												<li>
													<a href="{{ $file->url }}" target="_blank">{{ empty($file->title) ? pathinfo($file->url, PATHINFO_EXTENSION) : $file->title }}</a>
												</li>
											@endforeach
										</ul>
									</div>
								</div>
							@endif
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
								<hr />
								{!! Form::open([ 'action'=>'Account\Calendar\BaseController@getCreate', 'method'=>'get', 'class'=>'text-right' ]) !!}
									{!! Form::hidden('customer_id', @$ticket->contact->id_molista) !!}
									{!! Form::hidden('user_ids[]', $current_site_user->id) !!}
									{!! Form::hidden('user_ids[]', @$ticket->user->id_molista) !!}
									{!! Form::hidden('property_ids[]', @$ticket->item->id_molista) !!}
									{!! Form::button(Lang::get('account/calendar.button.schedule'), [ 'type'=>'submit', 'class'=>'btn btn-sm btn-default' ]) !!}
								{!! Form::close() !!}
							</div>
						</div>
					@endif

				</div>

			</div>

		@endif

		{!! Form::open([ 'id'=>'cc-bcc-form', 'class'=>'mfp-hide app-popup-block-white' ]) !!}
			<div class="form-group error-container">
				{!! Form::label('cc-bcc-email-input', 'Email') !!}
				{!! Form::email('email', null, [ 'id'=>'cc-bcc-email-input', 'class'=>'form-control required email' ]) !!}
			</div>
			<div class="text-right">
				<a href="#" class="btn btn-default btn-cancel-trigger">{{ Lang::get('general.cancel') }}</a>
				{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
			</div>
		{!! Form::close() !!}

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

			reply_form.on('change', 'select[name="private"]', function(){
				if ( $(this).val() == 1 ) {
					$(this).addClass('alert-danger');
				} else {
					$(this).removeClass('alert-danger');
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

			var add_cont;
			$('#cc-bcc-form').validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					var em = $('#cc-bcc-email-input').val();

					if ( add_cont.find('.email-input[value="' + em + '"]').length < 1 ) {
						var li =	'<li class="emails-list-item">' +
										'<input type="hidden" name="' + add_cont.data().name + '" value="' + em + '" class="email-input" />' +
										'<div class="label label-default">' + em + '<i class="fa fa-minus-square email-remove-trigger" aria-hidden="true"></i></div>' +
									'</li>';
						add_cont.closest('.form-control').find('.emails-list').append(li);
					}

					$.magnificPopup.close();
				}
			});

			cont.on('click', '.labels-email-input', function(e){
				e.preventDefault();

				add_cont = $(this);

				$.magnificPopup.open({
					items: { 
						src: '#cc-bcc-form' 
					},
					modal: true,
					type: 'inline',
					callbacks: {
						beforeOpen: function() {
							$('#cc-bcc-email-input').val('').closest('form').find('label.error').remove();
						},
					}
				});
			});
			$('body').on('click', '.btn-cancel-trigger', function(e){
				e.preventDefault();
				$.magnificPopup.close();
			});
			cont.on('click', '.email-remove-trigger', function(e){
				e.preventDefault();
				$(this).closest('.emails-list-item').remove();
			});
			cont.on('click', '.emails-list-item', function(e){
				e.stopPropagation();
			});

			reply_form.on('change','select[name="private"]', function(){
				if ( $(this).val() == 1 ) {
					reply_form.find('.non-private-info').addClass('hide');
				} else {
					reply_form.find('.non-private-info').removeClass('hide');
				}
			});

		});
	</script>

@endsection
