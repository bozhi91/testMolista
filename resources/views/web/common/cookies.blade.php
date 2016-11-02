<style type="text/css">
	#cookies-warning { position: fixed; bottom: 0px; left: 0px; width: 100%; display: none; font-size: 16px; }
	#cookies-warning .alert { margin-bottom: 0px; }
</style>

@if (@$current_site)
<div id="cookies-warning" style="">
	<div class="alert alert-info" role="alert">
		<div class="container">
			<button type="button" class="close"><span aria-hidden="true">×</span></button>
			{!! Lang::get('web/info.cookies.warning', [
				'site_name' => $current_site->title,
				'link' => site_url(action('Corporate\InfoController@getLegal') . '#cookies-policy', $current_site),
			]) !!}
		</div>
	</div>
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var cont = $('#cookies-warning');

		function accepts_cookies() {
			// Set cookie
			$.cookie("are_cookies_accepted", "are_cookies_accepted", { expires: 365, path: '/' });

			// Hide warning
			cont.animate({ height: 0 }, 300);
		}

		// Check if cookies accepted
        var cookies_accepted = $.cookie('are_cookies_accepted') == "are_cookies_accepted";

        if (cookies_accepted) {
        } else {
			cont.show();
			// Navigate
			$('body').on('click', accepts_cookies);
			// Click on warning
			cont.on('click', function(e){
				e.stopPropagation();
			});
			// Close warning
			cont.on('click', '.close', accepts_cookies);
		}
	});
</script>
@endif
