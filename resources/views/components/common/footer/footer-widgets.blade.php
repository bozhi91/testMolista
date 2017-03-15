<?php

	if (!isset($footerWidgetCol)) {
		$footerWidgetCol = "col-xs-12 col-md-4";
	}

?>

<div class="highlights">
	<div class="container">
		<div class="row">
			@if ( !empty($site_setup['widgets']['footer']) )
				@foreach ($site_setup['widgets']['footer'] as $widget)
					<div class="{{ $footerWidgetCol }}">
						@if ( $widget['type'] == 'menu' )
							<h4>{{ $widget['title'] }}</h4>
							@include('common.widget-menu', [
								'widget' => $widget,
								'widget_class' => 'list-unstyled',
							])
						@elseif ( $widget['type'] == 'text' )
							<h4>{{ $widget['title'] }}</h4>
							@include('common.widget-text', [
								'widget' => $widget,
							])
						@elseif ( $widget['type'] == 'awesome-link' )
							@if ($widget['title'])
							<h4>{{ $widget['title'] }}</h4>
							@endif
							@include('common.widget-awesome-link-footer', [
								'widget' => $widget,
							])
						@endif
						<div class="visible-xs-block visible-sm-block">&nbsp;</div>
					</div>
				@endforeach
			@endif
		</div>
	</div>
</div>
