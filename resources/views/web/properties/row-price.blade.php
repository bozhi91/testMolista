<div class="price {{@$price_class}}">
	<a href="{{ action('Web\PropertiesController@details', $item->slug) }}">{{ price($item->price, $item->infocurrency->toArray()) }}</a>
</div>