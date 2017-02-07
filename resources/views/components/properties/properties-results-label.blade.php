<div class="properties-results-label">
@if ( Input::get('search') )
	@if ( $properties->total() == 1 )
		<h2>{{ Lang::get('web/properties.search.results.one') }}</h2>
	@else
		<h2>{{ Lang::get('web/properties.search.results.many', [ 'total'=>number_format($properties->total(),0,',','.') ]) }}</h2>
	@endif
@else
	<h2>{{ Lang::get('web/properties.search.results') }}</h2>
@endif
</div>