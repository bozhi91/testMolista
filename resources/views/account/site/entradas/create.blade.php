@extends('layouts.account', [
	'use_google_maps' => true,
])

@section('account_content')

	<?php
		$title =   Lang::get('general.defaultPostTitle') ;
		$body  = "";
		$postAction = 'Account\Site\BlogController@storePost';

		if(!empty($_GET ['action'])){
		    if($_GET ['action'] == 'edit'){
                $postAction = 'Account\Site\BlogController@updatePost';
                $post = App\Http\Controllers\Account\Site\BlogController::getPostById($_GET ['post_id']);
		        $title = $post[0]->title;
				$body  = $post[0]->body;
		    }
		}
	?>

	<div id="admin-pages">
		@include('common.messages', [ 'dismissible'=>true ])
		<h2>{{ Lang::get('general.newPost') }}</h2>

		{!! Form::model(null, [ 'method'=>'POST', 'action'=>$postAction, 'id'=>'create-form' ]) !!}
			<br/>
			<div>
				<b>{{ Lang::get('general.postTitle') }}</b><br/>


				<input type="text" name="title", value='<?php echo $title;?>'  style="width:100%"><br/><br/>

				<b> {{ Lang::get('general.postBody') }}</b><br/>
				<textarea name="body" class="summernote" style="height: 300px !important;" contenteditable="false">
					{{ $body}}
				</textarea><br/><br/>

				@if(!empty($_GET['post_id']))
					{{ Form::input('hidden', 'post_id',$_GET['post_id']) }}
				@endif

				{!! Form::submit(  Lang::get('general.save'), [ 'class'=>'btn btn-primary']) !!}
			</div>
		{!! Form::close() !!}
        <?php $params = array("type"=>"blog","action"=>"list");?>
	</div>

	<!-- Includes for the HTML Editor -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>
	<!-- Includes for the HTML Editor -->

		<script>
			$(document).ready(function() {
				$('.summernote').summernote(
                    {
                        height: 200,   //set editable area's height
                        codemirror: { // codemirror options
                            theme: 'monokai'
                        }
                    }
                );

			});
        </script>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#edit-form');
			var geocoder = new google.maps.Geocoder();

			// Enable first language tab
			form.find('.locale-tabs').each(function(){
				$(this).find('a').eq(0).trigger('click');
			});

			// Form validation
			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				invalidHandler: function(e, validator){
					if ( validator.errorList.length ) {
						var el = $(validator.errorList[0].element);
						form.find('.main-tabs a[href="#' + el.closest(".tab-main").attr('id') + '"]').tab('show');
						if ( el.closest('.tab-locale').length ) {
							form.find('.locale-tabs a[href="#' + el.closest(".tab-locale").attr('id') + '"]').tab('show');
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.find('.is-wysiwyg').each(function(){
				var el = $(this);

				$(this).summernote({
					height: 450,
					lang: '{{ summetime_lang() }}',
					callbacks: {
						onChange: function(content) {
							el.val( content );
						}
					}
				});
			});

			var gmap = $('#gmap');
			if ( gmap.length )
			{
				var mapLatLng = {
					lat: parseFloat( form.find('.input-lat').val() || {{ config('app.lat_default') }} ),
					lng: parseFloat( form.find('.input-lng').val() || {{ config('app.lng_default') }} )
				};

				var map = new google.maps.Map(document.getElementById('gmap'), {
					zoom: parseInt( form.find('.input-zoom').val() ),
					center: mapLatLng
				});

				var marker = new google.maps.Marker({
					position: mapLatLng,
					map: map,
					draggable: true
				});

				marker.addListener('dragend',function(event) {
					form.find('.input-lat').val( event.latLng.lat() );
					form.find('.input-lng').val( event.latLng.lng() );
				});

				form.on('change', '.input-zoom', function(){
					map.setZoom( parseInt( $(this).val() ) );
				});

				form.find('.map-address-trigger').magnificPopup({
					type: 'inline',
					preloader: false,
					focus: '#address-input',
					callbacks: {
						beforeOpen: function() {
							$('#address-input').val('');
						}
					}
				});
			}

			$('#map-address-form').validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					if ( gmap.length ) {
						LOADING.show();
						geocoder.geocode({
							'address': $('#address-input').val()
						}, function(results, status) {
							LOADING.hide();
							$.magnificPopup.close();
							if (status === google.maps.GeocoderStatus.OK) {
								map.setCenter( results[0].geometry.location );
								marker.setPosition( results[0].geometry.location );
								form.find('.input-lat').val( results[0].geometry.location.lat() );
								form.find('.input-lng').val( results[0].geometry.location.lng() );
							} else {
								var message = "{{ print_js_string( Lang::get('account/properties.geolocate.error') ) }}: "+status;
								switch (status) {
									case 'ZERO_RESULTS':
										message = "{{ print_js_string( Lang::get('account/properties.geolocate.no_results') ) }}";
										break;
								}
								alertify.error(message);
							}
						});
					} else {
						return false;
					}
				}
			});

		});
	</script>

@endsection
