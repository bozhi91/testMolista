<div class="carousel-caption">
	<span class="carousel-caption-text">
		{{$main_property->title}}
		<span class="text-nowrap hidden-xs"> | {{ price($main_property->price, $main_property->infocurrency->toArray()) }} @include('web.properties.discount-price', [ 'property' => $main_property ]) </span>
	</span>
</div>