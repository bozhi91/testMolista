@extends('layouts.admin')

@section('content')

	<style media="screen">
		.table-total { font-size: 18px; font-weight: bold; }
	</style>

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="row">
			<div class="col-sm-3 hidden-xs">

				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('reseller', Input::get('reseller'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/resellers.payments.reseller') ]) !!}</p>
					<p>{!! Form::text('site', Input::get('site'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/resellers.payments.site') ]) !!}</p>
					<p>{!! Form::select('paid', [
						0 => Lang::get('admin/resellers.payments.paid'),
						1 => Lang::get('general.no'),
						2 => Lang::get('general.yes'),
					], Input::get('paid'), [ 'class'=>'form-control' ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}

			</div>
			<div class="col-xs-12 col-sm-9">

				<h1 class="list-title">{{ Lang::get('admin/menu.resellers.payments') }}</h1>

				@if ( $payments->count() < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/resellers.payments.empty') }}</div>
				@else

					{!! Form::model(null, [ 'method'=>'POST', 'action'=>[ 'Admin\Resellers\PaymentsController@postPayBatch' ], 'id'=>'payment-form' ]) !!}
					<table class="table table-striped">
						<thead>
							<tr>
								<th><input type="checkbox" class="check-all" /></th>
								{!! drawSortableHeaders(url()->full(), [
									'reseller' => [ 'title' => Lang::get('admin/resellers.payments.reseller'), 'sortable'=>false, ],
									'created' => [ 'title' => Lang::get('admin/resellers.payments.created'), 'sortable'=>false, ],
									'site' => [ 'title' => Lang::get('admin/resellers.payments.site'), 'sortable'=>false, ],
									'amount_pending' => [ 'title' => Lang::get('admin/resellers.payments.amount_pending'), 'sortable'=>false, ],
									'amount_paid' => [ 'title' => Lang::get('admin/resellers.payments.amount_paid'), 'sortable'=>false, ],
									'paid' => [ 'title' => Lang::get('admin/resellers.payments.paid'), 'class'=>'text-center', 'sortable'=>false, ],
									'action' => [ 'title' => '', 'sortable'=>false, ],
								]) !!}
							</tr>
						</thead>
						<tbody>
							@foreach ($payments as $payment)
								<tr>
									<td>
										@if (!$payment->reseller_paid)
										<input type="checkbox" name="payments[]" value="{{ $payment->id }}" />
										@endif
									</td>
									<td>
										@if ( $payment->reseller )
											{{ $payment->reseller->name }}
										@endif
									</td>
									<td>{{ $payment->created_at->format('d/m/Y') }}</td>
									<td>
										@if ( $payment->site )
											{{ $payment->site->main_url }}
										@endif
									</td>
									<td class="text-right">{{ !$payment->reseller_paid ? price($payment->reseller_amount * $payment->reseller_rate, $comissions_currency) : '' }}</td>
									<td class="text-right">{{ $payment->reseller_paid ? price($payment->reseller_amount * $payment->reseller_rate, $comissions_currency) : '' }}</td>
									<td class="text-center">{{ $payment->reseller_paid ? $payment->reseller_date->format('d/m/Y') : '' }}</td>
									<td class="text-right"><a href="{{ action('Admin\Resellers\PaymentsController@getShow', $payment->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.view') }}</a></td>
								</tr>
							@endforeach
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="text-right table-total">{{ price($payments->where('reseller_paid', 0)->sum(function($item){ return $item->reseller_amount * $item->reseller_rate; }), $comissions_currency) }}</td>
									<td class="text-right table-total">{{ price($payments->where('reseller_paid', 1)->sum(function($item){ return $item->reseller_amount * $item->reseller_rate; }), $comissions_currency) }}</td>
									<td></td>
									<td></td>
								</tr>
						</tbody>
					</table>

					<div class="error-container">
						<div class="form-inline">
							<div class="form-group" style="position: relative;">
								{!! Form::text('reseller_date', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('admin/resellers.payments.paid.date') ]) !!}
							</div>
							{!! Form::button(Lang::get('admin/resellers.payments.pay'), [ 'type'=>'submit', 'class'=>'btn btn-info']) !!}
						</div>
					</div>
					{!! Form::close() !!}

					{!! drawPagination($payments, Input::except('page')) !!}
				@endif

			</div>
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
					LOADING.show();
					f.submit();
				}
			});

			form.find('input[name="reseller_date"]').datetimepicker({
				format: 'YYYY-MM-DD'
			});

			form.on('click', '.check-all', function(){
				var checks = form.find('[name="payments\[\]"]');

				//if (checks.length != checks.filter(':checked').length) {
				if (this.checked) {
					checks.prop('checked', true);
				} else {
					checks.prop('checked', false);
				}
			});
		});
	</script>

@endsection
