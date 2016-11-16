@extends('layouts.account')

@section('account_content')

	<div id="account-marketplaces">

        @include('common.messages', [ 'dismissible'=>true ])
        <div class="row">
        	<div class="col-md-8">
				<h1 class="page-title">{{ Lang::get('account/marketplaces.h1') }}</h1>

				<div class="search-filters">
					@if ( @$clean_filters )
						<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
					@endif
					<h2>{{ Lang::get('general.filters') }}</h2>
					{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
						{!! Form::hidden('limit', Input::get('limit')) !!}
						<div class="form-group">
							{!! Form::label('title', Lang::get('account/marketplaces.title'), [ 'class'=>'sr-only' ]) !!}
							{!! Form::text('title', Input::get('title'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('account/marketplaces.title') ]) !!}
						</div>
						<div class="form-group">
							{!! Form::label('country', Lang::get('account/marketplaces.country'), [ 'class'=>'sr-only' ]) !!}
							{!! Form::select('country', [ '' => Lang::get('account/marketplaces.country') ]+$countries, Input::get('country'), [ 'class'=>'form-control' ]) !!}
						</div>
						{!! Form::submit(Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default' ]) !!}
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-md-4">
				<div class="right">
					<table width="100%" class="adviser-contact">
						<tbody>
							<tr>
								<td align="right"><img src="/images/account/adviser.png"/></td>
								<td valign="middle" align="left">
									<h4>¿Puedo ayudarte?</h4>
									<p><i class="fa fa-phone" aria-hidden="true"></i> 93 180 70 20</p>
									<p><i class="fa fa-envelope" aria-hidden="true"></i> <a href="mailto:xavier@molista.com">xavier@molista.com</a></p>
								</td>
							<tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@if ( $marketplaces->count() < 1)
			<div class="alert alert-info">{{ Lang::get('account/marketplaces.empty') }}</div>
		@else

			@if ( $current_site->plan_property_limit > 0 )
				<div class="alert alert-info">
					{!! Lang::get('account/warning.export.limit', [
						'max_properties' => number_format(App\Session\Site::get('plan.max_properties'),0,',','.'),
					]) !!}
				</div>
			@endif

			<table class="table table-striped">
				<thead>
					<tr>
						<?php $limit_sortable = ($current_site->plan_property_limit > 0) ? false : true; ?>
						{!! drawSortableHeaders(url()->full(), [
							'title' => [ 'title' => Lang::get('account/marketplaces.title'), 'sortable'=>false ],
							'country' => [ 'title' => Lang::get('account/marketplaces.country'), 'sortable'=>false, 'class'=>'text-center' ],
							'limit' => [ 'title' => Lang::get('account/marketplaces.limit'), 'sortable'=>false, 'class'=>'text-center' ],
							'properties' => [ 'title' => Lang::get('account/marketplaces.exported').' <span class="glyphicon glyphicon-info-sign" data-container="body" data-toggle="popover" data-placement="top" data-content="'.\Lang::get('account/marketplaces.exported_info').'"></span>', 'sortable'=>false, 'class'=>'text-center' ],
							'all' => [ 'title' => Lang::get('account/marketplaces.all'), 'sortable'=>false, 'class'=>'text-center' ],
							'configured' => [ 'title' => Lang::get('account/marketplaces.configured'), 'sortable'=>false, 'class'=>'text-center' ],
							'updated' => [ 'title' => Lang::get('account/marketplaces.updated'), 'sortable'=>false, 'class'=>'text-center text-nowrap' ],
							'action' => [ 'title' => '', 'sortable'=>false ],
						]) !!}
					</tr>
				</thead>
				<tbody>
					@foreach ($marketplaces as $marketplace)
						<tr>
							<td>
								@if ( @$marketplace->url )
									<a href="{{ $marketplace->url }}" target="_blank" class="marketplace-name" style="background-image: url({{ asset("marketplaces/{$marketplace->logo}") }});">{{ $marketplace->name }}</a>
								@else
									<span class="marketplace-name" style="background-image: url({{ asset("marketplaces/{$marketplace->logo}") }});">{{ $marketplace->name }}</span>
								@endif
							</td>
							<td class="text-center"><img src="{{ asset($marketplace->flag) }}" alt="{{ $marketplace->country }}" title="{{ $marketplace->country }}" /></td>
							<td class="text-center">
								@if ( $current_site->plan_property_limit > 0 )
									@if ( $marketplace->marketplace_maxproperties && $marketplace->marketplace_maxproperties < $current_site->plan_property_limit )
										{{ number_format($marketplace->marketplace_maxproperties,0,',','.') }}
									@else
										{{ number_format($current_site->plan_property_limit,0,',','.') }}
									@endif
								@elseif ( $marketplace->marketplace_maxproperties )
									{{ number_format($marketplace->marketplace_maxproperties,0,',','.') }}
								@else
									-
								@endif
							<td class="text-center">
								@if ( $marketplace->marketplace_export_all )
									{{ number_format($total_properties,0,',','.') }}
								@else
									{{ number_format($marketplace->properties->unique()->count(),0,',','.') }}
								@endif
							</td>
							<td class="text-center">
								<span class="glyphicon glyphicon-{{ $marketplace->marketplace_export_all ? 'ok' : 'remove' }}" aria-hidden="true"></span>
							</td>
							<td class="text-center">
								<span class="glyphicon glyphicon-{{ $marketplace->marketplace_enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
							</td>
							<td class="text-center text-nowrap">
								@if ( file_exists($current_site->getXmlPropertiesFeedPath($marketplace->code)) )
									{{ date("d/m/Y H:i", filemtime($current_site->getXmlPropertiesFeedPath($marketplace->code)) ) }}
								@else
									-
								@endif
							</td>
							<td class="text-right text-nowrap">
								@if ( $marketplace->marketplace_enabled )
									{!! Form::open([ 'method'=>'POST', 'class'=>'delete-form', 'action'=>['Account\MarketplacesController@postForget', $marketplace->code] ]) !!}
										@if ( false )
											<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('account/marketplaces.forget.button') }}</button>
										@endif
										<a href="{{ action('Account\MarketplacesController@getConfigure', $marketplace->code) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>
									{!! Form::close() !!}
								@else
									<a href="{{ action('Account\MarketplacesController@getConfigure', $marketplace->code) }}" class="btn btn-primary btn-xs">{{ Lang::get('account/marketplaces.configure') }}</a>
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! drawPagination($marketplaces, Input::except('page')) !!}
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#account-marketplaces');

  			$('[data-toggle="popover"]').css('cursor', 'pointer').popover();

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('account/marketplaces.forget.warning') ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});

		});
	</script>

@endsection
