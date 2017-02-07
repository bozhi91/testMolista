<div class="location text-italic">
	{{ implode(', ', array_filter([
		'district' => $item->district,
		'city' => $item->city->name,
		'state' => $item->state->name,
	])) }}
</div>