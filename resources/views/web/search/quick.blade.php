@if ( empty($no_title) )
	<h2>{{ Lang::get('web/search.quick.title') }}</h2>
@endif

{!! Form::model(null, [ 'action'=>'Web\PropertiesController@index', 'method'=>'GET', 'id'=>'quick-search-form' ]) !!}
	{!! Form::hidden('search', 1) !!}
	<div class="form-group error-container">
		{!! Form::text('term', null, [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/properties.term') ]) !!}
	</div>
	<div class="form-group error-container">
		{!! Form::select('mode', [''=>Lang::get('web/properties.mode')]+$search_data['modes'], null, [ 'class'=>'form-control has-placeholder' ]) !!}
	</div>
	<div class="form-group error-container">
		{!! Form::select('type', [''=>Lang::get('web/properties.type')]+$search_data['types'], null, [ 'class'=>'form-control has-placeholder' ]) !!}
	</div>
	<div class="form-group error-container">
		{!! Form::select('state', [''=>Lang::get('web/properties.state')]+$search_data['states'], null, [ 'class'=>'form-control has-placeholder' ]) !!}
	</div>
	<div class="form-group error-container">
		{!! Form::select('city', [''=>Lang::get('web/properties.city')], null, [ 'class'=>'form-control has-placeholder' ]) !!}
	</div>
	<div class="text-right">
		<a href="#" class="more-options pull-left text-bold advanced-search-trigger">{{ Lang::get('web/search.quick.more') }} &raquo;</a>
		{!! Form::submit(Lang::get('web/search.button'), [ 'class'=>'btn btn-primary text-uppercase' ]) !!}
	</div>
{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#quick-search-form');
		var cities = {};

		form.validate({
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

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
					data: { state_slug: state },
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

		form.on('click', '.advanced-search-trigger', function(e){
			e.preventDefault();

			var search_modal = $('#advanced-search-modal');

			form.find('input, select').each(function(){
				var nm = $(this).attr('name');
				if ( !nm ) return true;

				var target = search_modal.find('[name="' + $(this).attr('name') + '"]');
				if ( !target.length ) return true;

				var val = $(this).val();

				if ( target.prop("tagName").toLowerCase() == 'select' ) {
					target.html( $(this).html() );
				}

				if ( val ) {
					target.removeClass('is-placeholder');
				} else {
					target.addClass('is-placeholder');
				}

				target.val( val );
			});

			$('#advanced-search-trigger').trigger('click');

		});
	});
</script>
