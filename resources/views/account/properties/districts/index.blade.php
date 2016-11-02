@extends('layouts.account')

@section('account_content')

	<div id="districts">

		@include('common.messages', [ 'dismissible'=>true ])

		<a href="{{ action(('Account\Properties\DistrictsController@getCreate')) }}"
		   class="btn btn-primary pull-right">{{ Lang::get('account/properties.districts.create') }}</a>

		<h1 class="page-title">{{ Lang::get('account/properties.districts.h1') }}</h1>
		
		@if (count($districts) < 1)
			<div class="alert alert-info">{{ Lang::get('account/properties.districts.empty') }}</div>
		@else
		<table class="table table-striped">
			<thead>
				<tr>
					<th>{{ Lang::get('account/properties.districts.name') }}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($districts as $district)
				<tr>
					<td>{{ $district->name }}</td>
					<td class="text-right text-nowrap">
						{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\Properties\DistrictsController@delete', $district->id] ]) !!}
						<a href="{{ action('Account\Properties\DistrictsController@getEdit', $district->id) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>
						<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('general.delete') }}</button>
						{!! Form::close() !!}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		{!! drawPagination($districts, Input::except('page')) !!}
		@endif		
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#districts');

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.districts.delete') ) }}", function (e) {
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
