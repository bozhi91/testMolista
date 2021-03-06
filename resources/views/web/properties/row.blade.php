<div class="property-row">
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="property-column image-column">
				@include('web.properties.discount-label')
				<a href="{{ $item->full_url }}" class="image" style="background-image: url('{{ $item->main_image }}');">
					<img src="{{ $item->main_image }}" alt="{{$item->title}}" class="hide" />
				</a>
				@include ('web.properties.row-image-price')
			</div>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="property-column text-column">
				<div class="row">
					<div class="col-xs-12 col-sm-9">
						<div class="title text-bold">
							<a href="{{ $item->full_url }}">{{$item->title}}</a>
						</div>
						<div class="location text-italic">
							<i class="fontello-icon fontello-icon-marker hidden-xs"></i>
							{{ implode(', ', array_filter([
								'district' => $item->district,
								'city' => $item->city->name,
								'state' => $item->state->name,
							])) }}
						</div>
						<div class="description text-italic">{!! str_limit($item->description, 150, ' <a href="'.($item->full_url).'">[...]</a>') !!}</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						@include ('web.properties.row-price' , ['price_class'=>'text-bold text-right'])
					</div>
				</div>
				<div class="row hidden-xs">
					<div class="col-xs-9">
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
							<li class="text-nowrap">
								{{ Lang::get('account/properties.ref') }}: {{ $property->ref }}
							</li>
						</ul>
						<div class="services text-italic">
							{{ $item->services->sortBy('title')->implode('title',', ') }}
						</div>
					</div>
					<div class="col-xs-3">
					</div>
					<a href="{{ $item->full_url }}" class="hidden-xs btn btn-primary btn-more-info">
						@include ('web.properties.row-moreinfo')
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
