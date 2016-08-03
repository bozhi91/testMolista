@extends('layouts.account')

@section('account_content')

	<div id="account-tickets">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/tickets.new') }}</h1>

		{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\TicketsController@postCreate', 'files'=>true, 'id'=>'ticket-form' ]) !!}

			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="error-container form-group">
						{!! Form::label('customer_id', Lang::get('account/tickets.contact.name')) !!}
						{!! form::select('customer_id', [ ''=>'&nbsp;' ]+$customers, null, [ 'class'=>'form-control required has-select-2']) !!}
					</div>
					<div class="error-container form-group">
						{!! Form::label('user_id', Lang::get('account/tickets.assigned.to')) !!}
						@if ( Auth::user()->hasRole('company') )
							{!! form::select('user_id', [ ''=>'&nbsp;' ]+$employees, null, [ 'class'=>'form-control has-select-2']) !!}
						@else
							{!! form::select('user_id', $employees, null, [ 'class'=>'form-control required', 'readonly'=>'readonly' ]) !!}
						@endif
					</div>
					<div class="error-container form-group">
						{!! Form::label('property_id', Lang::get('account/properties.column.title')) !!}
						{!! form::select('property_id', [ ''=>'&nbsp;' ]+$properties, null, [ 'class'=>'form-control has-select-2']) !!}
					</div>
					<div class="form-group error-container">
						{!! Form::label('signature_id', Lang::get('account/tickets.signature')) !!}
						<select name="signature_id" class="form-control">
							<option value="">{{ Lang::get('account/tickets.signature.none') }}</option>
							@foreach ($signatures as $signature)
								<option value="{{ $signature->id }}" {{ $signature->default ? 'selected="selected"' : '' }}>{{ $signature->title }}</option>
							@endforeach
						</select>
					</div>
					@if ( @count($accounts) > 0 )
						<div class="form-group error-container">
							{!! Form::label('email_account_id', Lang::get('account/tickets.account')) !!}
							<select name="email_account_id" class="form-control">
								<option value=""></option>
								@foreach ($accounts as $account)
									<option value="{{ $account->id }}">{{ $account->title }}</option>
								@endforeach
							</select>
						</div>
					@endif
					<div class="form-group">
						{!! Form::label('attachment', Lang::get('account/tickets.attachment')) !!}
						<div class="error-container">
							{!! form::file('attachment', [ 'class'=>'form-control' ]) !!}
						</div>
						<div class="help-block">{!! Lang::get('account/tickets.attachment.helper', [ 
							'maxsize'=>Config::get('app.property_image_maxsize', 2048) 
						]) !!}</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('subject', Lang::get('account/tickets.subject')) !!}
						{!! Form::text('subject', null, [ 'class'=>'form-control required' ]) !!}
					</div>
					<div class="form-group error-container">
						{!! Form::label(null, 'CC') !!}
						<div class="form-control labels-email-input" data-name="cc[]">
							<i class="fa fa-plus-square" aria-hidden="true"></i>
							<ul class="list-inline emails-list"></ul>
						</div>
					</div>
					<div class="form-group error-container">
						{!! Form::label(null, 'BCC') !!}
						<div class="form-control labels-email-input" data-name="bcc[]">
							<i class="fa fa-plus-square" aria-hidden="true"></i>
							<ul class="list-inline emails-list"></ul>
						</div>
					</div>
					<div class="form-group error-container">
						{!! Form::label('body', Lang::get('account/tickets.body')) !!}
						{!! Form::textarea('body', null, [ 'class'=>'form-control required', 'rows'=>5 ]) !!}
					</div>
					<div class="text-right">
						<a href="{{ action('Account\TicketsController@getIndex') }}" class="btn btn-default">{{ Lang::get('general.back') }}</a>
						{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
					</div>
				</div>
			</div>

		{!! Form::close() !!}

	</div>

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

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#ticket-form');

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

			form.find('.has-select-2').select2();

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

			form.on('click', '.labels-email-input', function(e){
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
			form.on('click', '.email-remove-trigger', function(e){
				e.preventDefault();
				$(this).closest('.emails-list-item').remove();
			});
			form.on('click', '.emails-list-item', function(e){
				e.stopPropagation();
			});

		});
	</script>

@endsection