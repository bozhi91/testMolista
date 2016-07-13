  <div id="advanced-search-modal" class="mfp-hide app-popup-block search-popup-block">
	<h2>{{ Lang::get('web/search.title.popup') }}</h2>
	@include('web.search.form')
</div>

<a href="#advanced-search-modal" id="advanced-search-trigger" class="hide">Advanced search</a>

<script type="text/javascript">
	ready_callbacks.push(function(){
		$('#advanced-search-trigger').magnificPopup({
			type: 'inline',
			preloader: false,
			closeOnBgClick: false,
			callbacks: {
				open: function() {
					$('.if-overlay-then-blurred').addClass('blurred');
					$('#advanced-search-modal').find('select.has-select-2').each(function(){
						$(this).select2();
					});
				}
			}
		});		
	});
</script>
