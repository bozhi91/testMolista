@extends('layouts.popup')

@section('content')

	<style type="text/css">
		#property-customers { padding: 20px; }
		#associate-form label.error { display: block; margin-top: 5px; }
	</style>

	<div id="property-customers">
		@if ( session('success') )
			<script type="text/javascript">
				ready_callbacks.push(function(){
					if ( typeof window.parent == 'object' ) {
						if ( typeof window.parent.reloadLeadsList == 'function' ) {
							window.parent.reloadLeadsList();
						}
						window.parent.$.magnificPopup.close();
					} 
				});
			</script>

		@else
			@include('common.messages', [ 'dismissible'=>true ])

			<h4 class="page-title">{{ Lang::get('account/customers.associate.h1') }}</h4>
				{!! Form::model(null, [ 'action'=>[ 'Account\CustomersController@postAddPropertyCustomer', $property->slug ], 'method'=>'POST', 'id'=>'associate-form', 'class'=>'form-inline' ]) !!}
					<div class="error-container">
						<div class="form-group">
							<?php
								$tmp = [ '' => '' ];
								foreach ($customers as $customer) 
								{
									$tmp[$customer->id] = "{$customer->full_name} ({$customer->email})";
								}
							?>
							{!! form::select('customer_id', $tmp, null, [ 'class'=>'form-control required has-select-2']) !!}
						</div>
						{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
					</div>
				{!! Form::close() !!}

			<hr />

			<h4 class="page-title">{{ Lang::get('account/customers.create.h1') }}</h4>

			@include('web.customers.form', [
				'item' => null,
				'action' => [ 'Account\CustomersController@postAddPropertyCustomer', $property->slug ],
				'method' => 'POST',
				'button' => Lang::get('general.save'),
				'filled_by_customer' => false,
			])

			<script type="text/javascript">
				ready_callbacks.push(function(){
					var form = $('#associate-form');

					form.validate({
						ignore: '',
						errorPlacement: function(error, element) {
							element.closest('.error-container').append(error);
						},
						submitHandler: function(f) {
							LOADING.show();
							f.submit();
						}
					});

					form.find('.has-select-2').select2();
					
				});
			</script>

		@endif

	</div>

@endsection