@if ( $property->url_3d )
<div class="property-view-3d">
	<a href="{{ $property->url_3d }}" class="btn btn-primary hidden-xs" target="_blank">{{ Lang::get('web/properties.view.in.3d') }}</a>
</div>
@endif