<?php
	$payment_currency = $payment->infocurrency->toArray();
	$payment_currency['decimals'] = 2;
?>

@extends('layouts.popup')

@section('content')

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
						{!! Form::text(null, $payment->paid_from->format("d/m/Y"), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-4">
					<div class="form-group error-container">
						{!! Form::label(null, Lang::get('admin/resellers.payments.paid.until')) !!}
						{!! Form::text(null, $payment->paid_until->format("d/m/Y"), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('reseller_id', Lang::get('admin/resellers.payments.reseller')) !!}
						{!! Form::select('reseller_id', [''=>'&nbsp;']+$resellers, null, [ 'class'=>'form-control has-select-2', ]) !!}
					</div>
				</div>
			</div>

			<div class="reseller-rel" {{ $payment->reseller_id ? '' : 'style=""display: none;' }}>

				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('reseller_fixed', Lang::get('admin/resellers.payments.reseller.fixed')) !!}
							<div class="input-group">
								{!! Form::text('reseller_fixed', null, [ 'class'=>'form-control number update-comission-trigger fixed-commission-input', 'min'=>'0', ]) !!}
								<div class="input-group-addon">{{ $payment->payment_currency }}</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('reseller_variable', Lang::get('admin/resellers.payments.reseller.variable')) !!}
							<div class="input-group plan-input-group">
								{!! Form::text('reseller_variable', null, [ 'class'=>'form-control number update-comission-trigger variable-commission-input', 'min'=>'0', 'max'=>100, ]) !!}
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
									{!! Form::checkbox('reseller_paid', 1, null) !!}
									{{ Lang::get('admin/resellers.payments.paid') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group">
							{!! Form::label('reseller_date', Lang::get('admin/resellers.payments.paid.date')) !!}
							<div class="error-container" style="position: relative;">
								{!! Form::text('reseller_date', null, [ 'class'=>'form-control' ]) !!}
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
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					f.submit();
				}
			});

			form.find('.has-select-2').select2();

			form.find('input[name="reseller_date"]').datetimepicker({
				format: 'YYYY-MM-DD'
			});

			form.on('click', '.trigger-close', function(e){
				e.preventDefault();
				//window.parent.payments_reload();
				window.parent.$.magnificPopup.close();
			});

			form.on('change', 'select[name="reseller_id"]', function(e){
				if ( $(this).val() ) {
					form.find('.reseller-rel').show();
				} else {
					form.find('.reseller-rel').hide();
				}
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

		});
	</script>

@endsection