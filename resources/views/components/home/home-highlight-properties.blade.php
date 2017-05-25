<?php
	if (!isset($colperpage)) {
		$colperpage = 4;
	}
?>

@if ( count($highlighted) > 0 )
	<div class="container">
		<div class="properties-slider-area">
			<h2>{{ Lang::get('web/home.gallery') }}</h2>
			<div id="properties-slider" class="properties-slider carousel slide" data-ride="carousel">
				<div class="carousel-inner" role="listbox">
					<div class="item active">
						<div class="row">
							@foreach ($highlighted as $key => $property)
								@if ( $key > 0 && $current_site->home_highlights > 0 && $key % $current_site->home_highlights == 0 )
									</div></div><div class="item"><div class="row">
								@endif
								<div class="col-xs-12 col-sm-{{$colperpage}}">
									<div class="relative">
										@include('web.properties.pill', [ 'item'=>$property])
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
				<a class="left carousel-control hide hidden-xs" href="#properties-slider" role="button" data-slide="prev">
					&lsaquo;
					<span class="sr-only">{{ Lang::get('pagination.previous') }}</span>
				</a>
				<a class="right carousel-control hide hidden-xs" href="#properties-slider" role="button" data-slide="next">
					&rsaquo;
					<span class="sr-only">{{ Lang::get('pagination.next') }}</span>
				</a>
			</div>
		</div>
	</div>
@endif