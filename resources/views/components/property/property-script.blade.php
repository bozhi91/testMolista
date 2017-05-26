<script type="text/javascript">
	google.maps.event.addDomListener(window, 'load', function(){
		var mapLatLng = { lat: {{$property->lat_public}}, lng: {{$property->lng_public}} };

		var property_map = new google.maps.Map(document.getElementById('property-map'), {
			zoom: 14,
			center: mapLatLng,
			styles: {!! Theme::config('gmaps-style') !!}
		});

		@if ( @$property->show_address )
			var property_marker = new google.maps.Marker({
				position: mapLatLng,
				map: property_map,
				icon: '{{ asset( Theme::config('gmaps-marker') ) }}'
			});
		@else
			var property_marker = new google.maps.Circle({
				strokeColor: '{{ Theme::config('gmaps-circle') }}',
				strokeOpacity: 0.8,
				strokeWeight: 0,
				fillColor: '{{ Theme::config('gmaps-circle') }}',
				fillOpacity: 0.25,
				map: property_map,
				center: mapLatLng,
				radius: 500
			});
		@endif
	});

	ready_callbacks.push(function(){
		var cont = $('#property');

		if ( cont.find('.images-carousel .carousel-inner .item').length < 2) {
			cont.find('.images-carousel .carousel-inner .item').css({
				'padding-left' : '0px',
				'padding-right' : '0px'
			});
			cont.find('.images-carousel .carousel-control').remove();
		} else {
			cont.find('.images-carousel .carousel-control').removeClass('hide');
		}

		cont.find('.images-carousel').magnificPopup({
			delegate: '.image-thumb',
			gallery:{
				enabled: true,
				navigateByImgClick: false,
				arrowMarkup: 	'<a href="javascript:;" class="btn-nav btn-nav-%dir%">'+
									'<span class="glyphicon glyphicon-chevron-%dir%" aria-hidden="true"></span>'+
								'</a>',
			},
			callbacks: {
				buildControls: function() {
					this.contentContainer.append(this.arrowLeft.add(this.arrowRight));
				},
				open: function() {
					$('.if-overlay-then-blurred').addClass('blurred');
					$('body').find('.mfp-content').addClass('image-gallery-popup');
					if ( window.stButtons ){
						stButtons.locateElements();
					}
				},
				imageLoadComplete: function() {
					var cont = $('body').find('.image-gallery-header');
					if ( cont.length < 1 ) {
						return;
					}

					var ul = cont.find('ul');
					var target = cont.find('.btn-get-more-info').removeClass('hide').css({ opacity: 0 });

					if ( ul.height() > cont.height() ) {
						target.addClass('hide');
					} else {
						target.css({ opacity: 1 });

					}
				}
			},
			image: {
				markup: '<div class="mfp-figure">'+
							'<div class="image-gallery-border custom-border"></div>'+
							'<div class="image-gallery-header">'+
								'<ul class="list-inline clearfix">'+
									'<li class="social-link"><span class="st_facebook" displayText=""><i class="fa fa-facebook" aria-hidden="true"></i></span></li>'+
									'<li class="social-link"><span class="st_twitter" displayText=""><i class="fa fa-twitter" aria-hidden="true"></i></span></li>'+
									'<li class="close-area pull-right"><a href="#" class="btn-close popup-modal-dismiss"><i class="fa fa-close" aria-hidden="true"></i></a></li>'+
									'<li class="btn-area pull-right"><a href="#" class="btn btn-primary btn-get-more-info">{{ print_js_string( Lang::get('web/properties.call.to.action') ) }}</a></li>'+
								'</ul>'+
							'</div>'+
							'<div class="mfp-img">'+
							'</div>'+
						'</div>',
				cursor: ''
			},
			iframe: {
				markup: '<div class="mfp-figure">'+
							'<div class="image-gallery-header">'+
								'<ul class="list-inline clearfix">'+
									'<li class="social-link"><span class="st_facebook" displayText=""><i class="fa fa-facebook" aria-hidden="true"></i></span></li>'+
									'<li class="social-link"><span class="st_twitter" displayText=""><i class="fa fa-twitter" aria-hidden="true"></i></span></li>'+
									'<li class="close-area pull-right"><a href="#" class="btn-close popup-modal-dismiss"><i class="fa fa-close" aria-hidden="true"></i></a></li>'+
									'<li class="btn-area pull-right"><a href="#" class="btn btn-primary btn-get-more-info">{{ print_js_string( Lang::get('web/properties.call.to.action') ) }}</a></li>'+
								'</ul>'+
							'</div>'+
							'<div class="mfp-iframe-scaler">' +
								'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
							'</div>' +
						'</div>',
			},
			closeOnBgClick: false
		});

		$('body').on('click', '.btn-get-more-info', function(e){
			e.preventDefault();
			$.magnificPopup.close();
			cont.find('.more-info-trigger').trigger('click');

		});

		cont.on('click', '.trigger-image-thumbs', function(e){
			e.preventDefault();
			cont.find('.image-thumb').eq(0).trigger('click');
		});

		cont.find('.bottom-links .property-pill').matchHeight({ byRow : false });

		cont.find('.energy-certification-popover-trigger').popover({
			html : true,
			content: function() {
				return cont.find('.energy-certification-popover-content').html();
			},
			container: '.energy-certification',
			placement: 'bottom'
		}).on('show.bs.popover', function (e) {
			$(e.target).addClass('is-open');
		}).on('hide.bs.popover', function (e) {
			$(e.target).removeClass('is-open');
		});

		var form_info = $('#property-moreinfo-form');
		form_info.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			submitHandler: function(f) {
				LOADING.show();
				form_info.find('.form-error').addClass('hide');
				$.ajax({
					dataType: 'json',
					type: 'POST',
					url: form_info.attr('action'),
					data: form_info.serialize(),
					success: function(data) {
						LOADING.hide();
						if ( data.success ) {
							form_info.find('.form-content').addClass('hide');
							form_info.find('.form-success').removeClass('hide');
						} else {
							var message = data.message ? data.message : "{{ print_js_string( Lang::get('general.messages.error') ) }}";
							form_info.find('.form-error').removeClass('hide').find('.alert-content').html(message);
						}
					},
					error: function() {
						LOADING.hide();
							form_info.find('.form-error').removeClass('hide').find('.alert-content').html("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				});
			}
		});

		var form_share = $('#property-share-form');
		form_share.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			submitHandler: function(f) {
				LOADING.show();
				form_share.find('.form-error').addClass('hide');
				$.ajax({
					dataType: 'json',
					type: 'POST',
					url: form_share.attr('action'),
					data: form_share.serialize(),
					success: function(data) {
						LOADING.hide();
						if ( data.success ) {
							form_share.find('.form-content').addClass('hide');
							form_share.find('.form-success').removeClass('hide');
						} else {
							var message = data.message ? data.message : "{{ print_js_string( Lang::get('general.messages.error') ) }}";
							form_share.find('.form-error').removeClass('hide').find('.alert-content').html(message);
						}
					},
					error: function() {
						LOADING.hide();
							form_share.find('.form-error').removeClass('hide').find('.alert-content').html("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				});
			}
		});

		cont.find('.more-info-trigger').magnificPopup({
			type: 'inline',
			modal: true
		});

		$('body').on('click', '.popup-modal-dismiss', function (e) {
			e.preventDefault();
			$.magnificPopup.close();
		});

		$('body').on('click', function(e){
			if ( $(e.target).closest('.energy-certification').length < 1 ) {
				cont.find('.energy-certification-popover-trigger.is-open').trigger('click');
			}
		});

	});
</script>
