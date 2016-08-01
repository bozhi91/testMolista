{!! Form::model($account, [ 'method'=>$method, 'url'=>$action, 'id'=>'account-form' ]) !!}

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('protocol', Lang::get('account/profile.accounts.field.protocol')) !!}
				{!! Form::select('protocol', [
					'' => '',
					'smtp' => 'SMTP',
					'pop3' => 'POP3',
					'imap' => 'IMAP',
					'mailgun' => 'Mailgun',
				], null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('from_name', Lang::get('account/profile.accounts.field.name')) !!}
				{!! Form::text('from_name', null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('from_email', Lang::get('account/profile.accounts.field.email')) !!}
				{!! Form::text('from_email', null, [ 'class'=>'form-control required email' ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('username', Lang::get('account/profile.accounts.field.username')) !!}
				{!! Form::text('username', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('password', Lang::get('account/profile.accounts.field.password')) !!}
					{!! Form::text('password', null, [ 'class'=>'form-control '.($account ? '' : 'required') ]) !!}
				</div>
				@if ( $account )
					<div class="help-block">{{ Lang::get('account/profile.accounts.field.password.helper') }}</div>
				@endif
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('host', Lang::get('account/profile.accounts.field.host')) !!}
				{!! Form::text('host', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::label('port', Lang::get('account/profile.accounts.field.port')) !!}
				{!! Form::text('port', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				<label class="hidden-xs">&nbsp;</label>
				{!! Form::select('layer', [
					'' => '',
					'tls' => 'TLS',
					'ssl' => 'SSL',
				], null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
	</div>

	<div class="text-right">
		@if ( $account )
			{!! Form::button( Lang::get('account/profile.accounts.test'), [ 'class'=>'btn btn-info btn-sm pull-left btn-test-trigger hidden-xs', 'data-url'=>action('Account\Profile\AccountsController@getTest', $account->id) ]) !!}
		@endif
		<a href="{{ action('Account\Profile\AccountsController@getIndex') }}" class="btn btn-default">{{ Lang::get('general.back') }}</a>
		@if ( $account )
			{!! Form::button( Lang::get('general.delete'), [ 'class'=>'btn btn-danger btn-delete-trigger hidden-xs']) !!}
		@endif
		{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
	</div>

{!! Form::close() !!}

@if ( $account )
	{!! Form::open([ 'method'=>'delete', 'action'=>['Account\Profile\AccountsController@deleteRemove', $account->id], 'id'=>'account-delete-form' ]) !!}
	{!! Form::close() !!}
@endif

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#account-form');
		var form_delete = $('#account-delete-form');

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

		form.on('click','.btn-delete-trigger',function(e){
			e.preventDefault();
			form_delete.submit();
		});

		form_delete.validate({
			submitHandler: function(f) {
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/profile.accounts.delete.warning') ) }}", function (e) {
					if (e) {
						LOADING.show();
						f.submit();
					}
				});
			}
		});

		form.on('click','.btn-test-trigger',function(e){
			e.preventDefault();

			var el = $(this);

			LOADING.show();

			$.ajax({
				dataType: 'json',
				url: el.data().url,
				success: function(data) {
					LOADING.hide();
					if ( data.success ) {
						alertify.success("{{ print_js_string( Lang::get('account/profile.accounts.tested') ) }}");
					} else {
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				},
				error: function() {
					LOADING.hide();
					alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
				}
			});

		});

	});
</script>