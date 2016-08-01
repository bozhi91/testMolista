@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#user-email-accounts {}
		#user-email-accounts .current-email-accounts li { margin-bottom: 5px; }
		#user-email-accounts .current-email-accounts .btn { white-space: normal; text-align: left; }
	</style>

	<div id="user-email-accounts">

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="pull-right">
			<a href="{{ action('Account\Profile\AccountsController@getCreate') }}" class="btn btn-primary">{{ Lang::get('account/profile.accounts.button.new') }}</a>
		</div>

		<h1 class="page-title">{{ empty($page_title) ? Lang::get('account/menu.data.accounts') : $page_title }}</h1>

		@if ( count($accounts) < 1 )
			@yield('email_accounts_content')
		@else
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-md-3">
					<ul class="list-unstyled current-email-accounts">
						@foreach ($accounts as $item)
							<li>
								<a href="{{ action('Account\Profile\AccountsController@getEdit', $item->id) }}" class="btn btn-sm btn-block {{ @$account->id == $item->id ? 'btn-primary' : 'btn-default' }}">
									{{ $item->from_email }}
								</a>
							</li>
						@endforeach
					</ul>
				</div>
				<div class="col-xs-12 col-sm-8 col-md-9">
					@yield('email_accounts_content')
				</div>
			</div>
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#user-email-accounts');
		});
	</script>

@endsection
