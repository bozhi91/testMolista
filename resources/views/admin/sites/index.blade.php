@extends('layouts.admin')

@section('content')

	<div class="container" id="sites-list">
		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('title', Input::get('title'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin\sites.title') ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				@permission('site-create')
					<a href="{{ action('Admin\SitesController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
				@endpermission

				<h1 class="list-title">{{ Lang::get('admin/menu.sites') }}</h1>

				@if ( count($sites) < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/sites.empty') }}</div>
				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>{{ Lang::get('admin\sites.title') }}</th>
								<th>{{ Lang::get('admin\sites.created') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($sites as $site)
								<tr>
									<td>{{ $site->id }}</td>
									<td>{{ $site->title }}</td>
									<td>{{ $site->created_at->format('d/m/Y') }}</td>
									<td class="text-right">
										@if ( Auth::user()->can('site-edit') )
											<a href="{{ action('Admin\SitesController@edit', $site->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($sites, Input::only('limit','title')) !!}
				@endif
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#sites-list');
		});
	</script>

@endsection
