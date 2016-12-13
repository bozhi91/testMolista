@extends('layouts.account', [
	'hide_pending_request_warning' => 1,
])

@section('account_content')

	<div id="retry-payment">

		<h1 class="page-title">{{ Lang::get('account/payment.retry.title') }}</h1>

		@if ( empty($last_invoice) )
			<div class="alert alert-info">
				{!! Lang::get('account/payment.retry.empty') !!}
			</div>
		@else
			@if ( $last_invoice->paid )
				<div class="alert alert-info">
					{!! Lang::get('account/payment.retry.paid') !!}
				</div>
			@elseif ( @$payment_response )
				@if ( @$payment_response->paid )
					<div class="alert alert-success">
						{!! Lang::get('account/payment.retry.success') !!}
					</div>
				@else
					<div class="alert alert-danger">
						{!! Lang::get('account/payment.retry.error') !!}
					</div>
				@endif
			@elseif ( @$response_error )
				<div class="alert alert-danger">
					{!! $response_error->message !!}
				</div>
			@else
				<div class="alert alert-danger">
					{!! trans('general.messages.error') !!}
				</div>
			@endif
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#retry-payment');
		});
	</script>

@endsection
