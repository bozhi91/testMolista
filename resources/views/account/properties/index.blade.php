@extends('layouts.account')

@section('account_content')

	<div id="admin-properties" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			@if ( Auth::user()->can('property-create') && Auth::user()->canProperty('create') )
				<div class="pull-right">
					<a href="{{ action('Account\PropertiesController@create') }}" class="btn btn-primary">{{ Lang::get('account/properties.button.new') }}</a>
				</div>
			@endif

			<h1 class="page-title">
				{{ Lang::get('account/properties.h1') }}
				({{ $total_properties }})
			</h1>

			<div class="search-filters">
				@if ( !empty($clean_filters) )
					<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
				@endif
				<h2>{{ Lang::get('general.filters') }}</h2>
				{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
					{!! Form::hidden('limit', Input::get('limit')) !!}
					<div class="form-group">
						{!! Form::label('ref', Lang::get('account/properties.ref'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('ref', Input::get('ref'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/properties.ref') ]) !!}
					</div>
<!--					<div class="form-group">
						{!! Form::label('title', Lang::get('account/properties.title'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('title', Input::get('title'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/properties.title') ]) !!}
					</div>-->
					<div class="form-group">
						{!! Form::label('address', Lang::get('account/properties.address'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::text('address', Input::get('address'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/properties.address') ]) !!}
					</div>
					<div class="form-group">
						{!! Form::label('price', Lang::get('account/properties.price'), [ 'class'=>'sr-only' ]) !!}

						<div class="input-group input-group-with-select">
							<div class="input-group-select-left">
								{!! Form::select('operation', [
								'=' => '=', '<' => '<', '>' => '>', '<=' => '<=',
								'>=' => '>=',
							], Input::get('operation'), [ 'class'=>'form-control' ]) !!}
							</div>
							{!! Form::text('price', Input::get('price'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/properties.price')]) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('highlighted', Lang::get('account/properties.highlighted'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::select('highlighted', [
							'' => '',
							'2' => Lang::get('account/properties.highlighted'),
							'1' => Lang::get('account/properties.highlighted.not'),
						], Input::get('highlighted'), [ 'class'=>'form-control' ]) !!}
					</div>
					<div class="form-group">
						{!! Form::label('enabled', Lang::get('account/properties.enabled'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::select('enabled', [
							'' => '',
							'2' => Lang::get('account/properties.enabled'),
							'1' => Lang::get('account/properties.enabled.not'),
						], Input::get('enabled'), [ 'class'=>'form-control' ]) !!}
					</div>
					<div class="form-group">
						{!! Form::label('mode', Lang::get('account/properties.mode'), [ 'class'=>'sr-only' ]) !!}
						{!! Form::select('mode', array_merge(['' => ''], \App\Property::getModeOptionsAdmin()), Input::get('mode'), [ 'class'=>'form-control' ]) !!}
					</div>
					{!! Form::submit(Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::close() !!}
			</div>

			@if ( count($properties) < 1)
				<div class="alert alert-info">{{ Lang::get('account/properties.empty') }}</div>
			@else
				<table class="table table-striped">
					<thead>
						<tr>
							{!! drawSortableHeaders(url()->full(), [
								'reference' => [ 'title' => Lang::get('account/properties.ref') ],
								'address' => [ 'title' => Lang::get('account/properties.column.address') ],
								'price' => [ 'title' => Lang::get('account/properties.column.price') ],
								'lead' => [ 'title' => Lang::get('account/properties.tab.lead'), 'class'=>'text-center text-nowrap' ],
								'home_slider' => [ 'title' => Lang::get('account/properties.home.slider'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
								'highlighted' => [ 'title' => Lang::get('account/properties.highlighted'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
								'image' => [ 'title' => Lang::get('account/properties.image'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
								'enabled' => [ 'title' => Lang::get('account/properties.enabled'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
								'action' => [ 'title' => '', 'sortable'=>false ],
							]) !!}
						</tr>
					</thead>
					<tbody>
						@foreach ($properties as $property)
							<tr>
								<td>{{ $property->ref }}</td>
								<td>{!! implode( [
									$property->address,
									@implode(' / ', array_filter([ $property->city->name, $property->state->name ]))
									], '<br>') !!}</td>
								<td>{{ $property->price }}</td>
								<td class="text-center">{{ number_format($property->customers->count(), 0, ',', '.')  }}</td>
								<td class="text-center">
									@if ( Auth::user()->can('property-edit') && Auth::user()->canProperty('edit') )
										<a href="#" data-url="{{ action('Account\PropertiesController@getChangeHomeSlider', $property->slug) }}" class="change-status-trigger">
											<span class="glyphicon glyphicon-{{ $property->home_slider ? 'ok' : 'remove' }}" aria-hidden="true"></span>
										</a>
									@else
										<span class="glyphicon glyphicon-{{ $property->highlighted ? 'ok' : 'remove' }}" aria-hidden="true"></span>
									@endif
								</td>
								<td class="text-center">
									@if ( Auth::user()->can('property-edit') && Auth::user()->canProperty('edit') )
										<a href="#" data-url="{{ action('Account\PropertiesController@getChangeHighlight', $property->slug) }}" class="change-status-trigger">
											<span class="glyphicon glyphicon-{{ $property->highlighted ? 'ok' : 'remove' }}" aria-hidden="true"></span>
										</a>
									@else
										<span class="glyphicon glyphicon-{{ $property->highlighted ? 'ok' : 'remove' }}" aria-hidden="true"></span>
									@endif
								</td>
								<td class="text-center">
									<a href="{{ $property->main_image }}" target="_blank" class="property-table-thumb"
									   style="background-image: url('{{ $property->main_image_thumb }}')"></a>
								</td>
								<td class="text-center">
									@if ( Auth::user()->can('property-edit') && Auth::user()->canProperty('edit') )
										<a href="#" data-url="{{ action('Account\PropertiesController@getChangeStatus', $property->slug) }}" class="change-status-trigger">
											<span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
										</a>
									@else
										<span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
									@endif
								</td>
								<td class="text-right text-nowrap">
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" 
												aria-haspopup="true" aria-expanded="false">
											{{ Lang::get('general.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a class="dropdown-menu-link" href="{{ action('Account\PropertiesController@show', $property->slug) }}">
													<i class="fa fa-eye" aria-hidden="true"></i> {{ Lang::get('general.view') }}
												</a>
											</li>
											
											@if ( (($current_site_user->properties->where('id',$property->id)->count() > 0 && Auth::user()->canProperty('edit')) || Auth::user()->canProperty('edit_all')) && Auth::user()->can('property-edit') )
											<li>
												<a class="dropdown-menu-link" href="{{ action('Account\PropertiesController@edit', $property->slug) }}">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i> {{ Lang::get('general.edit') }}
												</a>
											</li>
											@endif
											
											@if ( Auth::user()->can('property-create') && Auth::user()->canProperty('create') )
											<li>
												<a class="dropdown-menu-link" href="{{ action('Account\PropertiesController@create', ['slug' => $property->slug]) }}" >
													<i class="fa fa fa-files-o" aria-hidden="true"></i> {{ Lang::get('general.copy') }}
												</a>
											</li>
											@endif
																						
											@if ( (($current_site_user->properties->where('id',$property->id)->count() > 0 && Auth::user()->canProperty('delete')) || Auth::user()->canProperty('delete_all')) && Auth::user()->can('property-delete') )
												{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 
													'action'=>['Account\PropertiesController@destroy', $property->slug] ]) !!}

												<button type="submit" class="btn btn-link dropdown-menu-button">
													<i class="fa fa-trash-o" aria-hidden="true"></i> {{ Lang::get('general.delete') }}
												</button>
												{!! Form::close() !!}
											@endif
										</ul>
									</div>
									
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											{{ Lang::get('general.share') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a class="dropdown-menu-link" href="{{Share::load($property->full_url)->facebook() }}">
													<i class="fa fa-facebook" aria-hidden="true"></i> Facebook
												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
                {!! drawPagination($properties, Input::except('page'), action('Account\PropertiesController@index', [ 'csv'=>1 ])) !!}
			@endif

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-properties');

			//Share dialog
			cont.find('.share-social-link').on('click', function(e){
				var popupSize = { width: 780, height: 550 };
				var verticalPos = Math.floor(($(window).width() - popupSize.width) / 2);
				var horisontalPos = Math.floor(($(window).height() - popupSize.height) / 2);

				var popup = window.open($(this).prop('href'), 'social',
					'width='+popupSize.width+',height='+popupSize.height+
					',left='+verticalPos+',top='+horisontalPos+
					',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

				if (popup) {
					popup.focus();
					e.preventDefault();
				}
			})

			cont.find('.property-table-thumb').each(function(){
				$(this).magnificPopup({
					type: 'image',
					closeOnContentClick: false,
					mainClass: 'mfp-img-mobile',
					image: {
						verticalFit: true
					}
				});
			});

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.delete') ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});

			cont.on('click', '.change-status-trigger', function(e){
				e.preventDefault();

				LOADING.show();

				var el = $(this);

				$.ajax({
					dataType: 'json',
					url: el.data().url,
					success: function(data) {
						LOADING.hide();
						if (data.success) {
							if (data.enabled || data.highlighted || data.home_slider) {
								el.find('.glyphicon').removeClass('glyphicon-remove').addClass('glyphicon-ok');
							} else {
								el.find('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-remove');
							}
						} else {
							if ( data.error_message ) {
								alertify.error(data.error_message);
							} else {
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						}
					},
					error: function() {
						LOADING.hide();
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				});

			});

		});
	</script>

@endsection
