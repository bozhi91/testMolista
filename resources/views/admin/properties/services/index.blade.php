@extends('layouts.admin')

@section('content')

	<div class="container">
		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('code', Input::get('code'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/properties/services.code') ]) !!}</p>
					<p>{!! Form::text('title', Input::get('title'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/properties/services.name') ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				<a href="{{ action('Admin\Properties\ServicesController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>

				<h1 class="list-title">{{ Lang::get('admin/menu.properties') }} / {{ Lang::get('admin/menu.services') }}</h1>

				@if ( count($services) < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/properties/services.empty') }}</div>

				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>{{ Lang::get('admin/properties/services.code') }}</th>
								<th>{{ Lang::get('admin/properties/services.name') }}</th>
								<th class="text-center">{{ Lang::get('admin/properties/services.icon') }}</th>
								<th class="text-center">{{ Lang::get('admin/properties/services.enabled') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($services as $service)
								<tr>
									<td>{{ $service->id }}</td>
									<td>{{ $service->code }}</td>
									<td>{{ $service->title }}</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $service->icon ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $service->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right">
									<a href="{{ action('Admin\Properties\ServicesController@edit', $service->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($services, Input::only('limit','title')) !!}
				@endif
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
