@if ( $property->url_3d )
<div class="property-view-3d-iframe">
	<iframe width="100%" height="400" src="{{ $property->url_3d }}" frameborder="0" allowfullscreen></iframe>
</div>
@endif