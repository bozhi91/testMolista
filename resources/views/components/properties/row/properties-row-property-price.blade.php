<div class="price">
	<a href="{{ $item->full_url }}">
		@if($property->desde=='1')
			{{ Lang::get('web/properties.from') }}
		@endif
		{{ price($item->price, $item->infocurrency->toArray(), $item) }}</a>
</div>