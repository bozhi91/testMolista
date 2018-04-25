@extends('layouts.admin')

@section('content')

	<div class="container" id="sites-list">
		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('domain', Input::get('domain'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/sites.domain') ]) !!}</p>
					<p>{!! Form::select('theme', [ ''=>Lang::get('admin/sites.theme') ] + $themes, Input::get('theme'), [ 'class'=>'form-control' ]) !!}</p>
					<p>{!! Form::select('transfer', [
						'' => Lang::get('admin/sites.transfer'),
						1 => Lang::get('general.no'),
						2 => Lang::get('general.yes'),
					], Input::get('transfer'), [ 'class'=>'form-control' ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				@if ( false && Auth::user()->can('site-create') )
					<a href="{{ action('Admin\SitesController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
				@endif

				<h1 class="list-title">{{ Lang::get('admin/menu.sites') }}</h1>

				@if ( count($sites) < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/sites.empty') }}</div>
				@else
					<table class="table table-striped">
						<thead>
							<tr>
								{!! drawSortableHeaders(url()->full(), [
									'id' => [ 'title' => '#' ],
									'domain' => [ 'title' => Lang::get('admin/sites.domain'), 'sortable'=>false ],
									'plan' => [ 'title' => Lang::get('admin/sites.tab.plan') ],
									'country' => [ 'title' => Lang::get('admin/sites.country'), 'sortable'=>false ],
									'theme' => [ 'title' => Lang::get('admin/sites.theme') ],
									'properties' => [ 'title' => Lang::get('admin/sites.properties'), 'class'=>'text-center' ],
									'users' => [ 'title' => Lang::get('admin/sites.employees'), 'class'=>'text-center' ],
									'transfer' => [ 'title' => Lang::get('admin/sites.transfer'), 'class'=>'text-center text-nowrap' ],
									'created' => [ 'title' => Lang::get('admin/sites.created') ],
									'action' => [ 'title' => '', 'sortable'=>false ],
								]) !!}
							</tr>
						</thead>
						<tbody>
							@foreach ($sites as $site)
								<tr>
									<td>{{ $site->id }}</td>
									<td>{{ $site->main_url }}</td>
									<td>{{ @$site->plan->name }}</td>
									<td>{{ $site->country->name }}</td>
									<td>{{ @$themes[$site->theme] }}</td>
									<td class="text-center">{{ number_format($site->properties->count(),0,',','.') }}</td>
									<td class="text-center">{{ number_format($site->users->count(),0,',','.') }}</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $site->web_transfer_requested ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td>{{ $site->created_at->format('d/m/Y') }}</td>
									<td class="text-right">
										@if ( Auth::user()->can('site-edit') )
											<a href="{{ action('Admin\SitesController@edit', $site->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.view') }}</a>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($sites, Input::except('page'), action('Admin\SitesController@index', array_merge(Input::except('page','limit'), ['csv'=>1]))) !!}
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
