{!! Form::model($item, [ 'action'=>$action, 'method'=>$method, 'id'=>'customer-form' ]) !!}
	<input type="hidden" name="ajax" value="{{ Input::get('ajax') }}" />

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! form::label('first_name', Lang::get('web/customers.register.name.first')) !!}
				{!! form::text('first_name', null, [ 'class'=>'form-control required']) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! form::label('last_name', Lang::get('web/customers.register.name.last')) !!}
				{!! form::text('last_name', null, [ 'class'=>'form-control required']) !!}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! form::label('email', Lang::get('web/customers.register.email')) !!}
				{!! form::text('email', null, [ 'class'=>'form-control required email']) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! form::label('phone', Lang::get('web/customers.register.phone')) !!}
				{!! form::text('phone', null, [ 'class'=>'form-control required']) !!}
			</div>
		</div>
	</div>

	<div class="row">
		@if ( !empty($filled_by_customer) )
			<div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<div class="error-container">
						{!! form::label('password', Lang::get('web/customers.register.password')) !!}
						<div class="input-group">
							@if ( $item )
								{!! form::password('password', [ 'class'=>'form-control', 'minlength'=>6 ]) !!}
							@else
								{!! form::password('password', [ 'class'=>'form-control required', 'minlength'=>6 ]) !!}
							@endif
							<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password cursor-pointer" aria-hidden="true"></span></div>
						</div>
					</div>
					@if ( $item )
						<div class="help-block">{{ Lang::get('web/customers.register.password.help') }}</div>
					@endif
				</div>
			</div>
		@else
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! form::label('password', Lang::get('account/customers.locale')) !!}
					{!! form::select('locale', $site_setup['locales_tabs'], null, [ 'class'=>'form-control required']) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! form::label('dni', Lang::get('account/customers.dni')) !!}
					{!! form::text('dni', null, [ 'class'=>'form-control']) !!}
				</div>
			</div>
		@endif
	</div>

	<div class="form-group">
		<div class="text-right">
			{!! Form::submit($button, [ 'class'=>'btn btn-primary' ]) !!}
		</div>
	</div>

{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#customer-form');

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			rules: {
				email: {
					remote: {
						url: '{{ action('Web\CustomersController@getCheck', 'email') }}',
						type: 'get',
						data: {
							id : {{ $item ? $item->id : 'null' }}
						}
					}
				}
			},
			messages: {
				email: {
					remote: "{{ print_js_string( Lang::get('web/customers.register.email.used') ) }}"
				}
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

		form.on('click', '.show-hide-password', function(e){
			e.preventDefault();
			form.find('input[name="password"]').togglePassword();
		});

	});
</script>
