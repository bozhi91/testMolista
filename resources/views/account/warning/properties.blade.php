@extends('layouts.account')

@section('account_content')

	<h1 class="page-title">{{ Lang::get('account/warning.properties.h1') }}</h1>

	<div class="intro">
		{!! Lang::get('account/warning.properties.intro', [
			'max_properties' => @$properties_allowed,
		]) !!}
	</div>

	<p>&nbsp;</p>
	<a href="{{ action('Account\PaymentController@getUpgrade') }}" class="btn btn-primary hide">{{ Lang::get('account/warning.button.upgrade') }}</a>

	<script type="text/javascript">
		ready_callbacks.push(function() {
		});
	</script>

@endsection
