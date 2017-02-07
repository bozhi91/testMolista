<div id="home-top-search">
	<div class="home-top-inner">
		<div class="container">
			<div class="row">
				{!! Form::model(null, [ 'action'=>'Web\PropertiesController@index', 'method'=>'GET', 'id'=>'home-search-form' ]) !!}
				<div class="hidden-xs col-sm-12">
					<div class="row home-top-search-top-block">

						<div class="col-sm-5 block-input">
							<div class="block-input-inner">
								{!! Form::text('term', Input::get('term'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/properties.term') ]) !!}
							</div>
						</div>

						<div class="col-sm-3">
							<div class="block-trigger-modal">
								<a href="#advanced-search-modal" id="advanced-search-opener"> <span class="block-trigger-modal-icon"><i class="fa fa-plus" aria-hidden="true"></i></span> <span class="block-trigger-modal-text">Más opciones</span> </a>
							</div>
						</div>

					</div>

					<div class="row home-top-search-bottom-block">

						<div class="col-sm-2 block-selector">
							{!! Form::select('mode', [''=>Lang::get('web/properties.mode')]+$search_data['modes'], Input::get('mode'), [ 'class'=>'form-control has-placeholder' ]) !!}
						</div>

						<div class="col-sm-2 block-selector">
							{!! Form::select('type', [''=>Lang::get('web/properties.type')]+$search_data['types'], Input::get('type'), [ 'class'=>'form-control has-placeholder' ]) !!}
						</div>

						<div class="col-sm-2 block-selector">
							{!! Form::select('state', [''=>Lang::get('web/properties.state')]+$search_data['states'], Input::get('state'), [ 'class'=>'form-control has-placeholder' ]) !!}
						</div>

						<div class="col-sm-2 block-selector">
							{!! Form::select('city', [''=>Lang::get('web/properties.city')], Input::get('city'), [ 'class'=>'form-control has-placeholder' ]) !!}
						</div>

						<div class="col-sm-2 block-submit">
							{!! Form::submit('Buscar', [ 'class'=>'form-control home-top-search-submit' ]) !!}
						</div>

					</div>

				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

<div id="advanced-search-modal" class="mfp-hide app-popup-block search-popup-block">
	<h2>{{ Lang::get('web/search.title.popup') }}</h2>
	@include('web.search.form')
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){

		$('#advanced-search-opener').magnificPopup({
			type: 'inline',
			preloader: false,
			closeOnBgClick: false,
			callbacks: {
				open: function() {
					$('.if-overlay-then-blurred').addClass('blurred');

					var form = $('#home-search-form');
					var modal = $('#advanced-search-modal');

					modal.find('select.has-select-2').each(function(){
						$(this).select2();
					});

					/* Sending selections from home-search-form */
					modal.find('[name="term"]').val( form.find('[name="term"]').val() );
					modal.find('[name="mode"]').val( form.find('[name="mode"]').val() );
					modal.find('[name="type"]').val( form.find('[name="type"]').val() );
					modal.find('[name="state"]').val( form.find('[name="state"]').val() ).trigger('change');
					modal.find('[name="city"]').val( form.find('[name="city"]').val() );
				}
			}
		});

		var form = $('#home-search-form');
		var cities = {};

		form.on('change', 'select[name="state"]', function(){
			var state = $(this).val();
			var target = form.find('select[name="city"]');

			target.html('<option value="">' + target.find('option[value=""]').eq(0).text() + '</option>').addClass('is-placeholder');
			if ( !state ) {
				return;
			}

			if ( cities.hasOwnProperty(state) ) {
				$.each(cities[state], function(k,v) {
					target.append('<option value="' + v.code + '">' + v.label + '</option>');
				});
			} else {
				$.ajax({
					dataType: 'json',
					url: '{{ action('Ajax\GeographyController@getSuggest', 'city') }}',
					data: { 
						state_slug: state,
						site_id: {{ @intval($site_setup['site_id']) }}
					},
					success: function(data) {
						if ( data ) {
							cities[state] = data;
							$.each(cities[state], function(k,v) {
								target.append('<option value="' + v.code + '">' + v.label + '</option>');
							});
						}
					}
				});
			}
		});

	});
</script>