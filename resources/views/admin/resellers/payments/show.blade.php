<?php
	$payment_currency = $payment->infocurrency->toArray();
	$payment_currency['decimals'] = 2;
?>

@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/menu.resellers.payments') }}</h1>

		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.site')) !!}
					{!! Form::text(null, $payment->site->main_url, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.plan')) !!}
					{!! Form::text(null, "{$payment->plan->name} ({$payment->plan->currency})", [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.amount')) !!}
					{!! Form::text(null, price($payment->payment_amount, $payment->infocurrency), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.amount').' (EUR)') !!}
					{!! Form::text(null, price($payment->payment_amount*$payment->payment_rate), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.paid.from')) !!}
					{!! Form::text(null, $payment->paid_from->format("d/m/Y"), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.paid.until')) !!}
					{!! Form::text(null, $payment->paid_until->format("d/m/Y"), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
		</div>

		<h2>{{ Lang::get('admin/resellers.payments.reseller') }}</h2>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.reseller.name')) !!}
					{!! Form::text(null, $payment->reseller->name, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.reseller.email')) !!}
					{!! Form::text(null, $payment->reseller->email, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.reseller.fixed')) !!}
					{!! Form::text(null, price($payment->reseller_fixed, $payment_currency), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.reseller.variable')) !!}
					{!! Form::text(null, "{$payment->reseller_variable}%", [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-4">
				<div class="form-group error-container">
					{!! Form::label(null, Lang::get('admin/resellers.payments.reseller.total')) !!}
					{!! Form::text(null, price($payment->reseller_amount, $payment_currency), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				</div>
			</div>
		</div>

		@if ( $payment->reseller_paid )
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label(null, Lang::get('admin/resellers.payments.paid')) !!}
						{!! Form::text(null, Lang::get('general.yes'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label(null, Lang::get('admin/resellers.payments.paid.date')) !!}
						{!! Form::text(null, $payment->reseller_date->format('d/m/Y'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
					</div>
				</div>
			</div>
		@else
			<br />
			{!! Form::model($payment, [ 'method'=>'POST', 'action'=>[ 'Admin\Resellers\PaymentsController@postPay', $payment->id ], 'id'=>'payment-form' ]) !!}
				<div class="error-container">
					<div class="form-inline">
						<div class="form-group" style="position: relative;">
							{!! Form::text('reseller_date', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('admin/resellers.payments.paid.date') ]) !!}
						</div>
						{!! Form::button(Lang::get('admin/resellers.payments.pay'), [ 'type'=>'submit', 'class'=>'btn btn-info']) !!}
					</div>
				</div>
			{!! Form::close() !!}
		@endif

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#payment-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					f.submit();
				}
			});

			form.find('input[name="reseller_date"]').datetimepicker({
				format: 'YYYY-MM-DD'
			});

		});
	</script>

@endsection