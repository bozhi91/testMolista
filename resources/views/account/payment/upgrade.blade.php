@extends('layouts.account')

@section('account_content')

<style type="text/css">
	.plan-group { position: relative; }
		.plan-group .mfp-bg { position: absolute; background: #fff; }
	.invoicing-area { margin-top: 50px; }
	.invoicing-area .checkbox { border: none; background: transparent; }
</style>

	@include('common.messages', [ 'dismissible'=>true ])

	<h1 class="page-title">{{ Lang::get('account/payment.plan.upgrade') }}</h1>

	{!! Form::model($current_site, [ 'method'=>'POST', 'action'=>'Account\PaymentController@postUpgrade', 'id'=>'upgrade-form' ]) !!}

		{!! Form::label(null, Lang::get('account/payment.upgrade.select')) !!}
		<a href="#plans-modal" class="plans-modal-trigger">
			<sup>
				<span class="glyphicon glyphicon-info-sign"></span>
			</sup>
		</a>
		<div class="form-group error-container">
			<div class="row">
				@foreach ($plans as $plan)
					<div class="col-xs-12 col-sm-4">
						<div class="radio plan-group {{ ($plan->level <= $current_plan_level) ? 'plan-group-disabled' : '' }} {{ $plan->is_free ? 'plan-is-free' : '' }}">
							<label>
								{!! Form::radio('plan', $plan->code, old('plan', Input::get('plan')) == $plan->code, [ 'class'=>'plan-select required' ]) !!}
								{{ $plan->name }}
							</label>
							<div class="plan-select">
								<br />
								@if ( $plan->is_free )
									{!! Form::select("payment_interval[{$plan->code}]", [
										'year' => Lang::get('web/plans.free'),
									], null, [ 'class'=>'payment-interval-select form-control', 'disabled'=>'disabled' ]) !!}
								@else
									{!! Form::select("payment_interval[{$plan->code}]", [
										'year' => Lang::get('web/plans.price.year') . ' ' . price($plan->price_year, $plan->infocurrency->toArray()),
										'month' => Lang::get('web/plans.price.month') . ' ' . price($plan->price_month, $plan->infocurrency->toArray()),
									], null, [ 'class'=>'payment-interval-select form-control', 'disabled'=>'disabled' ]) !!}
								@endif
							</div>
							@if ($plan->level <= $current_plan_level)
								<div class="mfp-bg"></div>
							@endif
						</div>
					</div>
				@endforeach
			</div>
		</div>

		@if ( empty($site_setup['plan']['payment_method']) )
			<p>&nbsp;</p>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label(null, Lang::get('account/payment.method.h1')) !!}
						{!! Form::select("payment_method", [ ''=>'' ]+$payment_options, null, [ 'class'=>'payment-method-select form-control' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="pay-method-rel pay-method-rel-transfer hide">
						<div class="form-group error-container">
							{!! Form::label('iban_account', Lang::get('account/payment.method.account')) !!}
							{!! Form::text('iban_account', null, [ 'class'=>'form-control iban' ]) !!}
						</div>
					</div>
					<div class="pay-method-rel pay-method-rel-stripe hide">
					</div>
				</div>
			</div>
		@endif

		<div class="invoicing-area">
			<h3>{{ Lang::get('account/payment.invoicing.title') }}</h3>
			@include('account.payment.form-invoicing', [
				'invoicing_type' => old('invoicing.type', @$current_site->invoicing['type']),
			])
		</div>

		<br />

		<div class="text-right">
			{!! Form::submit( Lang::get('account/payment.plan.upgrade.simple'), [ 'class'=>'btn btn-primary']) !!}
		</div>

	{!! Form::close() !!}

	<div id="plans-modal" class="mfp-hide app-popup-block-white app-popup-block-large">
		@include('corporate.common.plans', [
			'buy_plans' => $plans,
			'buy_button_hidden' => true,
		])
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#upgrade-form');

			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					payment_method: {
						required: function() {
							var plan = form.find('input[name="plan"]:checked');
							return ( plan.length < 1 ) ? false : ( plan.closest('.plan-group').hasClass('plan-is-free') ? false : true );
						}
					},
					iban_account: {
						required: function() {
							return form.find('select[name="payment_method"]').val() == 'transfer';
						}
					},
					"invoicing[company]": {
						required: function() {
							return form.find('input[name="invoicing[type]"]:checked').val() == 'company';
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.find('.plans-modal-trigger').magnificPopup({
				type: 'inline',
				showCloseBtn: false
			});

			form.on('click','input[name="plan"]', function(e){
				form.find('.payment-interval-select').removeClass('required').attr('disabled','disabled')
					.filter('[name="payment_interval[' + $(this).val() + ']"]').addClass('required').removeAttr('disabled');
			});
			form.find('input[name="plan"]:checked').trigger('click');
			form.find('.plan-group-disabled .plan-select').attr('disabled','disabled');

			form.on('change','select[name="payment_method"]', function(e){
				form.find('.pay-method-rel').addClass('hide').filter('.pay-method-rel-' + $(this).val()).removeClass('hide');
			});
			form.find('select[name="payment_method"]').trigger('change');

		});
	</script>
@endsection
