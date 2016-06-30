<footer class="footer">
	<div class="container">
		<ul class="list-unstyled pull-right footer-list">
			<li>
				<select onchange="document.location.href=this.value;" class="form-control">
					@foreach(LaravelLocalization::getSupportedLocales() as $locale => $def)
						<option value="{{ LaravelLocalization::getLocalizedURL($locale) }}" {!! ( $locale == LaravelLocalization::getCurrentLocale() ) ? 'selected="selected"' : '' !!}>{{ $def['native'] }}</option>
					@endforeach
				</select>
			</li>
		</ul>
	</div>
</footer>