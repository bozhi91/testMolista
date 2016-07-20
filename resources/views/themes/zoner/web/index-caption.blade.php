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
				<div class="carousel-caption-text">
					
					<div class="carousel-caption-title"> {{$main_property->title}} </div>
					
					<div class="carousel-caption-subtitle text-nowrap hidden-xs"> {{ price($main_property->price, [ 'decimals'=>0 ]) }} </div>

					<div class="carousel-caption-button">
						<a href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="carousel-button">{{ Lang::get('web/properties.search.results.more') }}</a>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>