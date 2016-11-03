<div class="property-metrics">
	@if ( @$property->details['lot_area'] )
		<ul class="list-inline metrics lot-area-metrics">
			<li class="text-nowrap lot-area-item lot-area-size">
				<img src="{{ asset('images/properties/icon-size.png') }}" class="lot-area-icon" alt="" />
				{{ number_format($property->size,0,',','.') }} m²
			</li>
			<li class="text-nowrap lot-area-item lot-area-size">
				<img src="{{ asset('images/properties/icon-area.png') }}" class="lot-area-icon"  alt="" />
				{{ number_format($property->details['lot_area'],0,',','.') }} m²
			</li>
			<li class="lot-area-sep hidden-xs"></li>
			<li class="text-nowrap text-lowercase">
				{{ number_format($property->rooms,0,',','.') }}
				@if ($property->rooms == 1)
					{{ Lang::get('web/properties.more.room') }}
				@else
					{{ Lang::get('web/properties.more.rooms') }}
				@endif
			</li>
			<li class="text-nowrap text-lowercase">
				{{ number_format($property->baths,0,',','.') }}
				@if ($property->baths == 1)
					{{ Lang::get('web/properties.more.bath') }}
				@else
					{{ Lang::get('web/properties.more.baths') }}
				@endif
			</li>
			<li class="text-nowrap">
				{{ @number_format(round($property->price/$property->size),0,',','.') }} {{ $property->infocurrency->symbol }}/m²
			</li>
			<li>
				{{ Lang::get('account/properties.ref') }}: {{ $property->ref }}
			</li>
		</ul>
	@else
		<ul class="list-inline metrics">
			<li>
				<div class="text-nowrap">
					{{ number_format($property->size,0,',','.') }} m²
				</div>
			</li>
			<li class="text-nowrap text-lowercase has-fontello-icon">
				<i class="fontello-icon fontello-icon-table hidden-xs"></i>
				{{ number_format($property->rooms,0,',','.') }}
				@if ($property->rooms == 1)
					{{ Lang::get('web/properties.more.room') }}
				@else
					{{ Lang::get('web/properties.more.rooms') }}
				@endif
			</li>
			<li class="text-nowrap text-lowercase has-fontello-icon">
				<i class="fontello-icon fontello-icon-shower hidden-xs"></i>
				{{ number_format($property->baths,0,',','.') }}
				@if ($property->baths == 1)
					{{ Lang::get('web/properties.more.bath') }}
				@else
					{{ Lang::get('web/properties.more.baths') }}
				@endif
			</li>
			<li class="text-nowrap has-fontello-icon">
				<i class="fontello-icon fontello-icon-coins hidden-xs"></i>
				{{ @number_format(round($property->price/$property->size),0,',','.') }} {{ $property->infocurrency->symbol }}/m²
			</li>
			<li>
				{{ Lang::get('account/properties.ref') }}: {{ $property->ref }}
			</li>
		</ul>
	@endif
</div>