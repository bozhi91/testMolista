@extends('layouts.account')

@section('account_content')

	<div id="user-profile">

		@if (session('status'))
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
				{{ session('status') }}
			</div>
		@else
			@include('common.messages', [ 'dismissible'=>true ])
		@endif

		<h1 class="page-title">{{ Lang::get('account/menu.data') }}</h1>

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs">
				<li class="{{ $current_tab == 'data' ? 'active' : '' }}"><a href="{{ action('AccountController@index') }}">{{ Lang::get('account/payment.data') }}</a></li>
				@role('company')
					<li class="{{ $current_tab == 'plan' ? 'active' : '' }} hidden-xs"><a href="{{ action('Account\Profile\PlanController@getIndex') }}">{{ Lang::get('account/payment.plans') }}</a></li>
					<li class="{{ $current_tab == 'invoices' ? 'active' : '' }}"><a href="{{ action('Account\Profile\InvoicesController@getIndex') }}">{{ Lang::get('account/payment.invoices') }}</a></li>
				@endrole
			</ul>

			<div class="tab-content">
				@include("account.profile.tab-{$current_tab}")
			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#user-profile');
		});
	</script>
@endsection
