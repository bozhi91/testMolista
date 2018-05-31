@if ($property->price_before)
	<span class="text-italic strike-through price-before"> <small>{{ price($property->price_before, $property->infocurrency->toArray()) }} </small> </span>
@endif