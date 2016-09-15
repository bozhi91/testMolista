@extends('layouts.admin')

@section('content')

	<div class="container">
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
					<table class="table table-striped">
						<thead>
							<tr>
								{!! drawSortableHeaders(url()->full(), [
									'reseller' => [ 'title' => Lang::get('admin/resellers.payments.reseller'), 'sortable'=>false, ],
									'amount' => [ 'title' => Lang::get('admin/resellers.payments.amount'), 'sortable'=>false, ],
									'created' => [ 'title' => Lang::get('admin/resellers.payments.created'), 'sortable'=>false, ],
									'site' => [ 'title' => Lang::get('admin/resellers.payments.site'), 'sortable'=>false, ],
									'paid' => [ 'title' => Lang::get('admin/resellers.payments.paid'), 'class'=>'text-center', 'sortable'=>false, ],
									'action' => [ 'title' => '', 'sortable'=>false, ],
								]) !!}
							</tr>
						</thead>
						<tbody>
							@foreach ($payments as $payment)
								<tr>
									<td>
										@if ( $payment->reseller )
											{{ $payment->reseller->name }}
										@endif
									</td>
									<td>{{ price($payment->payment_amount, $payment->infocurrency) }}</td>
									<td>{{ $payment->created_at->format('d/m/Y') }}</td>
									<td>
										@if ( $payment->site )
											{{ $payment->site->main_url }}
										@endif
									</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $payment->reseller_paid ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right"><a href="{{ action('Admin\Resellers\PaymentsController@getShow', $payment->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.view') }}</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($payments, Input::except('page')) !!}
				@endif

			</div>
		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
