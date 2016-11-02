<?php

	$widgetSlider = false; //ugly temporal
	if(!empty($site_setup['widgets']['home'])){
		foreach ($site_setup['widgets']['home'] as $widget) {
			if($widget['type'] == 'slider'){
				$widgetSlider = $widget;
			}
		}
	}
?>

@if ($widgetSlider)
	@include('common.widget-slider', ['widget' => $widgetSlider])
@elseif ($main_property)
	<div class="main-property carousel slide" data-interval="false">
		<div class="carousel-inner" role="listbox">
			<a href="{{ $main_property->full_url }}"  class="item active">
				<img src="{{$main_property->main_image}}" alt="{{$main_property->title}}" class="main-image" />
				@include('web.index-caption')
			</a>
		</div>
	</div>
@endif