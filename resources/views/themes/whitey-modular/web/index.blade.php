<?php



?>

@extends('layouts.web')

@section('content')

	@include('web.search.home')

	<div id="home">

		<div class="modular-slider">
			@include('components.home.home-slider')
		</div>

		<div class="modular-highlight-properties">
			@include('components.home.home-highlight-properties', ['colperpage' => 4 ])
		</div>

		<div class="modular-home-footer">
			@include('components.home.home-bottom-quick-search-area', [
				'quickSearchAreaTitleClasses' => 'col-xs-12 col-sm-12 col-md-8 Test-Modular-1' ,
			  	'quickSearchTagsClasses' => 'col-xs-12 col-sm-6 Test-Modular-2' ,
			  	'recentlyAddedPropertiesClasses' => 'hidden-xs hidden-sm col-md-4 Test-Modular-3'
			  ])
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#home');

			cont.find('.properties-slider .property-pill').matchHeight({ byRow : false });
			cont.find('.search-area .quick-link').matchHeight({ byRow : false });
			cont.find('.properties-slider .item').matchHeight({ byRow : false });

			cont.find('.properties-slider').on('slid.bs.carousel', function (ui) {
				cont.find('.properties-slider .item').each(function(k,v){
					if ( $(this).hasClass('active') ) {
						cont.find('.properties-slider-indicators li').eq(k).addClass('active');
					} else {
						cont.find('.properties-slider-indicators li').eq(k).removeClass('active');
					}
				});
			});

			if ( !$('body').hasClass('theme-bellamar') ) {
				cont.find('.main-property .slider-quick-search').css({
					bottom : ( -1 * cont.find('.main-property .carousel-caption-text').innerHeight() ) + 'px',
					opacity: 1
				});
			}


			cont.on('click', '.main-property .slider-quick-search', function(e){
				e.stopPropagation();
			});
			cont.on('click', '.main-property .item', function(e){
				e.preventDefault();
				document.location.href = $(this).data().href;
			});

			var search_sm = cont.find('.quick-search-xs-sm-area');
			if ( search_sm.is(':visible') ) {
				$('#quick-search-form').appendTo( search_sm );
			}

			var main_property = cont.find('.main-property');
			var main_property_image = main_property.find('.main-image');
			if ( (main_property_image.length > 0 && main_property.height() > main_property_image.height()) || $('body').hasClass('theme-white-cloud') ) {
				main_property_image.addClass('hide');
				main_property.find('.item.active').css({ 'background-image': 'url(' + main_property_image.attr('src') + ')' })
			}

		});
	</script>

@endsection
