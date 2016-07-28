@if ( empty($hide_pending_request_warning) && App\Session\Site::get('pending_request') )
	@if ( App\Session\Site::get('pending_request.payment_method') == 'stripe' )
		<div class="alert alert-danger" id="account-pending-request-alert">
			<div class="container">
				<h4>{{ Lang::get('account/warning.pending.request.h1') }}</h4>
				{!! Lang::get('account/warning.pending.request.intro', [
					'plan' => App\Session\Site::get('pending_request.plan.name')
				]) !!}
				{!! Form::open([ 'method'=>'POST', 'action'=>'Account\PaymentController@postCancel', 'class'=>'button-area' ]) !!}
					{!! Form::button(Lang::get('account/warning.pending.request.cancel'), [ 'type'=>'submit', 'class'=>'btn btn-warning' ]) !!}
					<a href="{{ action('Account\PaymentController@getPay') }}" class="btn btn-success">{{ Lang::get('account/warning.pending.request.pay') }}</a>
				{!! Form::close() !!}
			</div>
		</div>
	@endif
@endif