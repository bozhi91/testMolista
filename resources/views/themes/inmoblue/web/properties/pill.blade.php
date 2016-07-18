<div class="property-pill">
	<div class="padder">
		<a href="{{ action('Web\PropertiesController@details', $item->slug) }}" class="image" style="background-image: url('{{ $item->main_image }}');">
			<img src="{{ $item->main_image }}" alt="{{$item->title}}" class="hide" />
			@if ( $item->label)
				<div class="labels">
					<span class="label" style="background-color: {{ $item->label_color }};">{{ $item->label }}</span>
				</div>
			@endif
		</a>
		<div class="text">
			
			<div class="title text-bold">
				<a href="{{ action('Web\PropertiesController@details', $item->slug) }}">{{$item->title}}</a>
			</div>
			
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

			<hr>

			<div class="bottom-pill">
				<div class="price text-italic">

					{{ price($item->price, $item->infocurrency->toArray()) }}

					<span class="pull-right button-pill-block">
						<a class="button-pill" href="#">{{ Lang::get('web/properties.search.results.more') }}</a>
					</span>

				</div>
			</div>

		</div>
	</div>
</div>
