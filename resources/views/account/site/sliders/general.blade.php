<?php ?>

<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('title', Lang::get('account/site.sliders.label.title')) !!}
			{!! Form::text('title', isset($group) ? $group->name : '', [ 'class'=>'form-control required']) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('languages', Lang::get('account/site.sliders.label.languages')) !!}
			{!! Form::select('languages[]', $languages, isset($languagesCurrent) ? $languagesCurrent : null, [
			'class'=>'form-control has-select-2 required', 
			'multiple' => 'multiple',
			]) !!}
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-7">
		<h4>{{ Lang::get('account/site.sliders.general.sliders') }}</h4>
		<hr>
		<div class="alert alert-info images-empty">
			{{ Lang::get('account/site.sliders.general.empty') }}
		</div>

		<ul class="image-gallery sortable-image-gallery slider-image-gallery">
			@if (!empty($group) && count($group->images) > 0 )
				@foreach ($group->images->sortBy('position') as $image)
			
				@include('account.site.sliders.thumb',[
					'image_url' => $image->image,
					'image_id' => $image->id,
					'image_link' => $image->link,
				])
			
				@endforeach
			@endif
		</ul>
		<div class="visible-xs-block">
			<p>&nbsp;</p>
		</div>
	</div>
	<div class="col-xs-12 col-sm-5">
		<h4>{{ Lang::get('account/site.sliders.upload') }}</h4>
		<hr>
		<div class="dropzone-previews" id="dropzone-previews"></div>
		<div class="help-block">{{ Lang::get('account/properties.images.dropzone.helper') }}</div>
	</div>
</div>


<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#sliders-form');
		
		//Select2
		form.find('.has-select-2').select2();
		
		// Image gallery
		form.find('.image-gallery').sortable({
			stop: initImageWarnings
		});
		
		form.find('.image-gallery .thumb').each(function(){
			$(this).magnificPopup({
				type: 'image',
				closeOnContentClick: false,
				mainClass: 'mfp-img-mobile',
				image: {
					verticalFit: true
				}
			});
		});
		
		form.on('click', '.image-delete-trigger', function(e){
			var el = $(this);
			e.preventDefault();
			SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.images.delete') ) }}", function (e) {
				if (e) {
					el.closest('.handler').remove();
					initImageWarnings();
				}
			});
		});


		// Drop zone
		Dropzone.autoDiscover = false;
		$("#dropzone-previews").addClass('dropzone').dropzone({
			url: '{{ action('Account\Site\SlidersController@upload') }}',
			params: {
				_token: '{{ Session::getToken() }}'
			},
			maxFilesize: {{ Config::get('app.slider_image_maxsize') / 1024 }},
			acceptedFiles: 'image/*',
			dictFileTooBig: "{{ print_js_string( Lang::get('account/properties.images.dropzone.error.size', [ 'IMAGE_MAXSIZE'=>Config::get('app.slider_image_maxsize') ]) ) }}",
			dictDefaultMessage: "{{ print_js_string( Lang::get('account/properties.images.dropzone.helper') ) }}",
			error: function(file, response) {
				if ( $.type(response) === 'string') {
					if ( response.length > 500 ) {
						alertify.error("{{ print_js_string( Lang::get('account/properties.images.dropzone.error.size', [ 'IMAGE_MAXSIZE'=>Config::get('app.slider_image_maxsize') ]) ) }}");
					} else {
						alertify.error(response);
					}
				} else if ( $.type(response) === 'object' && response.message ) {
					alertify.error(response.message);
				} else {
					alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
				}

				$(file.previewElement).fadeOut(function(){
					$(this).remove()
				});
			},
			canceled: function(file) {
				$(file.previewElement).fadeOut(function(){
					$(this).remove()
				});
			},
			success: function(file,response) {
				var item = $(response.html);

				item.find('.thumb').magnificPopup({
					type: 'image',
					closeOnContentClick: false,
					mainClass: 'mfp-img-mobile',
					image: {
						verticalFit: true
					}
				});

				form.find('.image-gallery').append(item);

				$(file.previewElement).fadeOut(function(){
					$(this).remove()
				});

				initImageTooltips();
				initImageWarnings();
			}
		});
		
		function initImageWarnings() {
			form.find('.images-warning-size, .images-warning-orientation').addClass('hide');

			if ( form.find('.image-gallery .thumb').length < 1 ) {
				form.find('.images-empty').show();
			} else {
				form.find('.images-empty').hide();
				var fh = form.find('.slider-image-gallery li.handler:first-child');
				if ( fh.hasClass('handler-orange') ) {
					form.find('.images-warning-size').removeClass('hide');
				} else if ( fh.hasClass('handler-red') ) {
					form.find('.images-warning-orientation').removeClass('hide');
				}
			}
		}
		function initImageTooltips() {
			form.find('.thumb-has-tooltip').removeClass('thumb-has-tooltip').tooltip();
		}
		initImageWarnings();
		initImageTooltips();
	});
</script>