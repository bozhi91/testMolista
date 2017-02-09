<ul class="list-inline metrics">
	<li>
		<div class="text-nowrap">
			{{ number_format($item->size,0,',','.') }} m²
		</div>
	</li>
	<li class="text-nowrap text-lowercase has-fontello-icon">
		<i class="fontello-icon fontello-icon-table"></i>
		{{ number_format($item->rooms,0,',','.') }}
		@if ($item->rooms == 1)
			{{ Lang::get('web/properties.more.room') }}
		@else
			{{ Lang::get('web/properties.more.rooms') }}
		@endif
	</li>
	<li class="text-nowrap text-lowercase has-fontello-icon">
		<i class="fontello-icon fontello-icon-shower"></i>
		{{ number_format($item->baths,0,',','.') }}
		@if ($item->baths == 1)
			{{ Lang::get('web/properties.more.bath') }}
		@else
			{{ Lang::get('web/properties.more.baths') }}
		@endif
	</li>
	<li class="text-nowrap has-fontello-icon">
		<i class="fontello-icon fontello-icon-coins"></i>
		{{ @number_format(round($item->price/$item->size),0,',','.') }} {{ $item->infocurrency->symbol }}/m²
	</li>
</ul>