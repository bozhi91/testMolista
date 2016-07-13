<div class="price {{@$price_class}}">
	<a href="{{ action('Web\PropertiesController@details', $item->slug) }}">{{ price($item->price, [ 'decimals'=>0 ]) }}</a>
</div>