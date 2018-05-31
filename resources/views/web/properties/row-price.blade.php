<div class="price {{@$price_class}}">
	<a href="{{ $item->full_url }}">
		@if($item->desde=='1')
			{{ Lang::get('web/properties.from') }}
		@endif
		{{ price($item->price, $item->infocurrency->toArray()) }}
		@include('web.properties.discount-price')
	</a>
</div>