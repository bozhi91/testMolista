<div class="property-row">
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="property-column image-column">
				<a href="{{ action('Web\PropertiesController@details', $item->slug) }}" class="image" style="background-image: url('{{ $item->main_image }}');">
					<img src="{{ $item->main_image }}" alt="{{$item->title}}" class="hide" />
				</a>
			</div>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="property-column text-column">
				<div class="row">
					<div class="col-xs-12 col-sm-9">
						<div class="title text-bold">
							<a href="{{ action('Web\PropertiesController@details', $item->slug) }}">{{$item->title}}</a>
						</div>
						<div class="location text-italic">
							<i class="fontello-icon fontello-icon-marker hidden-xs"></i>
							{{ implode(', ', array_filter([
								'district' => $item->district,
								'city' => $item->city->name,
								'state' => $item->state->name,
							])) }}
						</div>
						<div class="description text-italic">{!! str_limit($item->description, 150, ' <a href="'.(action('Web\PropertiesController@details', $item->slug)).'">[...]</a>') !!}</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="price text-bold text-right">
							<a href="{{ action('Web\PropertiesController@details', $item->slug) }}">{{ price($item->price, [ 'decimals'=>0 ]) }}</a>
						</div>
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
								{{ number_format(round($item->price/$item->size),0,',','.') }} {{ price_symbol($property->currency) }}/m²
							</li>
						</ul>
						<div class="services text-italic">
							{{ $item->services->sortBy('title')->implode('title',', ') }}
						</div>
					</div>
					<div class="col-xs-3">
					</div>
					<a href="{{ action('Web\PropertiesController@details', $item->slug) }}" class="hidden-xs btn btn-primary btn-more-info">{{ Lang::get('web/properties.search.results.more') }}</a>
				</div>
			</div>
		</div>
	</div>
</div>
