
@if(!empty($property->html_property))
	<div class="row">
		<br><br>
		<h2>{{ Lang::get('general.htmlSnippet') }}</h2>
		<div class="col-xs-12 col-sm-12">
			<div class="property-pill" style="padding:20px;">
				{!! $property->html_property !!}
			</div>
		</div>
	</div>
@endif