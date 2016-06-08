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
		<h4 class="page-title">{{ Lang::get('account/payment.plan.details.title') }}</h4>
		<div class="row">
			@foreach (['free','pro','plus'] as $c)
				<div class="col-xs-4">
					<div class="plan-block plan-{{ $c }}">
						<div class="plan-block-title">{{ $plans[$c]->name }}</div>
						<div class="plan-block-body">
							<div class="plan-block-item">
								<div class="plan-block-feature">{{ Lang::get('account/payment.plan.price.year') }}</div>
								<div class="plan-block-price">
									@if ( @$plans[$c]->is_free ) 
										{{ Lang::get('account/payment.plan.free') }}
									@else
										{{ price($plans[$c]->price_year,[ 'decimals'=>0 ]) }}
									@endif
								</div>
							</div>
							<div class="plan-block-item">
								<div class="plan-block-feature">{{ Lang::get('account/payment.plan.price.year.month') }}</div>
								<div class="plan-block-price">
									@if ( @$plans[$c]->is_free ) 
										{{ Lang::get('account/payment.plan.free') }}
									@else
										{{ price($plans[$c]->price_year/12,[ 'decimals'=>1 ]) }}
									@endif
								</div>
							</div>
							<div class="plan-block-item">
								<div class="plan-block-feature">{{ Lang::get('account/payment.plan.price.month') }}</div>
								<div class="plan-block-price">
									@if ( @$plans[$c]->is_free ) 
										{{ Lang::get('account/payment.plan.free') }}
									@else
										{{ price($plans[$c]->price_month,[ 'decimals'=>1 ]) }}
									@endif
								</div>
							</div>
							<div class="plan-block-item">
								{{ Lang::get('account/payment.plan.employees') }}: 
								@if ( @$plans[$c]->max_employees )
									<strong>{{ number_format($plans[$c]->max_employees, 0, ',', '.') }}</strong>
								@else
									<strong>{{ Lang::get('account/payment.plan.unlimited') }}</strong>
								@endif
							</div>
							<div class="plan-block-item">
								{{ Lang::get('account/payment.plan.space') }}: 
								@if ( @$plans[$c]->max_space )
									<strong>{{ number_format($plans[$c]->max_space, 0, ',', '.') }}GB</strong>
								@else
									<strong>{{ Lang::get('account/payment.plan.unlimited') }}</strong>
								@endif
							</div>
							<div class="plan-block-item">
								{{ Lang::get('account/payment.plan.properties') }}: 
								@if ( @$plans[$c]->max_properties )
									<strong>{{ number_format($plans[$c]->max_properties, 0, ',', '.') }}</strong>
								@else
									<strong>{{ Lang::get('account/payment.plan.unlimited') }}</strong>
								@endif
							</div>
							<div class="plan-block-item">
								@if ( @$plans[$c]->configuration['integrations'] )
									{{ Lang::get('account/payment.plan.properties') }}
								@else
									-
								@endif
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
<?php
echo "<pre>";
print_r($plans);
echo "</pre>";
?>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#user-profile-form');

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

			$('#plans-modal-trigger').magnificPopup({
				type: 'inline'
			});

		});
	</script>
@endsection
