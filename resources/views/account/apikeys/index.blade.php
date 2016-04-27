@extends('layouts.account')

@section('account_content')

	<div id="apikeys" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			@if ( count($api_keys) < Config::get('app.apikeys_max_per_user', 10) )
				<div class="apikey-row pull-right">
					{!! Form::open([ 'method'=>'POST', 'class'=>'edit-form', 'action'=>['Account\ApiKeysController@postStore'] ]) !!}
						{!! Form::hidden('name', null) !!}
						{!! Form::button( Lang::get('account/apikeys.create'), [ 'class'=>'btn btn-default edit-trigger']) !!}
					{!! Form::close() !!}
				</div>
			@endif

			<h1>{{ Lang::get('account/apikeys.title') }}</h1>

			@if ( count($api_keys) > 0 )
				<table class="table table-striped">
					<thead>
						<tr>
							<th>{{ Lang::get('account/apikeys.name') }}</th>
							<th>{{ Lang::get('account/apikeys.key') }}</th>
							<th>{{ Lang::get('account/apikeys.created') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($api_keys as $api_key)
							<tr class="apikey-row">
								<td>{{ $api_key->name }}</td>
								<td>{{ $api_key->key }}</td>
								<td>{{ date("d/m/Y", strtotime($api_key->created_at) ) }}</td>
								<td class="text-right text-nowrap">
									{!! Form::open([ 'method'=>'POST', 'class'=>'delete-form', 'action'=>['Account\ApiKeysController@postDelete', $api_key->id] ]) !!}
										<a href="#" class="btn btn-default btn-xs edit-trigger">{{ Lang::get('general.edit') }}</a>
										<button type="submit" class="btn btn-default btn-xs">{{ Lang::get('general.delete') }}</button>
									{!! Form::close() !!}
									{!! Form::open([ 'method'=>'POST', 'class'=>'edit-form', 'action'=>['Account\ApiKeysController@postStore', $api_key->id] ]) !!}
										{!! Form::hidden('name', $api_key->name) !!}
									{!! Form::close() !!}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			@endif

			<p>{{ Lang::get('account/apikeys.available', [ 'CREATED'=>count($api_keys), 'TOTAL'=>Config::get('app.apikeys_max_per_user', 10) ]) }}</p>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#apikeys');

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('account/apikeys.delete') ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});

			// Edit create modal
			cont.on('click', '.edit-trigger', function(e){
				e.preventDefault();

				var form = $(this).closest('.apikey-row').find('.edit-form');

				SITECOMMON.prompt("{{ print_js_string( Lang::get('account/apikeys.name') ) }}", function (e, str) {
					if (e) {
						LOADING.show();
						form.find('input[name="name"]').val(str);
						form.submit();
					}
				}, form.find('input[name="name"]').val() );
			});

		});
	</script>

@endsection