@extends('layouts.account')

@section('account_content')

	<div id="account-tickets">

		<h1>{{ Lang::get('account/tickets.delete') }}</h1>

		@include('common.messages', [ 'dismissible'=>true ])

		{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\TicketsController@deleteDestroy', $ticket->id] ]) !!}
			{!! Form::hidden('confirm',1) !!}

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

			<div class="alert alert-danger text-center">
				<h4 style="margin: 0px;">{{ Lang::get('account/employees.delete.intro') }}</h4>
			</div>

			<div class="text-right">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::submit( Lang::get('general.delete'), [ 'class'=>'btn btn-danger']) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#delete-form');

			// Form validation
			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

		});
	</script>

@endsection
