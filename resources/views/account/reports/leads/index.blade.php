@extends('layouts.account')

@section('account_content')

	<div id="account-reports" class="account-reports">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/reports.leads.h1') }}</h1>

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs">
				<li class="{{ ($current_tab == 'general') ? 'active' : '' }}"><a href="{{ action('Account\Reports\LeadsController@getIndex','7-days') }}">{{ Lang::get('account/reports.leads.tab.general') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main active">
					@include("account.reports.leads.tab-{$current_tab}")
				</div>

			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#account-reports');

			cont.on('click','.main-tabs a',function(e){
				if ( $(this).closest('li').hasClass('active') ) {
					e.preventDefault();
				}
			});
		});
	</script>

@endsection