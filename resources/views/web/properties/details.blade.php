@extends('layouts.web', [
	'menu_section' => 'properties',
	'use_google_maps' => true,
])

@section('content')

	<div id="property">

		<div class="container">

			<div class="header">
				<div class="price text-bold text-italic pull-right">{{ price($property->price, $property->infocurrency->toArray()) }}</div>
				<h1 class="text-bold">{{$property->title}}</h1>
				<div class="location text-italic">
					<i class="fontello-icon fontello-icon-marker hidden-xs"></i>
					{{ implode(', ', array_filter([
						'district' => $property->district,
						'city' => $property->city->name,
						'state' => $property->state->name,
					])) }}
				</div>
			</div>

			@if ( $property->images->count() > 0 )
				<div class="images-gallery">
					<div class="image-main text-center">
						<img src="{{ $property->main_image }}" alt="{{$property->title}}" class="img-responsive cursor-pointer trigger-image-thumbs" id="property-main-image" />
					</div>
					@if ( $property->images->count() > 1 )
						<div id="images-carousel" class="images-carousel carousel slide" data-interval="false">
							<div class="carousel-inner" role="listbox">
								<div class="item active">
									<div class="row">
										@foreach ($property->images->sortBy('position')->values() as $key => $image)
											@if ( $key > 0 && $key%6 < 1 )
												</div></div><div class="item"><div class="row">
											@endif
											<div class="col-xs-4 col-sm-2">
												<a title="{{$key}}" href="{{ "{$property->image_folder}/{$image->image}" }}" class="image-thumb" style="background-image: url('{{ "{$property->image_folder}/{$image->image}" }}');">
													<img src="{{ "{$property->image_folder}/{$image->image}" }}" alt="{{$property->title}}" class="hide" />
												</a>
											</div>
										@endforeach
									</div>
								</div>
							</div>
							<a class="left carousel-control hide" href="#images-carousel" role="button" data-slide="prev">
								&lsaquo;
							</a>
							<a class="right carousel-control hide" href="#images-carousel" role="button" data-slide="next">
								&rsaquo;
							</a>
						</div>
					@endif
				</div>
			@endif

			<div class="details">
				<div class="row">
					<div class="cols-xs-12 col-sm-10">
						<div class="description text-italic">
							{!! nl2p($property->description) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="cols-xs-12 col-sm-9">
						<ul class="list-inline metrics">
							<li>
								<div class="text-nowrap">
									{{ number_format($property->size,0,',','.') }} m²
								</div>
							</li>
							<li class="text-nowrap text-lowercase has-fontello-icon">
								<i class="fontello-icon fontello-icon-table hidden-xs"></i>
								{{ number_format($property->rooms,0,',','.') }} 
								@if ($property->rooms == 1)
									{{ Lang::get('web/properties.more.room') }}
								@else
									{{ Lang::get('web/properties.more.rooms') }}
								@endif
							</li>
							<li class="text-nowrap text-lowercase has-fontello-icon">
								<i class="fontello-icon fontello-icon-shower hidden-xs"></i>
								{{ number_format($property->baths,0,',','.') }}
								@if ($property->baths == 1)
									{{ Lang::get('web/properties.more.bath') }}
								@else
									{{ Lang::get('web/properties.more.baths') }}
								@endif
							</li>
							<li class="text-nowrap has-fontello-icon">
								<i class="fontello-icon fontello-icon-coins hidden-xs"></i>
								{{ number_format(round($property->price/$property->size),0,',','.') }} {{ $property->infocurrency->symbol }}/m²
							</li>
							<li>
								{{ Lang::get('account/properties.ref') }}: {{ $property->ref }}
							</li>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="cols-xs-12 col-sm-9">
						<div class="services text-italic">
							{{ $property->services->sortBy('title')->implode('title',', ') }}
						</div>
						@if ( $property->ec || $property->ec_pending )
							<div class="energy-certification">
								<span class="energy-certification-popover-trigger text-bold cursor-pointer">
									<i class="fa fa-info-circle" aria-hidden="true"></i>
									&nbsp;{{ Lang::get('account/properties.energy.certificate') }}:
								</span>
								&nbsp; 
								@if ( $property->ec_pending )
									{{ Lang::get('account/properties.energy.certificate.pending') }}</span>
								@else
									<img src="{{ asset("images/properties/ec-{$property->ec}.png") }}" alt="{{ $property->ec }}" class="energy-certification-icon" />
								@endif
							</div>
							<div class="energy-certification-popover-content hide">
								<table>
									<tr>
										<td class="hidden-xs"><img src="{{ asset("images/properties/ec-all.png") }}" alt="{{ Lang::get('account/properties.energy.certificate') }}" /></td>
										<td class="text">{!! Lang::get('web/properties.energy.certificate.help') !!}
									</tr>
								</table>
							</div>
						@endif
						<br />
						<a href="{{ action('Web\PropertiesController@downloads', [ $property->slug, LaravelLocalization::getCurrentLocale() ]) }}" class="btn btn-primary hidden-xs" target="_blank">{{ Lang::get('web/properties.download.pdf') }}</a>
					</div>
				</div>
				<a href="#property-moreinfo-form" class="btn btn-primary call-to-action more-info-trigger">{{ Lang::get('web/properties.call.to.action') }}</a>
			</div>

			<div class="map-area">
				<div id="property-map" class="map"></div>
			</div>

			@include('web.properties.details-bottom', [ 'related_properties'=>$property->related_properties ])

		</div>

	</div>

	{!! Form::open([ 'action'=>[ 'Web\PropertiesController@moreinfo', $property->slug ], 'method'=>'POST', 'id'=>'property-moreinfo-form', 'class'=>'mfp-hide app-popup-block-white' ]) !!}
		<h2 class="page-title">{{ Lang::get('web/properties.call.to.action') }}</h2>
		<div class="alert alert-success form-success hide">
			{!! Lang::get('web/properties.moreinfo.success') !!}
			<div class="text-right">
				<a href="#" class="alert-link popup-modal-dismiss">{{ Lang::get('general.continue') }}</a>
			</div>
		</div>
		<div class="form-content">
			<div class="row">
				<div class="cols-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('first_name', Lang::get('web/customers.register.name.first') ) !!}
						{!! Form::text('first_name', old('first_name', SiteCustomer::get('first_name')), [ 'class'=>'form-control required' ] ) !!}
					</div>
				</div>
				<div class="cols-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('last_name', Lang::get('web/customers.register.name.last') ) !!}
						{!! Form::text('last_name', old('first_name', SiteCustomer::get('last_name')), [ 'class'=>'form-control required' ] ) !!}
					</div>
				</div>
			</div>
			<div class="form-group error-container">
				{!! Form::label('email', Lang::get('web/customers.register.email') ) !!}
				{!! Form::text('email', old('email', SiteCustomer::get('email')), [ 'class'=>'form-control required email' ] ) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('phone', Lang::get('web/customers.register.phone') ) !!}
				{!! Form::text('phone', old('phone', SiteCustomer::get('phone')), [ 'class'=>'form-control required' ] ) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('message', Lang::get('web/pages.message') ) !!}
				{!! Form::textarea('message', old('message'), [ 'class'=>'form-control required', 'rows'=>4 ] ) !!}
			</div>
			<div class="alert alert-danger alert-dismissible form-error hide">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<div class="alert-content"></div>
			</div>
			<div class="form-group text-right">
				{!! Form::button(Lang::get('general.cancel'), [ 'class'=>'btn btn-default popup-modal-dismiss pull-left' ] ) !!}
				{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ] ) !!}
			</div>
		</div>
	{!! Form::close() !!}

	<script type="text/javascript">
		google.maps.event.addDomListener(window, 'load', function(){
			var mapLatLng = { lat: {{$property->lat}}, lng: {{$property->lng}} };

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

		});
	</script>

@endsection
