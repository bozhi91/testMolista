<div class="location property-location">
	<i class="fontello-icon fontello-icon-marker hidden-xs"></i>
	{{ implode(', ', array_filter([
		'district' => $property->district,
		'city' => $property->city->name,
		'state' => $property->state->name,
	])) }}
</div>