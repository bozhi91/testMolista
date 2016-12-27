<div id="home-slider" class="carousel slide" data-ride="carousel">
	<!-- Indicators -->
	<ol class="carousel-indicators">
		@foreach ($widget['items'] as $key => $item)
		<li data-target="#home-slider" data-slide-to="{{$key}}" class="<?= $key == 0 ? 'active' : '' ?>"></li>
		@endforeach
	</ol>

	<div class="carousel-inner" role="listbox">
		@foreach ($widget['items'] as $key => $item)
			@if(!empty($item['link']))
				<a href="{{$item['link']}}" class="item <?= $key == 0 ? 'active' : '' ?>">
					<div class="image-div" style="background: url({{ $item['image'] }});"></div>
				</a>
			@else
				<div class="item <?= $key == 0 ? 'active' : '' ?>">
					<div class="image-div" style="background: url({{ $item['image'] }});"></div>
				</div>
			@endif
		@endforeach
	</div>


	<!-- Controls -->
	<a class="left carousel-control slider-control" href="#home-slider" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	</a>
	<a class="right carousel-control slider-control" href="#home-slider" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
	</a>

	<div class="container">
		<div class="row">
			<div class="slider-quick-search-container col-xs-12 col-md-3 hidden-xs hidden-sm">
				<div class="slider-quick-search cursor-default">
					@include('web.search.quick', [ 'no_title'=>1 ])
				</div>
			</div>
		</div>
	</div>
</div>
