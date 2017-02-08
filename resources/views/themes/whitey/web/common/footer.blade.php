<footer id="footer" class="if-overlay-then-blurred">

	@include('components.common.footer.footer-widgets', [ 'footerWidgetCol' => 'col-xs-12 col-md-4' ])

	@include('components.common.footer.footer-bottom')

</footer>

<script type="text/javascript">
	ready_callbacks.push(function(){

		function onResize() {
			var h = $('#footer').height();
			$('#sticky-wrapper').css({
				'margin-bottom': (-1*h)+'px',
				'padding-bottom': (h)+'px'
			});
		}

		$(window).resize(onResize);
		onResize();

	});
</script>
