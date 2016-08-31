@if ( empty($selected_ids) )
	<?php $selected_ids = []; ?>
@elseif ( !is_array($selected_ids) )
	<?php $selected_ids = [ $selected_ids ]; ?>
@endif

@foreach ($properties as $property)
	<option value="{{ $property->id }}" data-location="{{ $property->address ? $property->full_address : '' }}" {{ in_array($property->id, $selected_ids) ? 'selected="selected"' : '' }}>
		{{ $property->ref }}: 
		{{ $property->title }}
	</option>
@endforeach
