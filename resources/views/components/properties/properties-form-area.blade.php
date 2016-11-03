<div class="properties-search-form-area">
	@if ( Input::get('search') )
		@if ( $properties->total() == 1 )
			<h2>{{ Lang::get('web/properties.search.results.one') }}</h2>
		@else
			<h2>{{ Lang::get('web/properties.search.results.many', [ 'total'=>number_format($properties->total(),0,',','.') ]) }}</h2>
		@endif
	@else
		<h2>{{ Lang::get('web/properties.search.results') }}</h2>
	@endif
	<div class="form-area" style="opacity: 0;">
		@include('web.search.form')
		<a href="#" class="form-area-minimizer text-center"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a>
	</div>
</div>