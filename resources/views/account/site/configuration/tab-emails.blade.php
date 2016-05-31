{!! Form::hidden('mailer[service]', empty($site->mailer['service']) ? 'default' : null, [ 'id'=>'mailer-service-input' ]) !!}

<p>&nbsp;</p>

@if ( false && !empty($site->mailer['service']) )
	<div class="pull-right hidden-xs">
		<a href="#" class="btn btn-default btn-sm btn-test-email">{{ Lang::get('account/site.configuration.mailing.test.button') }}</a>
	</div>
@endif

<ul class="nav nav-pills mailer-tabs" role="tablist">
	<li role="presentation" class="mailer-option {{ ( empty($site->mailer['service']) || @$site->mailer['service'] == 'default' ) ? 'active' : '' }}">
		<a href="#tab-mailer-option-default" aria-controls="tab-mailer-option-default" role="tab" data-toggle="tab" data-service="default">{{ Lang::get('account/site.configuration.mailing.default') }}</a>
		@if ( empty($site->mailer['service']) || @$site->mailer['service'] == 'default' ) 
			<span class="mailer-current">{{ Lang::get('account/site.configuration.mailing.current') }}</span>
		@endif
	</li>
	<li role="presentation" class="mailer-option {{ ( @$site->mailer['service'] == 'custom' ) ? 'active' : '' }}">
		<a href="#tab-mailer-option-custom" aria-controls="messages" role="tab" data-toggle="tab" data-service="custom">{{ Lang::get('account/site.configuration.mailing.smtp') }}</a>
		@if ( @$site->mailer['service'] == 'custom' ) 
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

	<div role="tabpanel" class="tab-pane mailer-tab-pane {{ ( empty($site->mailer['service']) || @$site->mailer['service'] == 'default' ) ? 'active' : '' }}" id="tab-mailer-option-default">
		<div class="help-block">
			{!! Lang::get('account/site.configuration.mailing.default.help') !!}
		</div>
	</div>

	<div role="tabpanel" class="tab-pane mailer-tab-pane {{ ( @$site->mailer['service'] == 'custom' ) ? 'active' : '' }}" id="tab-mailer-option-custom">

		<hr />

		<div class="row">

			<div class="col-xs-12 col-sm-6">
				<h4 class="page-title">{{ Lang::get('account/site.configuration.mailing.out') }}</h4>
				<div class="form-group error-container">
					{!! Form::label('mailer[out][protocol]', Lang::get('account/site.configuration.mailing.protocol')) !!}
					{!! Form::select('mailer[out][protocol]', [
						'smtp' => 'SMTP',
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[out][username]', Lang::get('account/site.configuration.mailing.smtp.login')) !!}
					{!! Form::text('mailer[out][username]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[out][password]', Lang::get('account/site.configuration.mailing.smtp.pass')) !!}
					{!! Form::text('mailer[out][password]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[out][host]', Lang::get('account/site.configuration.mailing.smtp.host')) !!}
					{!! Form::text('mailer[out][host]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8">
						<div class="form-group error-container">
							{!! Form::label('mailer[out][port]', Lang::get('account/site.configuration.mailing.smtp.port')) !!}
							{!! Form::text('mailer[out][port]', null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							<label class="hidden-xs">&nbsp;</label>
							{!! Form::select('mailer[out][layer]', [
								'' => '',
								'tls' => 'TLS',
								'ssl' => 'SSL',
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12 col-sm-6">
				<h4 class="page-title">{{ Lang::get('account/site.configuration.mailing.in') }}</h4>
				<div class="form-group error-container">
					{!! Form::label('mailer[in][protocol]', Lang::get('account/site.configuration.mailing.protocol')) !!}
					{!! Form::select('mailer[in][protocol]', [
						'pop3' => 'POP3',
						'imap' => 'IMAP',
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[in][username]', Lang::get('account/site.configuration.mailing.smtp.login')) !!}
					{!! Form::text('mailer[in][username]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[in][password]', Lang::get('account/site.configuration.mailing.smtp.pass')) !!}
					{!! Form::text('mailer[in][password]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group error-container">
					{!! Form::label('mailer[in][host]', Lang::get('account/site.configuration.mailing.smtp.host')) !!}
					{!! Form::text('mailer[in][host]', null, [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8">
						<div class="form-group error-container">
							{!! Form::label('mailer[in][port]', Lang::get('account/site.configuration.mailing.smtp.port')) !!}
							{!! Form::text('mailer[in][port]', null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							<label class="hidden-xs">&nbsp;</label>
							{!! Form::select('mailer[in][layer]', [
								'' => '',
								'tls' => 'TLS',
								'ssl' => 'SSL',
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>

