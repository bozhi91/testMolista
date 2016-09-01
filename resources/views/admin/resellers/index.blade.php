@extends('layouts.admin')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-sm-3 hidden-xs">

				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('ref', Input::get('ref'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/resellers.ref') ]) !!}</p>
					<p>{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/resellers.name') ]) !!}</p>
					<p>{!! Form::text('email', Input::get('email'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/resellers.email') ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}

			</div>
			<div class="col-xs-12 col-sm-9">

				@if ( Auth::user()->can('reseller-create') )
					<a href="{{ action('Admin\ResellersController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
				@endif

				<h1 class="list-title">{{ Lang::get('admin/menu.resellers') }}</h1>

				@if ( $resellers->count() < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/resellers.empty') }}</div>
				@else
					<table class="table table-striped">
						<thead>
							<tr>
								{!! drawSortableHeaders(url()->full(), [
									'ref' => [ 'title' => Lang::get('admin/resellers.ref'), ],
									'name' => [ 'title' => Lang::get('admin/resellers.name'), ],
									'email' => [ 'title' => Lang::get('admin/resellers.email'), ],
									'enabled' => [ 'title' => Lang::get('admin/resellers.enabled'),'class'=>'text-center', ],
									'action' => [ 'title' => '', 'sortable'=>false, ],
								]) !!}
							</tr>
						</thead>
						<tbody>
							@foreach ($resellers as $reseller)
								<tr>
									<td>{{ $reseller->ref }}</td>
									<td>{{ $reseller->name }}</td>
									<td>{{ $reseller->email }}</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $reseller->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right">
										@permission('reseller-edit')
											<a href="{{ action('Admin\ResellersController@edit', $reseller->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
										@endpermission
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($resellers, Input::except('page')) !!}
				@endif

			</div>
		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
