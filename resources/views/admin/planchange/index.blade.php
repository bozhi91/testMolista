@extends('layouts.admin')

@section('content')

	<div class="container" id="planchange-list">

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::select('plan_id', [ ''=>Lang::get('admin/planchange.plan') ]+$plans, Input::get('plan_id'), [ 'class'=>'form-control' ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				<h1 class="list-title">{{ Lang::get('admin/menu.planchange') }}</h1>

				@if ( $planchanges->count() < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/planchange.empty') }}</div>
				@else
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>{{ Lang::get('admin/planchange.site') }}</th>
									<th>{{ Lang::get('admin/planchange.plan') }}</th>
									<th>{{ Lang::get('admin/planchange.payment.interval') }}</th>
									<th>{{ Lang::get('admin/planchange.payment.method') }}</th>
									<th>{{ Lang::get('admin/planchange.status') }}</th>
									<th>{{ Lang::get('admin/planchange.created') }}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($planchanges as $planchange)
									<tr>
										<td>{{ $planchange->site->main_url }}</td>
										<td>{{ $planchange->plan->name }}</td>
										<td>{{ Lang::get("web/plans.price.{$planchange->payment_interval}") }}</td>
										<td>{{ Lang::get("account/payment.method.{$planchange->payment_method}") }}</td>
										<td class="text-capitalize">{{ $planchange->status }}</td>
										<td>{{ $planchange->created_at->format('d/m/Y') }}</td>
										<td class="text-right">
											@if ( Auth::user()->can('site-edit') )
												<a href="{{ action('Admin\PlanchangeController@getEdit', $planchange->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.view') }}</a>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					{!! drawPagination($planchanges, Input::only('limit','title')) !!}
				@endif
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#planchange-list');
		});
	</script>

@endsection
