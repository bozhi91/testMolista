@extends('layouts.web', [
	'menu_section' => 'properties',
])

@section('content')

	<div id="properties">

		<div class="search-area">
			<div class="container">
				<h2>{{ Lang::get('web/properties.search.results') }}</h2>
				<div class="form-area" style="opacity: 0;">
					@include('web.search.form')
					<a href="#" class="form-area-minimizer text-center"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a>
				</div>
			</div>
		</div>

		<div class="results-area">
			<div class="container">
				@if ( count($properties) < 1)
					<div class="alert alert-info">{{ Lang::get('web/properties.empty') }}</div>

				@else
					<ul class="list-unstyled property-list">
						@foreach ($properties as $property)
							<li>
								@include('web.properties.row', [ 'item'=>$property ])
							</li>
						@endforeach
					</ul>
					<div class="pagination-area text-center">
						{!! $properties->appends( Input::except('page') )->render() !!}
					</div>
				@endif
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#properties');

			cont.find('.form-area').addClass('closed').css({ opacity: 1 });

			cont.on('focus', '.first-input-line input', function(e){
				cont.find('.form-area').removeClass('closed').find('select.has-select-2').select2();
			});

			cont.on('click', '.form-area-minimizer', function(e){
				e.preventDefault();
				cont.find('.form-area').addClass('closed');
			});

			function fixPropertiesSize() {
				if ( cont.find('.results-area .property-column').length < 1 ) {
					return false;
				}

				if ( cont.find('.results-area .property-column .metrics').eq(0).is(':visible') ) {
					cont.find('.results-area .property-column').matchHeight({ byRow : false });
				} else {
					cont.find('.results-area .property-column').css({ height: 'auto' });
				}
			}
			$(window).resize(fixPropertiesSize);
			fixPropertiesSize();

		});
	</script>

@endsection
