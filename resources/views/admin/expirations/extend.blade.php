@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/menu.expirations') }}</h1>

		{!! Form::model(null, [ 'method'=>'post', 'action'=>[ 'Admin\ExpirationsController@postExtend', $site->id ], 'id'=>'extend-form' ]) !!}

			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div><strong>{{ $site->title }}</strong></div>
					<div>{{ $site->main_url}}</div>
					<br />
					<div>{{ Lang::get('admin/expirations.plan') }}: {{ $site->plan->name }} ({{ $site->plan->currency }})</div>
					<div>
						{{ Lang::get("web/plans.price.{$site->payment_interval}") }}:
						@if ( $site->payment_interval == 'month' )
							{{ price($site->plan->price_month, $site->plan->infocurrency->toArray()) }}
						@else
							{{ price($site->plan->price_year, $site->plan->infocurrency->toArray()) }}
						@endif
					</div>
					<div>{{ Lang::get('admin/expirations.paid.until') }}: {{ date("d/m/Y", strtotime($site->paid_until)) }}</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('payment_amount', Lang::get('admin/expirations.extend.amount')) !!}
						<div class="input-group plan-input-group">
							{!! Form::text('payment_amount', null, [ 'class'=>'form-control required number', 'min'=>0 ]) !!}
							<div class="input-group-addon">{{ $site->plan->currency }}</div>
						</div>
					</div>
					<div class="form-group error-container">
						{!! Form::label('paid_from', Lang::get('admin/expirations.extend.paid.from')) !!}
						<div style="position: relative;">
							{!! Form::text('paid_from', null, [ 'class'=>'datetimepicker-start form-control required' ]) !!}
						</div>
					</div>
					<div class="form-group error-container">
						{!! Form::label('paid_until', Lang::get('admin/expirations.extend.paid.until')) !!}
						<div style="position: relative;">
							{!! Form::text('paid_until', null, [ 'class'=>'datetimepicker-end form-control required' ]) !!}
						</div>
					</div>
				</div>
			</div>

			<div class="text-right">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#extend-form');

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

			form.find('.datetimepicker-start').datetimepicker({
				format: 'DD/MM/YYYY'
			}).on("dp.change", function (e) {
				form.find('.datetimepicker-end').data("DateTimePicker").minDate(e.date);
			});
			form.find('.datetimepicker-end').datetimepicker({
				format: 'DD/MM/YYYY',
				useCurrent: false //Important! See issue #1075
			}).on("dp.change", function (e) {
				form.find('.datetimepicker-start').data("DateTimePicker").maxDate(e.date);
			});
			if ( form.find('.datetimepicker-start').val() ) {
				form.find('.datetimepicker-start').trigger('dp.change');
			}
			if ( form.find('.datetimepicker-end').val() ) {
				form.find('.datetimepicker-end').trigger('dp.change');
			}

		});
	</script>

@endsection
