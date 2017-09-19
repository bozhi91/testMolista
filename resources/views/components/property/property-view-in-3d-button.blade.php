<?php
	if (!isset( $url_3d_container_classes )) {
		$url_3d_container_classes = '';
	}
	if (!isset( $url_3d_button_classes )) {
		$url_3d_button_classes = '';
	}
?>

@if ( $property->url_3d )
<div class="property-view-3d {{ $url_3d_container_classes }}">
	<a href="{{ $property->url_3d }}" class="btn btn-primary {{ $url_3d_button_classes }}" target="_blank">{{ Lang::get('web/properties.view.in.3d') }}</a>
</div>
@endif