@extends('layouts.web', [
	'menu_section' => 'home',
])

@section('content')

	<div id="home">

		@if ( count($properties) > 0 )
			<?php
				$main_property = $properties->shift()
			?>
			<div class="main-property carousel slide" data-ride="carousel">
				<div class="carousel-inner" role="listbox">
					<div class="item active" style="background-image: url('{{$main_property->main_image}}');">
						<img src="{{$main_property->main_image}}" alt="{{$main_property->title}}" class="hide" />
						<div class="carousel-caption">
							<a href="{{ action('Web\PropertiesController@details', $main_property->slug) }}" class="carousel-caption-text">
								{{$main_property->title}}
								<span class="text-nowrap hidden-xs"> | {{ price($main_property->price, [ 'decimals'=>0 ]) }}</span>
							</a>
						</div>
					</div>
				</div>
			</div>

			@if ( count($properties) > 0 )
				<div class="container">
					<div class="properties-slider-area">
						<h2>{{ Lang::get('web/home.gallery') }}</h2>
						<div id="properties-slider" class="properties-slider carousel slide" data-ride="carousel">
							<div class="carousel-inner" role="listbox">
								<div class="item active">
									<div class="row">
										@foreach ($properties as $key => $property)
											@if ( $key > 0 && $key%3 == 0 )
												</div></div><div class="item"><div class="row">
											@endif
											<div class="col-xs-12 col-sm-4">
												<div class="relative">
													@include('web.properties.pill', [ 'item'=>$property])
													<a class="left carousel-control visible-xs" href="#properties-slider" role="button" data-slide="prev">
														&lsaquo;
														<span class="sr-only">{{ Lang::get('pagination.previous') }}</span>
													</a>
													<a class="right carousel-control visible-xs" href="#properties-slider" role="button" data-slide="next">
														&rsaquo;
														<span class="sr-only">{{ Lang::get('pagination.next') }}</span>
													</a>
												</div>
											</div>
										@endforeach
									</div>
								</div>
							</div>
							<a class="left carousel-control hidden-xs" href="#properties-slider" role="button" data-slide="prev">
								&lsaquo;
								<span class="sr-only">{{ Lang::get('pagination.previous') }}</span>
							</a>
							<a class="right carousel-control hidden-xs" href="#properties-slider" role="button" data-slide="next">
								&rsaquo;
								<span class="sr-only">{{ Lang::get('pagination.next') }}</span>
							</a>
						</div>
					</div>
				</div>
			@endif

		@endif

		<div class="container">
			<div class="search-area {{ count($properties) ? 'under-properties' : '' }}">
				<div class="row">
					<div class="col-xs-12 col-sm-9"></div>
					<div class="col-xs-12 col-sm-3">
						<h2>{{ Lang::get('web/home.search') }}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-4 hidden-xs">
						<a href="" class="quick-link quick-link-new">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.new') }} dsfasd fs</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
						<a href="" class="quick-link quick-link-rent">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.rent') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-4 hidden-xs">
						<a href="" class="quick-link quick-link-used">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.used') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
						<a href="" class="quick-link quick-link-houses">
							<div class="image"></div>
							<div class="text">{{ Lang::get('web/home.link.houses') }}</div>
							<div class="arrow">
								<span>&rsaquo;</span>
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-3 col-sm-offset-1">
						{!! Form::model(null, [ 'action'=>'Web\PropertiesController@index', 'method'=>'GET', 'id'=>'quick-search-form' ]) !!}
							{!! Form::hidden('search', 1) !!}
							<div class="form-group error-container">
								{!! Form::text('term', null, [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/properties.term') ]) !!}
							</div>
							<div class="form-group error-container">
								{!! Form::select('mode', [''=>Lang::get('web/properties.mode')]+$modes, null, [ 'class'=>'form-control has-placeholder' ]) !!}
							</div>
							<div class="form-group error-container">
								{!! Form::select('type', [''=>Lang::get('web/properties.type')]+$types, null, [ 'class'=>'form-control has-placeholder' ]) !!}
							</div>
							<div class="form-group error-container">
								{!! Form::select('state', [''=>Lang::get('web/properties.state')]+$states->toArray(), null, [ 'class'=>'form-control has-placeholder' ]) !!}
							</div>
							<div class="form-group error-container">
								{!! Form::select('city', [''=>Lang::get('web/properties.city')], null, [ 'class'=>'form-control has-placeholder' ]) !!}
							</div>
							<div class="text-right">
								<a href="#" class="more-options pull-left text-bold advanced-search-trigger">{{ Lang::get('web/home.search.more') }} &raquo;</a>
								{!! Form::submit(Lang::get('web/home.search.button'), [ 'class'=>'btn btn-primary text-uppercase' ]) !!}
							</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#home');
			var form = $('#quick-search-form');
			var cities = $('#quick-search-form');

			cont.find('.properties-slider .property-pill').matchHeight({ byRow : false });

			cont.find('.search-area .quick-link').matchHeight({ byRow : false });

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
				alert('[TODO] advanced search');
			});
		});
	</script>

@endsection
