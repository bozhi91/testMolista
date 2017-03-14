<script type="text/javascript">
	ready_callbacks.push(function(){
		var cont = $('#home');

		cont.find('.properties-slider .property-pill').matchHeight({ byRow : false });
		cont.find('.search-area .quick-link').matchHeight({ byRow : false });

		if ( cont.find('.properties-slider .carousel-inner .item').length < 2) {
			cont.find('.carousel-control').not('.slider-control').remove();
		} else {
			cont.find('.carousel-control').removeClass('hide');
		}

		var main_property = cont.find('.main-property');
		var main_property_image = main_property.find('.main-image');
		if ( main_property_image.length > 0 && main_property.height() > main_property_image.height() ) {
			main_property_image.addClass('hide');
			main_property.find('.item.active').css({ 'background-image': 'url(' + main_property_image.attr('src') + ')' })
		}

	});
</script>