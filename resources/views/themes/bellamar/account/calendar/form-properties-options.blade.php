@if ( empty($selected_ids) )
	<?php $selected_ids = []; ?>
@elseif ( !is_array($selected_ids) )
	<?php $selected_ids = [ $selected_ids ]; ?>
@endif

@foreach ($properties as $property)
	<?php 
		$option_text = implode(', ', array_filter([
			$property->address_parts['street'],
			$property->address_parts['number'],
			$property->city->name,
		])); 
	?>
	<option value="{{ $property->id }}" data-location="{{ $property->address ? $property->full_address : '' }}" {{ in_array($property->id, $selected_ids) ? 'selected="selected"' : '' }}>
			{{ $property->ref }}: 
			{{ $option_text }}
	</option>
@endforeach
