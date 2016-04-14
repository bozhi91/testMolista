<div class="property-pill">
	<div class="padder">
		<a href="{{ action('Web\PropertiesController@details', $item->slug) }}" class="image" style="background-image: url('{{ $item->main_image }}');">
			<img src="{{ $item->main_image }}" alt="{{$item->title}}" class="hide" />
		</a>
		<div class="text">
			@if ( $item->newly_build)
				<div class="labels">
					<span class="label">{{ Lang::get('web/properties.labels.new') }}</span>
				</div>
			@endif
			<div class="title text-bold">
				<a href="{{ action('Web\PropertiesController@details', $item->slug) }}">{{$item->title}}</a>
			</div>
			<div class="price text-italic">{{ price($item->price, [ 'decimals'=>0 ]) }}</div>
			<div class="location text-italic">{{ implode(', ', array_filter([
				'district' => $item->district,
				'city' => $item->city->name,
				'state' => $item->state->name,
			])) }}</div>
		</div>
	</div>
</div>
