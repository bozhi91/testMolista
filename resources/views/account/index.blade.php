@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		.account-block .plan-item { font-size: 18px; font-weight: bold; padding-top: 8px; }
		.account-block .list-inline { margin-right: -5px; }
	</style>

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

		@include('account.user-form', [
			'user_image' => empty(Auth::user()->image) ? false : Auth::user()->image_directory . '/' . Auth::user()->image,
		])

		<br />

		<div class="text-right">
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

	{!! Form::close() !!}

	<div class="hidden-xs hidden-sm">

		<div class="account-block">
			<h3>{{ Lang::get('account/payment.plan.h1') }}</h3>
			<div class="text-right">
				<ul class="list-inline">
					<li class="plan-item pull-left">{{ \App\Session\Site::get('plan.name') }}</li>
					<li><a href="#plans-modal" class="btn btn-link" id="plans-modal-trigger">{{ Lang::get('account/payment.plan.show') }}</a></li>
					<li><a href="{{ action('Account\PaymentController@getUpgrade') }}" class="btn btn-primary">{{ Lang::get('account/payment.plan.upgrade') }}</a></li>
				</ul>
			</div>
		</div>

		@if ( \App\Session\Site::get('plan.payment_method') )
			<div class="account-block">
				<h3>{{ Lang::get('account/payment.method.h1') }}</h3>
				<ul class="list-inline">
					@if ( \App\Session\Site::get('plan.payment_method') == 'stripe' )
						<li class="plan-item">{{ Lang::get('account/payment.method.stripe') }}</li>
					@elseif ( \App\Session\Site::get('plan.payment_method') == 'transfer' )
						<li>
							<div class="plan-item">{{ Lang::get('account/payment.method.transfer') }}</div>
							<div class="help-block">{{ \App\Session\Site::get('plan.iban_account') }}</div>
						</li>
					@endif
					<li class="pull-right"><a href="{{ action('Account\PaymentController@getMethod') }}" class="btn btn-primary">{{ Lang::get('account/payment.method.change') }}</a></li>
				</ul>
			</div>
		@endif

		<div id="plans-modal" class="mfp-hide app-popup-block-white app-popup-block-large">
			@include('corporate.common.plans', [
				'buy_plan_url' => action('Account\PaymentController@getUpgrade'),
				'buy_button_text' => Lang::get('account/payment.plan.upgrade.simple'),
			])
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#user-profile-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					image: {
						required: function() {
							if ( form.find('.user-image-link').length > 0 ) {
								return false;
							}
							return form.find('select[name="signature"]').val() == 1;
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			$('#plans-modal-trigger').magnificPopup({
				type: 'inline',
				showCloseBtn: false
			});

		});
	</script>
@endsection
