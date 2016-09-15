@extends('layouts.admin')

@section('content')

	<div class="container" id="expirations-list">

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('domain', Input::get('domain'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/expirations.site') ]) !!}</p>
					<p>{!! Form::select('plan_id', [ ''=>Lang::get('admin/expirations.plan') ]+$plans, Input::get('plan_id'), [ 'class'=>'form-control' ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				<h1 class="list-title">{{ Lang::get('admin/menu.expirations') }}</h1>

				@if ( $expirations->count() < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/expirations.empty') }}</div>
				@else
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>{{ Lang::get('admin/expirations.site') }}</th>
									<th>{{ Lang::get('admin/expirations.plan') }}</th>
									<th>{{ Lang::get('admin/expirations.payment.interval') }}</th>
									<th>{{ Lang::get('admin/expirations.payment.method') }}</th>
									<th class="text-nowrap">{{ Lang::get('admin/expirations.paid.until') }}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($expirations as $expiration)
									<tr>
										<td>{{ $expiration->main_url }}</td>
										<td>{{ $expiration->plan->name }}</td>
										<td>{{ Lang::get("web/plans.price.{$expiration->payment_interval}") }}</td>
										<td>{{ Lang::get("account/payment.method.{$expiration->payment_method}") }}</td>
										<td>{{ date("d/m/Y", strtotime($expiration->paid_until)) }}</td>
										<td class="text-right">
											@if ( $expiration->payment_method == 'transfer' )
												<a href="{{ action('Admin\ExpirationsController@getExtend', $expiration->id) }}" class="btn btn-xs btn-default">{{ Lang::get('admin/expirations.site.extend') }}</a>
											@endif
											@if ( Auth::user()->can('site-edit') )
												<a href="{{ action('Admin\SitesController@edit', $expiration->id) }}" class="btn btn-xs btn-default">{{ Lang::get('admin/expirations.site.view') }}</a>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					{!! drawPagination($expirations, Input::except('page'), action('Admin\ExpirationsController@getIndex', array_merge(Input::except('page','limit'), ['csv'=>1]))) !!}
				@endif
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#expirations-list');
		});
	</script>

@endsection
