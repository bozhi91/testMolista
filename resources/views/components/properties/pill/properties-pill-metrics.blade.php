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