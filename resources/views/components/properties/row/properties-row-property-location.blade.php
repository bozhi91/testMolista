<div class="location">
	<i class="fontello-icon fontello-icon-marker hidden-xs"></i>
	{{ implode(', ', array_filter([
		'district' => $item->district,
		'city' => $item->city->name,
		'state' => $item->state->name,
	])) }}
</div>