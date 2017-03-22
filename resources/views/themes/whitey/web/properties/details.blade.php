@extends('layouts.web', [
	'menu_section' => 'properties',
	'use_google_maps' => true,
])

@section('content')

	<div id="property">

		<div class="container">

			<div class="header">
				
				<div class="hidden-xs">
					<div class="row">
						<div class="hidden-xs col-sm-8">
							<div class="modular-property-title">
								@include('components.property.property-title')
							</div>
							<div class="modular-property-price">
								@include('components.property.property-price')
							</div>
						</div>
						<div class="hidden-xs col-sm-4">
							<div class="modular-property-more-info-button">
								@include('components.property.property-moreinfo-button')
							</div>
						</div>
					</div>
				</div>

				<div class="visible-xs">
					<div class="row">
						<div class="col-xs-8">
							<div class="modular-property-title">
								@include('components.property.property-title')
							</div>
							<div class="modular-property-location">
								@include('components.property.property-location')
							</div>
						</div>
						<div class="col-xs-4">
							<div class="modular-property-price">
								@include('components.property.property-price')
							</div>
						</div>
					</div>
				</div>
				
			</div>

			<div class="content">
				
				<div class="row">
					<div class="col-xs-12 col-sm-7">
						@include('components.property.property-image-slider')
					</div>
					<div class="col-xs-12 col-sm-5">
						<div class="hidden-xs">
							<div class="modular-property-content-text">
							
							@include('components.property.property-metrics')

							@include('components.property.property-location')

							@include('components.property.property-description')

							@include('components.property.property-services')

							@include('components.property.property-energy-certification')

							@include('components.property.property-download-pdf')

							</div>
						</div>
						<div class="visible-xs">

							<div class="modular-property-content-text">
							
								@include('components.property.property-description')

								<div class="modular-property-metrics row">
									@include('components.property.property-metrics')
								</div>

								@include('components.property.property-services')

								@include('components.property.property-energy-certification')

							</div>

							<div class="modular-property-more-info-button">
								@include('components.property.property-moreinfo-button')
							</div>

						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						@include('components.property.property-map-area')
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						@include('components.property.property-related-properties' , [ 'related_properties'=>$property->related_properties ] )
					</div>
				</div>

			</div>


		</div>

	</div>

	<script type="text/javascript">
		
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

			cont.find('.image-thumb').magnificPopup({
				type: 'image',
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

			var form = $('#property-moreinfo-form');
			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					form.find('.form-error').addClass('hide');
					$.ajax({
						dataType: 'json',
						type: 'POST',
						url: form.attr('action'),
						data: form.serialize(),
						success: function(data) {
							LOADING.hide();
							if ( data.success ) {
								form.find('.form-content').addClass('hide');
								form.find('.form-success').removeClass('hide');
							} else {
								var message = data.message ? data.message : "{{ print_js_string( Lang::get('general.messages.error') ) }}";
								form.find('.form-error').removeClass('hide').find('.alert-content').html(message);
							}
						},
						error: function() {
							LOADING.hide();
								form.find('.form-error').removeClass('hide').find('.alert-content').html("{{ print_js_string( Lang::get('general.messages.error') ) }}");
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

			if ( $(window).width() < 768 ) {
				$('#property .content .modular-property-content-text .modular-property-metrics .metrics li').matchHeight(); //{ byRow : false }
			};

		});
	</script>

@endsection
