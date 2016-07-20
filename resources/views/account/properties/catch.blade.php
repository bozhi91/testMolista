@extends('layouts.popup')

@section('content')

	<div style="padding: 20px;">

		<h2 class="page-title">{{ Lang::get('account/properties.show.property.catch.edit') }}</h2>

		{!! Form::open([ 'action'=>[ 'Account\PropertiesController@postCatch', $property->id, @$item->id ], 'method'=>'POST', 'id'=>'catch-form' ]) !!}
			@include('account.properties.catch-form', [
				'item' => @$item,
				'price_symbol' => $property->infocurrency->symbol,
				'price_position' => $property->infocurrency->position,
			])
			<div class="row">
				<div class="col-xs-12">
					<div class="buttons-area text-right">
						{!! Form::button(Lang::get('general.cancel'), [ 'class'=>'btn btn-default btn-cancel-trigger' ]) !!}
						{!! Form::button(Lang::get('general.continue'), [ 'class'=>'btn btn-primary', 'type'=>'submit' ]) !!}
					</div>
				</div>
			</div>
		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#catch-form');

			form.on('click', '.btn-cancel-trigger', function(e){
				e.preventDefault();
				window.parent.$.magnificPopup.close();
			});
			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: form.attr('action'),
						data: form.serialize(),
						success: function(data) {
							LOADING.hide();
							if ( data.success ) {
								alertify.success("{{ print_js_string( Lang::get('account/properties.show.property.catch.success') ) }}");
								window.parent.location.reload(true);
								form.find('.buttons-area').hide();
							} else {
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						},
						error: function() {
							LOADING.hide();
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					});
				}
			});

		});
	</script>

@endsection