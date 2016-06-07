@extends('layouts.account')

@section('account_content')

	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
			{{ session('status') }}
		</div>
	@else
		@include('common.messages', [ 'dismissible'=>true ])
	@endif

	<h1 class="page-title">{{ Lang::get('account/profile.h1') }}</h1>

	{!! Form::model(Auth::user(), [ 'method'=>'POST', 'files'=>true, 'action'=>'AccountController@updateProfile', 'id'=>'user-profile-form' ]) !!}
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('name', Lang::get('account/profile.name')) !!}
					{!! Form::text('name', null, [ 'class'=>'form-control required']) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('email', Lang::get('account/profile.email')) !!}
					{!! Form::email('email', null, [ 'class'=>'form-control required email' ]); !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('locale', Lang::get('account/profile.locale')) !!}
					{!! Form::select('locale', $site_setup['locales_select'], null, [ 'class'=>'form-control required' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<div class="error-container">
						{!! Form::label('password', Lang::get('account/profile.password')) !!}
						<div class="input-group">
							{!! Form::password('password', [ 'class'=>'form-control', 'minlength'=>6 ]) !!}
							<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password" style="cursor: pointer;" aria-hidden="true"></span></div>
						</div>
					</div>
					<div class="help-block">{!! Lang::get('account/profile.password.helper') !!}</div>
				</div>
			</div>
		</div>

		<br />

		<div class="text-right">
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

	{!! Form::close() !!}

	<hr />
	<h2 class="page-title">{{ Lang::get('account/profile.plans.h1') }}</h1>
	<div class="row">
		<div class="col-xs-12 col-sm-8">
			<a href="#" class="btn btn-primary pull-right">{{ Lang::get('account/profile.plans.upgrade') }}</a>
			<div>
				<p><strong>{{ Lang::get('account/profile.plans.current') }}: {{ \App\Session\Site::get('plan.name') }}</strong></p>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="text-right">
				<a href="" class="btn btn-link">{{ Lang::get('account/profile.plans.show') }}</a>
			</div>
		</div>
	</div>

	@if ( \App\Session\Site::get('plan.payment_method') )
		<hr />
		<h2 class="page-title">{{ Lang::get('account/profile.payment.h1') }}</h1>
		@if ( \App\Session\Site::get('plan.payment_method') == 'stripe' )
			<p>{{ Lang::get('account/profile.payment.stripe') }}</p>
		@elseif ( \App\Session\Site::get('plan.payment_method') == 'transfer' )
			<p>{{ Lang::get('account/profile.payment.transfer') }}</p>
			{!! Form::open([ 'id'=>'pay-transfer-form', 'class'=>'form-inline' ]) !!}
				<div class="form-group">
					{!! Form::label('iban_account', Lang::get('account/profile.payment.transfer.iban')) !!}
					{!! Form::text('iban_account', \App\Session\Site::get('plan.iban_account'), [ 'class'=>'form-control required' ]) !!}
				</div>
				{!! Form::button(Lang::get('account/profile.payment.transfer.update'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
			{!! Form::close([ 'class'=>'form-inline' ]) !!}
		@endif
	@endif

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var profile_form = $('#user-profile-form');
			var transfer_form = $('#pay-transfer-form');

			profile_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			profile_form.on('click', '.show-hide-password', function(e){
				e.preventDefault();
				profile_form.find('input[name="password"]').togglePassword();
			});

			transfer_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
alert(123);
				}
			});

			
		});
	</script>
@endsection
