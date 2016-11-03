<div class="property-download-pdf">
	<a href="{{ action('Web\PropertiesController@downloads', [ $property->slug, LaravelLocalization::getCurrentLocale() ]) }}" class="btn btn-primary hidden-xs" target="_blank">{{ Lang::get('web/properties.download.pdf') }}</a>
</div>