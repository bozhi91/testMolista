@extends('layouts.account')

@section('account_content')

	<div id="account-reports" class="account-reports">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/reports.referers.h1') }}</h1>

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs">
				<li class="{{ ($current_tab == 'general') ? 'active' : '' }}"><a href="{{ action('Account\Reports\ReferersController@getIndex') }}">{{ Lang::get('account/reports.referers.tab.general') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main {{ ($current_tab == 'general') ? 'active' : '' }}">

					<div class="text-right">
						<div class="btn-group" role="group">
							<a href="{{ action('Account\Reports\ReferersController@getIndex', [ 'period'=>'7-days']) }}" class="btn {{ ($period == '7-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.7') }}</a>
							<a href="{{ action('Account\Reports\ReferersController@getIndex', [ 'period'=>'30-days']) }}" class="btn {{ ($period == '30-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.30') }}</a>
							<a href="{{ action('Account\Reports\ReferersController@getIndex', [ 'period'=>'60-days']) }}" class="btn {{ ($period == '60-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.60') }}</a>
							<a href="{{ action('Account\Reports\ReferersController@getIndex', [ 'period'=>'90-days']) }}" class="btn {{ ($period == '90-days') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.days.90') }}</a>
							<a href="{{ action('Account\Reports\ReferersController@getIndex', [ 'period'=>'year-to-date']) }}" class="btn {{ ($period == 'year-to-date') ? 'btn-primary' : 'btn-default' }}">{{ Lang::get('account/reports.time.year.to.date') }}</a>
						</div>
					</div>

					<div class="stats-area">
						<p>&nbsp;</p>

						@if ( $stats )
							<?php
								$valid_headers = @array_shift(array_values($stats));

								$headers = array_filter([
									'origin' => isset($valid_headers['origin']) ? [ 'title' => Lang::get("account/reports.referers.origin"), 'class'=>'text-capitalize', 'sortable'=>false, ] : false,
									'leads' => isset($valid_headers['leads']) ? [ 'title' => Lang::get("account/reports.referers.leads"), 'class'=>'text-center', 'sortable'=>false, ] : false,
									'sold' => isset($valid_headers['sold']) ? [ 'title' => Lang::get("account/reports.referers.sold"), 'class'=>'text-center', 'sortable'=>false, ] : false,
									'rent' => isset($valid_headers['rent']) ? [ 'title' => Lang::get("account/reports.referers.rent"), 'class'=>'text-center', 'sortable'=>false, ] : false,
									'transfer' => isset($valid_headers['transfer']) ? [ 'title' => Lang::get("account/reports.referers.transfer"), 'class'=>'text-center', 'sortable'=>false, ] : false,
									'other' => isset($valid_headers['other']) ? [ 'title' => Lang::get("account/reports.referers.other"), 'class'=>'text-center', 'sortable'=>false, ] : false,
								]);
							?>
							<table class="table table-striped">
								<thead>
									<tr>
										{!! drawSortableHeaders(null, $headers) !!}
									</tr>
								</thead>
								<tbody>
									@foreach ($stats as $stat)
										<tr>
											@foreach ($headers as $header => $def)
												<td class="{{ @$def['class'] }}">
													@if ( $header == 'origin' )
														{{ $stat[$header] }}
													@else
														{{ @number_format($stat[$header],0,',','.') }}
													@endif
												</td>
											@endforeach
										</tr>
									@endforeach
								</tbody>
							</table>
						@else
							<div class="alert alert-info">
								{{ Lang::get('account/reports.empty') }}
							</div>
						@endif

					</div>

				</div>

			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#account-reports');
		});
	</script>

@endsection