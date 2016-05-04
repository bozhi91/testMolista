@extends('layouts.web')

@section('content')

	<div id="login-register">
		<div class="container">

			<div class="row">
				<div class="col-xs-12 col-md-10 col-md-offset-1">

					@include('common.messages', [ 'dismissible'=>true ])

					<h2>{{ Lang::get('web/customers.register.title') }}</h2>

					@include('web.customers.form', [
						'item' => null,
						'action' => 'Web\CustomersController@postRegister',
						'method' => 'POST',
						'button' => Lang::get('web/customers.register.button'),
						'filled_by_customer' => true,
					])

				</div>
			</div>

		</div>
	</div>

@endsection
