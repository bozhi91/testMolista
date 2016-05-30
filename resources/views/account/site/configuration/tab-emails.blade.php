{!! Form::hidden('mailer[service]', empty($site->mailer['service']) ? 'mail' : null, [ 'id'=>'mailer-service-input' ]) !!}

<p>&nbsp;</p>

@if ( !empty($site->mailer['service']) )
	<div class="pull-right hidden-xs">
		<a href="#" class="btn btn-default btn-sm btn-test-email">{{ Lang::get('account/site.configuration.mailing.test.button') }}</a>
	</div>
@endif

<ul class="nav nav-pills mailer-tabs" role="tablist">
	<li role="presentation" class="mailer-option {{ ( empty($site->mailer['service']) || @$site->mailer['service'] == 'mail' ) ? 'active' : '' }}">
		<a href="#tab-mailer-option-mail" aria-controls="tab-mailer-option-mail" role="tab" data-toggle="tab" data-service="mail">{{ Lang::get('account/site.configuration.mailing.default') }}</a>
		@if ( empty($site->mailer['service']) || @$site->mailer['service'] == 'mail' ) 
			<span class="mailer-current">{{ Lang::get('account/site.configuration.mailing.current') }}</span>
		@endif
	</li>
	<li role="presentation" class="mailer-option {{ ( @$site->mailer['service'] == 'mandrill' ) ? 'active' : '' }} hide">
		<a href="#tab-mailer-option-mandrill" aria-controls="tab-mailer-option-mandrill" role="tab" data-toggle="tab" data-service="mandrill">{{ Lang::get('account/site.configuration.mailing.mandrill') }}</a>
		@if ( @$site->mailer['service'] == 'mandrill' ) 
			<span class="mailer-current">{{ Lang::get('account/site.configuration.mailing.current') }}</span>
		@endif
	</li>
	<li role="presentation" class="mailer-option {{ ( @$site->mailer['service'] == 'smtp' ) ? 'active' : '' }}">
		<a href="#tab-mailer-option-smtp" aria-controls="messages" role="tab" data-toggle="tab" data-service="smtp">{{ Lang::get('account/site.configuration.mailing.smtp') }}</a>
		@if ( @$site->mailer['service'] == 'smtp' ) 
			<span class="mailer-current">{{ Lang::get('account/site.configuration.mailing.current') }}</span>
		@endif
	</li>
</ul>

<div class="tab-content mailer-tab-content">

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('mailer[from_name]', Lang::get('account/site.configuration.mailing.from.name')) !!}
				{!! Form::text('mailer[from_name]', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('mailer[from_email]', Lang::get('account/site.configuration.mailing.from.email')) !!}
				{!! Form::text('mailer[from_email]', null, [ 'class'=>'form-control required email' ]) !!}
			</div>
		</div>
	</div>

	<div role="tabpanel" class="tab-pane mailer-tab-pane {{ ( empty($site->mailer['service']) || @$site->mailer['service'] == 'mail' ) ? 'active' : '' }}" id="tab-mailer-option-mail">
		<div class="help-block">
			{!! Lang::get('account/site.configuration.mailing.default.help') !!}
		</div>
	</div>

	<div role="tabpanel" class="tab-pane mailer-tab-pane {{ ( @$site->mailer['service'] == 'mandrill' ) ? 'active' : '' }}" id="tab-mailer-option-mandrill">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('mailer[mandrill_user]', Lang::get('account/site.configuration.mailing.mandrill.user')) !!}
					{!! Form::text('mailer[mandrill_user]', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('mailer[mandrill_key]', Lang::get('account/site.configuration.mailing.mandrill.key')) !!}
					{!! Form::text('mailer[mandrill_key]', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('mailer[mandrill_host]', Lang::get('account/site.configuration.mailing.mandrill.host')) !!}
					{!! Form::text('mailer[mandrill_host]', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('mailer[mandrill_port]', Lang::get('account/site.configuration.mailing.mandrill.port')) !!}
					{!! Form::text('mailer[mandrill_port]', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="help-block">
			{!! Lang::get('account/site.configuration.mailing.mandrill.help') !!}
		</div>
	</div>

	<div role="tabpanel" class="tab-pane mailer-tab-pane {{ ( @$site->mailer['service'] == 'smtp' ) ? 'active' : '' }}" id="tab-mailer-option-smtp">

		<hr />

		<div class="row">

			<div class="col-xs-12 col-sm-6">
				<h4 class="page-title">SMTP</h4>
				<div class="form-group error-container">
					{!! Form::label('mailer[smtp_login]', Lang::get('account/site.configuration.mailing.smtp.login')) !!}
					{!! Form::text('mailer[smtp_login]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[smtp_pass]', Lang::get('account/site.configuration.mailing.smtp.pass')) !!}
					{!! Form::text('mailer[smtp_pass]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[smtp_host]', Lang::get('account/site.configuration.mailing.smtp.host')) !!}
					{!! Form::text('mailer[smtp_host]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8">
						<div class="form-group error-container">
							{!! Form::label('mailer[smtp_port]', Lang::get('account/site.configuration.mailing.smtp.port')) !!}
							{!! Form::text('mailer[smtp_port]', null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							<label class="hidden-xs">&nbsp;</label>
							{!! Form::select('mailer[smtp_tls_ssl]', [
								'' => '',
								'tls' => 'TLS',
								'ssl' => 'SSL',
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
				</div>
				<div class="help-block">
					{!! Lang::get('account/site.configuration.mailing.smtp.help') !!}
				</div>
			</div>

			<div class="col-xs-12 col-sm-6">
				<h4 class="page-title">POP3</h4>
				<div class="form-group error-container">
					{!! Form::label('mailer[pop3_login]', Lang::get('account/site.configuration.mailing.smtp.login')) !!}
					{!! Form::text('mailer[pop3_login]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[pop3_pass]', Lang::get('account/site.configuration.mailing.smtp.pass')) !!}
					{!! Form::text('mailer[pop3_pass]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[pop3_host]', Lang::get('account/site.configuration.mailing.smtp.host')) !!}
					{!! Form::text('mailer[pop3_host]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8">
						<div class="form-group error-container">
							{!! Form::label('mailer[pop3_port]', Lang::get('account/site.configuration.mailing.smtp.port')) !!}
							{!! Form::text('mailer[pop3_port]', null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							<label class="hidden-xs">&nbsp;</label>
							{!! Form::select('mailer[pop3_tls_ssl]', [
								'' => '',
								'tls' => 'TLS',
								'ssl' => 'SSL',
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
				</div>
				<div class="help-block">
					{!! Lang::get('account/site.configuration.mailing.pop3.help') !!}
				</div>
			</div>

		</div>

	</div>

</div>

