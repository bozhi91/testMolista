@extends('layouts.admin')

@section('content')

	<div class="container" id="marketplaces-list">
		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/marketplaces.title') ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block' ]) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				@permission('marketplace-create')
					<a href="{{ action('Admin\MarketplacesController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
				@endpermission

				<h1 class="list-title">{{ Lang::get('admin/menu.marketplaces') }}</h1>

				@if ( $marketplaces->count() < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/marketplaces.empty') }}</div>
				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>{{ Lang::get('admin/marketplaces.code') }}</th>
								<th>{{ Lang::get('admin/marketplaces.title') }}</th>
								<th>{{ Lang::get('admin/marketplaces.created') }}</th>
								<th class="text-center">{{ Lang::get('admin/marketplaces.enabled') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($marketplaces as $marketplace)
								<tr>
									<td>{{ $marketplace->code }}</td>
									<td>{{ $marketplace->name }}</td>
									<td>{{ $marketplace->created_at->format('d/m/Y') }}</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $marketplace->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right">
										@if ( Auth::user()->can('marketplace-edit') )
											<a href="{{ action('Admin\MarketplacesController@edit', $marketplace->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($marketplaces, Input::only('limit','name')) !!}
				@endif
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#marketplaces-list');
		});
	</script>

@endsection
