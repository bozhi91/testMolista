@extends('layouts.account')

@section('account_content')

	<div id="admin-properties" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			@if ( Auth::user()->can('property-create') && Auth::user()->canProperty('create') )
				<div class="pull-right">
					<a href="{{ action('Account\PropertiesController@create') }}" class="btn btn-primary">{{ Lang::get('account/properties.button.new') }}</a>
				</div>
			@endif

			<h1 class="page-title">{{ Lang::get('account/properties.h1') }}</h1>

			@if ( count($properties) < 1)
				<div class="alert alert-info">{{ Lang::get('account/properties.empty') }}</div>
			@else
				<table class="table table-striped">
					<thead>
						<tr>
							<th>{{ Lang::get('account/properties.column.title') }}</th>
							<th>{{ Lang::get('account/properties.column.location') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($properties as $property)
							<tr>
								<td>{{ $property->title }}</td>
								<td>{{ $property->city->name }} / {{ $property->state->name }}</td>
								<td class="text-right text-nowrap">
									{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\PropertiesController@destroy', $property->slug] ]) !!}
										<a href="{{ action('Account\PropertiesController@show', $property->slug) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.view') }}</a>
										@if ( Auth::user()->can('property-edit') && Auth::user()->canProperty('edit') )
											<a href="{{ action('Account\PropertiesController@edit', $property->slug) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>
										@endif
										@if ( Auth::user()->can('property-delete') && Auth::user()->canProperty('delete') )
											<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('general.delete') }}</button>
										@endif
									{!! Form::close() !!}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
                {!! drawPagination($properties, Input::only('limit','title')) !!}
			@endif

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-properties');

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.delete') ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});

		});
	</script>

@endsection