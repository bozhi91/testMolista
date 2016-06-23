@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		.marketplace-name { display: inline-block; padding-left: 25px; background: left center no-repeat; }
	</style>

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
							'configured' => [ 'title' => Lang::get('account/marketplaces.configured'), 'sortable'=>false, 'class'=>'text-center' ],
							'action' => [ 'title' => '', 'sortable'=>false ],
						]) !!}
					</tr>
				</thead>
				<tbody>
					@foreach ($marketplaces as $marketplace)
						<tr>
							<td><span class="marketplace-name" style="background-image: url({{ asset("marketplaces/{$marketplace->logo}") }});">{{ $marketplace->name }}</span></td>
							<td class="text-center"><span class="glyphicon glyphicon-{{ $marketplace->marketplace_enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
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