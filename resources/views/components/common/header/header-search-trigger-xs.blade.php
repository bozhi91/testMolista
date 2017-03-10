<ul class="nav navbar-nav header-menu header-menu-search-trigger visible-xs">
	<li class="visible-xs"><a href="javascript:;" class="main-item show-advance-search-trigger">{{ Lang::get('web/search.title.popup') }}</a></li>
</ul>

<script type="text/javascript">
	ready_callbacks.push(function() {
		var cont = $('#header');

		cont.on('click','.show-advance-search-trigger',function(e){
			e.preventDefault();
			$('.advanced-search-trigger').trigger('click');
		});

	});
</script>