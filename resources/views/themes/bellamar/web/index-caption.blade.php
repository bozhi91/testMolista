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
					<span class="text-nowrap hidden-xs"> | {{ price($main_property->price, [ 'decimals'=>0 ]) }}</span>
				</span>
			</div>
		</div>
	</div>
</div>