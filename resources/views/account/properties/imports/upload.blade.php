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
				}
			});

		});
	</script>
@endsection
