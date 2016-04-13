@extends('layouts.admin')

@section('content')

	<div class="container">

		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('title', Input::get('title'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/properties.title') ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				<h1 class="list-title">{{ Lang::get('admin/menu.properties') }}</h1>

				@if ( count($properties) < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/properties.empty') }}</div>

				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>{{ Lang::get('admin/properties.title') }}</th>
								<th>{{ Lang::get('admin/properties.location') }}</th>
								<th>{{ Lang::get('admin/properties.site') }}</th>
								<th class="text-center">{{ Lang::get('admin/properties.enabled') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($properties as $property)
								<tr>
									<td>{{ $property->id }}</td>
									<td>{{ $property->title }}</td>
									<td>{{ @$property->city->name }}</td>
									<td>
										@if ( $property->site )
											{{ $property->site->title }}
											@if ( Auth::user()->can('site-edit') )
												<a href="{{ action('Admin\SitesController@edit',$property->site->id) }}" class="list-link"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
											@endif
										@else
											{{ Lang::get('admin/properties.owner.none') }}
										@endif
									</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right">
										<a href="{{ action('Admin\Properties\BaseController@show', $property->id) }}" class="btn btn-xs btn-default" target="_blank">{{ Lang::get('general.view') }}</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($properties, Input::only('limit','title')) !!}

				@endif

			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
