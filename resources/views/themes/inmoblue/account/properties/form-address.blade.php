<?php
	$street_types = array_unique([
		'Avenida','Acceso','Agregado','Aldea','Alameda','Andador','Area','Arrabal','Arroyo','Autopista',
		'Bajada','Bloque','Barranco','Barranquil','Barrio','Bulevar',
		'Caleya','Calleja','Callejón','Callizo','Camino','Carmen','Campa','Campo','Cañada','Caserío','Chalet','Cinturón','Colegio','Cigarral','Colonia','Concejo','Colegio','Conjunto','Cuesta','Costanilla','Continuación','Carretera','Calle','Carrer',
		'Destrás','Diputación','Diseminados',
		'Edificios','Entrada','Ensanche','Escalinata','Espalda','Explanada','Extramuros','Extrarradio',
		'Ferrocarril','Finca',
		'Glorieta','Gran vía','Grupo',
		'Huerta','Huerto',
		'Jardines',
		'Lado','Ladera','Lago','Lugar',
		'Malecón','Manzana','Masías','Mercado','Monte','Muelle','Municipio',
		'Páramo','Parroquia','Parque','Particular','Partida','Pasadizo','Pasaje','Pasadizo','Placeta','Poblado','Polígono','Prolongación','Puente','Plaza','Paseo',
		'Quinta',
		'Raconada','Ramal','Rambla','Rincón','Rincona','Rampa','Riera','Rúa',
		'Salida','Salón','Sextor','Senda','Solar','Subida',
		'Terrenos','Torrente','Travesía',
		'Urbanización',
		'Valle','Vereda','Vía','Viaducto','Vial',
	]);
	sort($street_types);
	$street_types = array_combine($street_types, $street_types);
?>

<div class="form-group error-container">
	{!! Form::label('address_parts[type]', Lang::get('account/properties.address.type')) !!}
	{!! Form::select('address_parts[type]', [''=>'']+$street_types, null, [ 'id'=>'address-parts-type', 'class'=>'form-control address-part-input' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('address_parts[street]', Lang::get('account/properties.address.street')) !!}
	{!! Form::text('address_parts[street]', null, [ 'id'=>'address-parts-street', 'class'=>'form-control address-part-input' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('address_parts[number]', Lang::get('account/properties.address.number')) !!}
	{!! Form::text('address_parts[number]', null, [ 'id'=>'address-parts-number', 'class'=>'form-control address-part-input' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('address_parts[block]', Lang::get('account/properties.address.block')) !!}
	{!! Form::text('address_parts[block]', null, [ 'id'=>'address-parts-block', 'class'=>'form-control' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('address_parts[stair]', Lang::get('account/properties.address.stair')) !!}
	{!! Form::text('address_parts[stair]', null, [ 'id'=>'address-parts-stair', 'class'=>'form-control' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('address_parts[floor]', Lang::get('account/properties.address.floor')) !!}
	{!! Form::text('address_parts[floor]', null, [ 'id'=>'address-parts-floor', 'class'=>'form-control' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('address_parts[door]', Lang::get('account/properties.address.door')) !!}
	{!! Form::text('address_parts[door]', null, [ 'id'=>'address-parts-door', 'class'=>'form-control' ]) !!}
</div>
<div class="form-group error-container">
	{!! Form::label('zipcode', Lang::get('account/properties.zipcode')) !!}
	{!! Form::text('zipcode', null, [ 'class'=>'form-control' ]) !!}
</div>
<div class="help-block">
	<label>
		{!! Form::checkbox('show_address', 1, null) !!}
		{{ Lang::get('account/properties.show_address') }}
	</label>
</div>
<div class="hide">
	{!! Form::label('address', Lang::get('account/properties.address')) !!}
	{!! Form::textarea('address', null, [ 'id'=>'address-full-input', 'class'=>'form-control address-input', 'rows'=>'3' ]) !!}
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		$('body').on('change', '.address-part-input', function() {
			var address = $.trim( $('#address-parts-type').val() + ' ' + $('#address-parts-street').val() );

			if ( $('#address-parts-number').val() ) {
				address += ', ' + $('#address-parts-number').val();
			}

			$('#address-full-input').val( address );
		});
	});
</script>