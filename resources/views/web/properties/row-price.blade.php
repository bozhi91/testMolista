<div class="price {{@$price_class}}">
	<a href="{{ $item->full_url }}">{{ price($item->price, $item->infocurrency->toArray()) }}</a>
</div>