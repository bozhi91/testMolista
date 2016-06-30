@extends('layouts.admin')

@section('content')

	<div class="container">

		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
				{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
				<h4>{{ Lang::get('general.filters') }}</h4>
				<p>{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/config/plans.name') ]) !!}</p>
				<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">
				@permission('pack-create')
				<a href="{{ action('Admin\Config\PlansController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
				@endpermission

				<h1 class="list-title">{{ Lang::get('admin/menu.plans') }}</h1>

				@if ( count($plans) < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/config/plans.empty') }}</div>
				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>{{ Lang::get('admin/config/plans.name') }}</th>
								<th class="text-right">{{ Lang::get('admin/config/plans.price') }}</th>
								<th class="text-center">{{ Lang::get('admin/config/plans.enabled') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($plans as $plan)
								<tr>
									<td>{{ $plan->code }}</td>
									<td>{{ $plan->name }}</td>
									<td class="text-right">
										@if ( $plan->is_free )
											{{ Lang::get('admin/config/plans.free') }}
										@else
											{{ price($plan->price_month,'EUR') }} / {{ price($plan->price_year,'EUR') }}
										@endif
									</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $plan->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right">
										@permission('pack-edit')
											<a href="{{ action('Admin\Config\PlansController@edit', $plan->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
										@endpermission
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($plans, Input::only('limit','name')) !!}
				@endif
			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
