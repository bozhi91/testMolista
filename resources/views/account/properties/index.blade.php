@extends('layouts.account')
@section('account_content')

	<?php
    	use Illuminate\Support\Facades\DB;

		$site_id = session("SiteSetup")['site_id'];
		$plan_id = session("SiteSetup")['plan']['id'];

    	$result = DB::table('properties')
			->select('id')
			->where('site_id',$site_id)
			->get();

    	$numProperties = count($result);

    	$site_plan = DB::table('sites')
			->select('plan_id')
			->where('id',$site_id)
			->first();

		$plan = DB::table('plans')
			->join('sites', 'plans.id', '=', 'sites.plan_id')
			->select('plans.max_properties')
			->where('sites.id',$site_id)
			->first();

    //check if the site is blocked
    $isBlocked = DB::table('sites')
        ->select('blocked_site')
        ->where('id',session("SiteSetup")['site_id'])
        ->first();

    	$propertyLimit = $plan->max_properties;

		if($plan->max_properties==null || $propertyLimit==0){
			$propertyLimit = 10000;
		}
		$props = App\Http\Controllers\Account\PropertiesController::getRecentProperties();
		$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

	?>

	<div id="admin-properties" class="row">
		<div class="col-xs-12">

        <?php $message  = " <p><a href='".$protocol.$_SERVER['HTTP_HOST']."/account/payment/upgrade' target='_blank'>
					<button type='button' class='btn btn-info .btn-md' style='margin-top:10px !important;'>".Lang::get('account/properties.update')."</button>
					</a></p>";
        ?>

	        @include('common.messages', [ 'dismissible'=>true ])
			@if(Auth::user()->can('property-create') && Auth::user()->canProperty('create') )
				<div class="pull-right">

					@if(!empty($plan))
						@if( $total_properties<$propertyLimit )<!-- The plan is free -->
							<a href="{{ action('Account\PropertiesController@create') }}" class="btn btn-primary">{{ Lang::get('account/properties.button.new') }}</a>
						@else
							<?php $message  = " <p><a href='".$protocol.$_SERVER['HTTP_HOST']."/account/payment/upgrade' target='_blank'>
								<button type='button' class='btn btn-info .btn-md' style='margin-top:10px !important;'>".Lang::get('account/properties.update')."</button>
								</a></p>";
							?>
							@include('Modals.commonModal', ['header'=>Lang::get('account/properties.accessDenied') ,
									 'message'=> Lang::get('account/properties.propMessage_3').$message])
							<a onclick="$('#commonModal').modal();" class="btn btn-primary">{{ Lang::get('account/properties.button.new') }}</a>
						@endif
					@endif
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
								'marketplaces' => [ 'title' => 'Marketplaces', 'sortable'=>false ],
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
								<td>
									@if($property->desde == 1)
										{{ Lang::get('web/properties.from') }}
									@endif
									{{ $property->price }}
										@if($property->mode=='vacationRental')
											/{{ Lang::get('web/properties.week') }}
										@endif
								</td>
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
									<a href="{{ $property->main_image }}" target="_blank" class="property-table-thumb" style="background-image: url('{{ $property->main_image_thumb }}')"></a>
								</td>
								<td class="text-center">
									@if ( Auth::user()->can('property-edit') && Auth::user()->canProperty('edit') )
										<a href="#" data-url="{{ action('Account\PropertiesController@getChangeStatus', $property->slug) }}" class="change-status-trigger">
											<!-- if props>5 and plan = free-->
											@if(!empty($plan))
												@if( ($total_properties>$propertyLimit) )
													<span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
												@endif
												@if($total_properties<$propertyLimit )
													<span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
												@endif
											@endif
										</a>
									@else
										<span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
									@endif
								</td>

								<td style="overflow: auto;max-height: 91px;	float:left;">
                                    <?php
                                    $result = App\Http\Controllers\Account\PropertiesController::getMarketplaces($property->id);
                                    $path   = "properties/".$property->slug."/edit?market=true";

                                    foreach ($result as $res){
                                        $url = "http://".$res->subdomain.".Contromia.com/marketplaces/".$res->logo;
                                        echo "<a target='_blank'  href={$path}>
											<span class='marketplace-name text-nowrap;' title='".$res->name."'style='background-image: url(".$url.")'/>&nbsp;
                                        	</span></a>";
                                    }
                                    ?>
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

											<li>
												<?php
													$flatUrl="";
													if(!empty($property)){
														$flatUrl = App\Http\Controllers\Account\PropertiesController::viewPropertyInWeb($property->id);
													}
												?>
												<a class="dropdown-menu-link" href= {{$flatUrl}} target="_blank">
													<i class="fa fa-eye" aria-hidden="true"></i> {{ Lang::get('general.viewinweb') }}
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

			if( ($(".page-title").text().split("(")[1].split(")")[0] > {{$propertyLimit}}) ){
                $('#propertyModal').modal();
            }

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
