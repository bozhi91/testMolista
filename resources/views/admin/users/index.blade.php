@extends('layouts.admin')

@section('content')

	<div class="container" id="users-list">
		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/users.name') ]) !!}</p>
					<p>{!! Form::text('email', Input::get('email'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/users.email') ]) !!}</p>
					<p>{!! Form::select('role', [ ''=>Lang::get('admin/users.role')]+$roles->toArray(), Input::get('role'), [ 'class'=>'form-control' ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				@permission('user-create')
					<a href="{{ action('Admin\UsersController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
				@endpermission

				<h1 class="list-title">{{ Lang::get('admin/menu.users') }}</h1>

				@if ( count($users) < 1)
					<div class="alert alert-info" role="alert">{{ Lang::get('admin/users.empty') }}</div>
				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>{{ Lang::get('admin/users.name') }}</th>
								<th>{{ Lang::get('admin/users.email') }}</th>
								<th>{{ Lang::get('admin/users.role') }}</th>
								<th>{{ Lang::get('admin/users.site') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($users as $user)
								<tr>
									<td>{{ $user->id }}</td>
									<td>{{ $user->name }}</td>
									<td>{{ $user->email }}</td>
									<td>{{ $user->roles->implode('display_name', ', ') }}</td>
									<td>{{ $user->sites->implode('main_url', ', ') }}</td>
									<td class="text-right">
										@if ( Auth::user()->can('user-edit') )
											<a href="{{ action('Admin\UsersController@edit', $user->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($users, Input::except('page'), action('Admin\UsersController@index', array_merge(Input::except('page','limit'), ['csv'=>1]))) !!}
				@endif
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#users-list');
		});
	</script>

@endsection
