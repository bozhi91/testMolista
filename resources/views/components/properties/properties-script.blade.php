<script type="text/javascript">
	ready_callbacks.push(function(){
		var cont = $('#properties');

		cont.find('.form-area').addClass('closed').css({ opacity: 1 });

		cont.on('focus', '.first-input-line input', function(e){
			cont.find('.form-area').removeClass('closed').find('select.has-select-2').select2();
		});

		cont.on('click', '.form-area-minimizer', function(e){
			e.preventDefault();
			cont.find('.form-area').addClass('closed');
		});

		function fixPropertiesSize() {
			if ( cont.find('.results-area .property-column').length < 1 ) {
				return false;
			}

			if ( cont.find('.results-area .property-column .metrics').eq(0).is(':visible') ) {
				cont.find('.results-area .property-column').matchHeight({ byRow : false });
			} else {
				cont.find('.results-area .property-column').css({ height: 'auto' });
			}
		}
		$(window).resize(fixPropertiesSize);
		fixPropertiesSize();

	});
</script>