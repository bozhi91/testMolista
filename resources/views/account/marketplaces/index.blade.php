@extends('layouts.account')

@section('account_content')

	<div id="account-marketplaces">

        @include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/marketplaces.h1') }}</h1>

		@if ( $marketplaces->count() < 1)
			<div class="alert alert-info">{{ Lang::get('account/marketplaces.empty') }}</div>
		@else
			<table class="table table-striped">
				<thead>
					<tr>
						{!! drawSortableHeaders(url()->full(), [
							'title' => [ 'title' => Lang::get('account/marketplaces.title') ],
							'limit' => [ 'title' => Lang::get('account/marketplaces.limit'), 'class'=>'text-center' ],
							'properties' => [ 'title' => Lang::get('account/marketplaces.exported'), 'class'=>'text-center' ],
							'configured' => [ 'title' => Lang::get('account/marketplaces.configured'), 'class'=>'text-center' ],
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
							<td class="text-center">
								@if ( $marketplace->marketplace_maxproperties )
									{{ number_format($marketplace->marketplace_maxproperties,0,',','.') }}
								@else
									-
								@endif
							<td class="text-center">
								{{ number_format($marketplace->properties->count(),0,',','.') }}
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