@extends('layouts.popup')

@section('content')

	<div style="padding: 20px;">

		<h2 class="page-title">{{ Lang::get('account/properties.show.property.catch.edit') }}</h2>

		{!! Form::open([ 'action'=>[ 'Account\PropertiesController@postCatch', $property->id, @$item->id ], 'method'=>'POST', 'id'=>'catch-form' ]) !!}
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('seller_first_name', Lang::get('account/properties.show.property.seller.name.first') ) !!}
						{!! Form::text('seller_first_name', @$item->seller_first_name, [ 'class'=>'form-control required', ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('seller_last_name', Lang::get('account/properties.show.property.seller.name.last') ) !!}
						{!! Form::text('seller_last_name', @$item->seller_last_name, [ 'class'=>'form-control required', ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('seller_email', Lang::get('account/properties.show.property.seller.email') ) !!}
						{!! Form::text('seller_email', @$item->seller_email, [ 'class'=>'form-control email required', ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('seller_id_card', Lang::get('account/properties.show.property.seller.id') ) !!}
						{!! Form::text('seller_id_card', @$item->seller_id_card, [ 'class'=>'form-control', ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('seller_phone', Lang::get('account/properties.show.property.seller.phone') ) !!}
						{!! Form::text('seller_phone', @$item->seller_phone, [ 'class'=>'form-control', ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('seller_cell', Lang::get('account/properties.show.property.seller.cell') ) !!}
						{!! Form::text('seller_cell', @$item->seller_cell, [ 'class'=>'form-control', ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<div class="form-group error-container">
						{!! Form::label('price_min', Lang::get('account/properties.show.property.price.min') ) !!}
						<div class="input-group">
							{!! Form::text('price_min', @$item->price_min, [ 'class'=>'form-control required number', 'min'=>1 ]) !!}
							<div class="input-group-addon">{{ price_symbol($property->currency) }}</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-3">
					<div class="form-group error-container">
						{!! Form::label('commission', Lang::get('account/properties.show.property.commission') ) !!}
						{!! Form::select('commission', percent_array(), @$item->commission, [ 'class'=>'form-control required', ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<label>&nbsp;</label>
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