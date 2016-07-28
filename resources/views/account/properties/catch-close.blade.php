@extends('layouts.popup')

@section('content')

	<div style="padding: 20px;">

		<h2 class="page-title">{{ Lang::get('account/properties.show.property.catch.close') }}</h2>

		{!! Form::model(null, [ 'action'=>[ 'Account\PropertiesController@postCatchClose', $item->id ], 'method'=>'POST', 'id'=>'catch-form' ]) !!}
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('status', Lang::get('account/properties.show.property.catch.status') ) !!}
						{!! Form::select('status', [
							'' => '&nbsp;',
							'sold' => Lang::get('account/properties.show.property.catch.status.sold'),
							'rent' => Lang::get('account/properties.show.property.catch.status.rent'),
							'other' => Lang::get('account/properties.show.property.catch.status.other'),
						], null, [ 'class'=>'form-control required', ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('closer_id', Lang::get('account/properties.show.property.catch.close.responsible') ) !!}
						{!! Form::select('closer_id', [''=>'&nbsp;']+$managers, @$item->employee_id, [ 'class'=>'has-select-2 form-control required', ]) !!}
					</div>
				</div>
			</div>
			<div class="row hide">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('transaction_date', Lang::get('account/properties.show.property.catch.transaction.date') ) !!}
						{!! Form::text('transaction_date', date('Y-m-d'), [ 'class'=>'form-control required', ]) !!}
					</div>
				</div>
			</div>
			<div class="row hide status-rel status-rel-sold status-rel-rent">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('buyer_id', Lang::get('account/properties.show.transactions.buyer') ) !!}
						{!! Form::select('buyer_id', [''=>'&nbsp;']+$customers, null, [ 'class'=>'has-select-2 form-control required', ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('price_sold', Lang::get('account/properties.show.transactions.price') ) !!}
						<div class="input-group">
							@if ( @$item->property->infocurrency->position == 'before' )
								<div class="input-group-addon">{{ @$item->property->infocurrency->symbol }}</div>
							@endif
							{!! Form::text('price_sold', null, [ 'class'=>'form-control required number', 'min'=>1 ]) !!}
							@if ( @$item->property->infocurrency->position == 'after' )
								<div class="input-group-addon">{{ @$item->property->infocurrency->symbol }}</div>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="row hide status-rel status-rel-other">
				<div class="col-xs-12">
					<div class="form-group error-container">
						{!! Form::label('reason', Lang::get('account/properties.show.transactions.reason') ) !!}
						{!! Form::textarea('reason', null, [ 'class'=>'form-control required', 'rows'=>2 ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="text-right">
						{!! Form::button(Lang::get('general.cancel'), [ 'class'=>'btn btn-default btn-cancel-trigger' ]) !!}
						{!! Form::button(Lang::get('general.continue'), [ 'class'=>'btn btn-primary', 'type'=>'submit' ]) !!}
					</div>
				</div>
			</div>
		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#catch-form');

			form.on('click', '.btn-cancel-trigger', function(e){
				e.preventDefault();
				window.parent.$.magnificPopup.close();
			});

			form.on('change', 'select[name="status"]', function(){
				form.find('.status-rel').addClass('hide')
						.filter('.status-rel-' + $(this).val() ).removeClass('hide')
						.find('.has-select-2').select2();
			});

			form.find('.has-select-2').select2();

			form.find('input[name="transaction_date"]').datetimepicker({
				format: 'YYYY-MM-DD'
			});

			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: form.attr('action'),
						data: form.serialize(),
						success: function(data) {
							LOADING.hide();
							if ( data.success ) {
								alertify.success("{{ print_js_string( Lang::get('account/properties.show.property.catch.success') ) }}");
								window.parent.location.reload(true);
							} else {
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						},
						error: function() {
							LOADING.hide();
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					});
				}
			});

		});
	</script>

@endsection