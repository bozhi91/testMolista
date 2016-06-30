@extends('layouts.account')

@section('account_content')

	<h1 class="page-title">{{ Lang::get('account/warning.employees.h1') }}</h1>

	<div class="intro">
		{!! Lang::get('account/warning.employees.intro', [
			'max_employees' => @$employees_allowed,
		]) !!}
	</div>

	<p>&nbsp;</p>
	<a href="{{ action('Account\PaymentController@getUpgrade') }}" class="btn btn-primary hide">{{ Lang::get('account/warning.button.upgrade') }}</a>

	<script type="text/javascript">
		ready_callbacks.push(function() {
		});
	</script>

@endsection
