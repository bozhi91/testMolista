<div class="carousel-caption">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-3 hidden-xs hidden-sm">
				<div class="relative">
					<div class="slider-quick-search cursor-default">
						@include('web.search.quick', [ 'no_title'=>1 ])
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6">
				<span class="carousel-caption-text">
					{{$main_property->title}}
					<span class="text-nowrap hidden-xs"> |
						@if($main_property->desde=='1')
							{{ Lang::get('web/properties.from') }}
						@endif
						{{ price($main_property->price, $main_property->infocurrency->toArray()) }} @include('web.properties.discount-price', [ 'property' => $main_property ]) </span>
					@if($main_property->mode=='vacationRental')
						/{{ Lang::get('web/properties.week') }}
					@endif
				</span>
			</div>
		</div>
	</div>
</div>