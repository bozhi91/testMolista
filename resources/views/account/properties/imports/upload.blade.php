@extends('layouts.account')

@section('account_content')

	<div id="properties-imports">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/properties.imports.h1') }}</h1>

		<div class="row">
			<div class="col-xs-12 col-sm-6">
				{!! Lang::get('account/properties.imports.intro') !!}
			</div>
			<div class="col-xs-12 col-sm-6">
				{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\Properties\ImportsController@postUpload', 'id'=>'upload-form', 'files'=>true ]) !!}
					<div class="form-group">
						<div class="error-container">
							{!! Form::label('file', Lang::get('account/properties.imports.file')) !!}
							{!! Form::file('file', [ 'class'=>'form-control required' ]); !!}
						</div>
						<div class="help-block"><a href="{{ action('Account\Properties\ImportsController@getSample', $current_version) }}" target="_blank">{{ Lang::get('account/properties.imports.version.sample') }}</a></div>
					</div>
					<div class="form-group">
						<div class="text-right hidden-xs">
							<a href="{{ action('Account\Properties\ImportsController@getIndex') }}" class="btn btn-default">{{ Lang::get('general.back') }}</a>
							{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-custom btn-primary' ]) !!}
						</div>
						<div class="visible-xs">
							{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-custom btn-primary btn-block' ]) !!}
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>

		<div class="instructions">
			<hr />
			<h3>{{ Lang::get('account/properties.imports.csv.instructions.title') }}</h3>
			<div class="intro" style="padding-bottom: 30px; font-size: 16px;">
				{!! Lang::get('account/properties.imports.csv.instructions.intro') !!}
			</div>
			<div class="fields" style="font-size: 11px;">
				<table class="table">
					<thead>
						<tr>
							<th>{{ Lang::get('account/properties.imports.csv.instructions.column') }}</th>
							<th>{{ Lang::get('account/properties.imports.csv.instructions.type') }}</th>
							<th class="text-center">{{ Lang::get('account/properties.imports.csv.instructions.required') }}</th>
							<th>{{ Lang::get('account/properties.imports.csv.instructions.options') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($csv_columns as $k => $v)
							<tr>
								<td>{{ $v['title'] }}</td>
								<td style="text-transform: capitalize;">{{ $v['type'] }}</td>
								<td class="text-center"><span class="glyphicon glyphicon-{{ $v['required'] ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
								<td>
									@if ( @$v['options'] && is_array($v['options']) )
										{!! implode('<br />', $v['options']) !!}
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {

			var form = $('#upload-form');

			form.validate({
				rules: {
					file: {
						extension: 'csv'
					}
				},
				messages: {
					file: {
						extension: "{{ print_js_string(Lang::get('account/properties.imports.file.error.type')) }}"
					}
				},
				errorPlacement: function(error, elem) {
				    elem.closest('div.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

		});
	</script>
@endsection
