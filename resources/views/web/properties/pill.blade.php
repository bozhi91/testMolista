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
			<ul class="list-inline metrics">
				<li class="text-nowrap text-lowercase has-fontello-icon">
					<i class="fontello-icon fontello-icon-table"></i>
					{{ number_format($property->rooms,0,',','.') }} 
					@if ($property->rooms == 1)
						{{ Lang::get('web/properties.more.room') }}
					@else
						{{ Lang::get('web/properties.more.rooms') }}
					@endif
				</li>
				<li class="text-nowrap text-lowercase has-fontello-icon">
					<i class="fontello-icon fontello-icon-shower"></i>
					{{ number_format($property->baths,0,',','.') }}
					@if ($property->baths == 1)
						{{ Lang::get('web/properties.more.bath') }}
					@else
						{{ Lang::get('web/properties.more.baths') }}
					@endif
				</li>
			</ul>
		</div>
	</div>
</div>
