<div class="property-pill">
	<div class="padder">
		<a href="{{ $item->full_url }}" class="image" style="background-image: url('{{ $item->main_image_thumb }}');">
			<img src="{{ $item->main_image_thumb }}" alt="{{$item->title}}" class="hide" />
		</a>
		@include('web.properties.discount-label')
		<div class="text">
			@if ( $item->label)
				<div class="labels">
					<span class="label" style="background-color: {{ $item->label_color }};">{{ $item->label }}</span>
				</div>
			@endif
			<div class="title text-bold">
				<a href="{{ $item->full_url }}">{{$item->title}}</a>
			</div>
			<div class="price text-italic">{{ price($item->price, $item->infocurrency->toArray()) }} @include('web.properties.discount-price') </div>
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
			<div class="button hide">
				<a href="{{ $item->full_url }}">Ver Detalles</a>
			</div>
		</div>
	</div>
</div>
