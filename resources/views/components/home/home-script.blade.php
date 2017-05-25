<script type="text/javascript">
	ready_callbacks.push(function(){
		var cont = $('#home');
		var carousel = $('#properties-slider');

		function setSlider() {
			var el = $('<div class="visible-xs">').prependTo( $('body') );

			if ( el.is(':hidden') ) {
				if ( carousel.find('.carousel-inner .item').length < 2) {
					carousel.find('.carousel-control').remove();
				} else {
					carousel.find('.carousel-control').removeClass('hide');
				}
				carousel.find('.carousel-inner .item').removeClass('active').eq(0).addClass('active');
			} else {
				carousel.find('.carousel-inner .item').addClass('active');
			}

			el.remove();
		}

		cont.find('.properties-slider .property-pill').matchHeight({ byRow : false });
		cont.find('.search-area .quick-link').matchHeight({ byRow : false });

		var main_property = cont.find('.main-property');
		var main_property_image = main_property.find('.main-image');
		if ( main_property_image.length > 0 && main_property.height() > main_property_image.height() ) {
			main_property_image.addClass('hide');
			main_property.find('.item.active').css({ 'background-image': 'url(' + main_property_image.attr('src') + ')' })
		}

		$(window).resize(setSlider);
		setSlider();

	});
</script>