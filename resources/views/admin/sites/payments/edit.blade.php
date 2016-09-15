<?php
	$payment_currency = $payment->infocurrency->toArray();
	$payment_currency['decimals'] = 2;
?>

@extends('layouts.popup')

@section('content')

	@if ( session('success') )
		<div style="padding: 20px 30px;">
			<div class="alert alert-success text-center">
				<h4>{!! session('success') !!}</h4>
				<p>&nbsp;</p>
				<div>
					<button class="btn btn-success" id="btn-close-trigger">{{ Lang::get('general.messages.close') }}</button>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			ready_callbacks.push(function(){
				$('#btn-close-trigger').on('click', function(e){
					e.preventDefault();
					window.parent.payments_reload();
				});
			});
		</script>

	@else
		<style type="text/css">
			#payment-form input[readonly="readonly"] { background-color: #fff; }
		</style>

		<div style="padding: 20px 30px;">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($payment, [ 'method'=>'POST', 'action'=>[ 'Admin\Sites\PaymentsController@postSave', $payment->id ], 'id'=>'payment-form' ]) !!}

				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('payment_amount', Lang::get('admin/resellers.payments.amount')) !!}
							<div class="input-group">
								{!! Form::text('payment_amount', null, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
								<div class="input-group-addon">{{ $payment->payment_currency }}</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/resellers.payments.paid.from')) !!}
							{!! Form::text(null, $payment->paid_from->format("Y-m-d"), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/resellers.payments.paid.until')) !!}
							{!! Form::text(null, $payment->paid_until->format("Y-m-d"), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('reseller_id', Lang::get('admin/resellers.payments.reseller')) !!}
							{!! Form::select('reseller_id', [''=>'&nbsp;']+$resellers, null, [ 'class'=>'form-control', ]) !!}
						</div>
					</div>
				</div>

				<div class="reseller-rel" style="display: none;">

					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								{!! Form::label('reseller_fixed', Lang::get('admin/resellers.payments.reseller.fixed')) !!}
								<div class="input-group">
									{!! Form::text('reseller_fixed', null, [ 'class'=>'form-control number update-comission-trigger fixed-commission-input reseller-input-rel', 'min'=>'0', ]) !!}
									<div class="input-group-addon">{{ $payment->payment_currency }}</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								{!! Form::label('reseller_variable', Lang::get('admin/resellers.payments.reseller.variable')) !!}
								<div class="input-group plan-input-group">
									{!! Form::text('reseller_variable', null, [ 'class'=>'form-control number update-comission-trigger variable-commission-input reseller-input-rel', 'min'=>'0', 'max'=>100, ]) !!}
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								{!! Form::label('reseller_amount', Lang::get('admin/resellers.payments.reseller.total')) !!}
								<div class="input-group">
									{!! Form::text('reseller_amount', null, [ 'class'=>'form-control total-commission-input', 'readonly'=>'readonly', ]) !!}
									<div class="input-group-addon">{{ $payment->payment_currency }}</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								<label>&nbsp;</label>
								<div class="checkbox">
									<label>
										{!! Form::checkbox('reseller_paid', 1, null, [ 'id'=>'reseller_paid', 'class'=>'reseller-input-rel' ]) !!}
										{{ Lang::get('admin/resellers.payments.paid') }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								{!! Form::label('reseller_date', Lang::get('admin/resellers.payments.paid.date')) !!}
								<div class="error-container" style="position: relative;">
									{!! Form::text('reseller_date', null, [ 'class'=>'form-control reseller-input-rel' ]) !!}
								</div>
							</div>
						</div>
					</div>

				</div>

				<div class="text-right">
					<a href="#" class="btn btn-default trigger-close">{{ Lang::get('general.cancel') }}</a>
					{!! Form::button(Lang::get('general.save'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
				</div>

			{!! Form::close() !!}

		</div>

		<script type="text/javascript">
			ready_callbacks.push(function(){
				var form = $('#payment-form');
				var amount = {{ $payment->payment_amount }};

				form.validate({
					errorPlacement: function(error, element) {
						element.closest('.error-container').append(error);
					},
					rules: {
						reseller_date: {
							required: '#reseller_paid:checked'
						}
					},
					submitHandler: function(f) {
						f.submit();
					}
				});

				form.find('input[name="reseller_date"]').datetimepicker({
					format: 'YYYY-MM-DD'
				});

				form.on('click', '.trigger-close', function(e){
					e.preventDefault();
					window.parent.$.magnificPopup.close();
				});

				form.on('blur', '.update-comission-trigger', function(e){
					var f = parseFloat( form.find('.fixed-commission-input').val() );
					if ( isNaN(f) ) {
						f = 0;
					}
					var v = parseFloat( form.find('.variable-commission-input').val() );
					if ( isNaN(v) ) {
						v = 0;
					}
					var t = f + ( amount * v / 100 );
					form.find('.total-commission-input').val( t );
				});

				form.find('select[name="reseller_id"]').on('change', function(e){
					if ( $(this).val() ) {
						form.find('.reseller-rel').show();
					} else {
						form.find('.reseller-rel').hide().find('.reseller-input-rel').val('').prop('checked',false);
						form.find('.update-comission-trigger').eq(0).trigger('blur');
					}
				}).trigger('change').select2();

			});
		</script>

	@endif

@endsection