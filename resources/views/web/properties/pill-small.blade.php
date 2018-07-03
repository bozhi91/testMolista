<div class="property-pill property-pill-small">
	<a href="{{ $item->full_url }}" class="image" style="background-image: url('{{ $item->main_image_thumb }}');">
		<img src="{{ $item->main_image_thumb }}" alt="{{$item->title}}" class="hide" />
	</a>
	<div class="text">
		<div class="title text-bold">
			<a href="{{ $item->full_url }}">{{$item->title}}</a>
		</div>
		<div class="location text-italic">{{ implode(', ', array_filter([
			'district' => $item->district,
			'city' => $item->city->name,
			'state' => $item->state->name,
		])) }}</div>

		<div class="price text-italic">
			@if($item->desde=='1')
				{{ Lang::get('web/properties.from') }}
			@endif
			{{ price($item->price, $item->infocurrency->toArray(),$item) }}
			@include('web.properties.discount-price')
				@if($property->mode=='vacationRental')
					/{{ Lang::get('web/properties.week') }}
				@endif
		</div>
	</div>
</div>
