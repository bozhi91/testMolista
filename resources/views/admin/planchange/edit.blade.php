@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		{!! Form::model($planchange, [ 'method'=>'POST', 'action'=>[ 'Admin\PlanchangeController@postEdit', $planchange->id ], 'id'=>'planchange-form' ]) !!}
			{!! Form::hidden('accept', null) !!}

			<h1 class="list-title">{{ Lang::get('admin/planchange.edit.title') }}</h1>

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tab-request" aria-controls="tab-request" role="tab" data-toggle="tab">{{ Lang::get('admin/planchange.edit.request') }}</a></li>
				<li role="presentation" class=""><a href="#tab-history" aria-controls="tab-history" role="tab" data-toggle="tab">{{ Lang::get('admin/planchange.edit.history') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main active" id="tab-request">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('admin/planchange.site')) !!}
								{!! Form::text(null, $planchange->site->main_url, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
							</div>
							@if ( $planchange->new_data['payment_method'] == 'transfer' )
								<div class="form-group error-container">
									{!! Form::label('payment_amount', Lang::get('admin/planchange.paid.amount')) !!}
									<div class="input-group plan-input-group">
										{!! Form::text('payment_amount', null, [ 'class'=>'form-control required number', 'min'=>0 ]) !!}
										<div class="input-group-addon">{{ $planchange->plan->currency }}</div>
									</div>
								</div>
								<div class="form-group error-container">
									{!! Form::label('paid_from', Lang::get('admin/planchange.paid.from')) !!}
									<div style="position: relative;">
										{!! Form::text('paid_from', null, [ 'class'=>'datetimepicker-start form-control required' ]) !!}
									</div>
								</div>
								<div class="form-group error-container">
									{!! Form::label('paid_until', Lang::get('admin/planchange.paid.until')) !!}
									<div style="position: relative;">
										{!! Form::text('paid_until', null, [ 'class'=>'datetimepicker-end form-control required' ]) !!}
									</div>
								</div>
							@endif
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<div class="error-container">
									{!! Form::label('response', Lang::get('admin/planchange.reject.reason')) !!}
									{!! Form::textarea('response', null, [ 'class'=>'form-control', 'rows'=>4 ]) !!}
								</div>
								<div class="help-block">{!! Lang::get('admin/planchange.reject.reason.helper', [ 'language'=>$planchange->locale_name ]) !!}</div>
							</div>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th></th>
											<th>{{ Lang::get('admin/planchange.edit.data.current') }}</th>
											<th>{{ Lang::get('admin/planchange.edit.data.requested') }}</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>{{ Lang::get('admin/planchange.plan') }}</td>
											<td>
												{{ @$old_plan->name }} 
												@if ( empty($old_plan->is_free) )
													({{ @$old_plan->currency }})
												@endif
											</td>
											<td>
												{{ @$planchange->plan->name }} 
												@if ( empty($planchange->plan->is_free) )
													({{ @$planchange->plan->currency }})
												@endif
											</td>
										</tr>
										<tr>
											<td>{{ Lang::get('admin/planchange.payment.interval') }}</td>
											<td>
												@if ( @$old_plan && @$planchange->old_data['payment_interval'] )
													{{ Lang::get("web/plans.price.{$planchange->old_data['payment_interval']}") }}
													@if ( empty($old_plan->is_free) )
														@if ( $planchange->old_data['payment_interval'] == 'month' )
															({{ price($old_plan->price_month, $old_plan->infocurrency->toArray()) }})
														@else
															({{ price($old_plan->price_year, $old_plan->infocurrency->toArray()) }})
														@endif
													@endif
												@endif
											</td>
											<td>
												@if ( @$planchange->plan && @$planchange->new_data['payment_interval'] ) 
													{{ Lang::get("web/plans.price.{$planchange->new_data['payment_interval']}") }}
													@if ( empty($planchange->plan->is_free) )
														@if ( $planchange->new_data['payment_interval'] == 'month' )
															({{ price($planchange->plan->price_month, $planchange->plan->infocurrency->toArray()) }})
														@else
															({{ price($planchange->plan->price_year, $planchange->plan->infocurrency->toArray()) }})
														@endif
													@endif
												@endif
											</td>
										</tr>
										<tr>
											<td>{{ Lang::get('admin/planchange.payment.method') }}</td>
											<td>{{ @$planchange->old_data['payment_method'] ? Lang::get("account/payment.method.{$planchange->old_data['payment_method']}") : '' }}</td>
											<td>{{ @$planchange->new_data['payment_method'] ? Lang::get("account/payment.method.{$planchange->new_data['payment_method']}") : '' }}</td>
										</tr>
										<tr>
											<td>{{ Lang::get('corporate/signup.payment.iban') }}</td>
											<td>{{ @$planchange->old_data['iban_account']  }}</td>
											<td>{{ @$planchange->new_data['iban_account'] }}</td>
										</tr>
										@if ( empty($old_plan->is_free) && $planchange->site->paid_until )
											<tr>
												<td>{{ Lang::get('admin/expirations.paid.until') }}</td>
												<td>{{ date("d/m/Y", strtotime($planchange->site->paid_until)) }}</td>
												<td></td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="text-right">
						{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
						{!! Form::button( Lang::get('admin/planchange.button.reject'), [ 'class'=>'btn btn-danger btn-option', 'data-accept'=>0 ]) !!}
						{!! Form::button( Lang::get('admin/planchange.button.accept'), [ 'class'=>'btn btn-success btn-option', 'data-accept'=>1 ]) !!}
					</div>

				</div>
				
				<div role="tabpanel" class="tab-pane tab-main" id="tab-history">
					@if ( $history->count() < 1 )
						{{ Lang::get('admin/planchange.edit.history.empty') }}
					@else
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>{{ Lang::get('admin/planchange.created') }}</th>
										<th>{{ Lang::get('admin/planchange.plan') }}</th>
										<th>{{ Lang::get('admin/planchange.payment.interval') }}</th>
										<th>{{ Lang::get('admin/planchange.payment.method') }}</th>
										<th>{{ Lang::get('admin/planchange.status') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($history as $item)
										<tr>
											<td>{{ $item->created_at->format("d/m/Y") }}</td>
											<td>{{ $item->plan->name }}</td>
											<td>{{Lang::get("web/plans.price.{$item->payment_interval}") }}</td>
											<td>{{ Lang::get("account/payment.method.{$item->payment_method}") }}</td>
											<td class="text-capitalize">{{ $item->status }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>

			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#planchange-form');

			form.on('click', '.btn-option', function(e){
				e.preventDefault();
				form.find('input[name="accept"]').val( $(this).data().accept );
				form.submit();
			});

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					paid_until: {
						required: function() {
							return form.find('input[name="accept"]').val() == 1;
						}
					},
					response: {
						required: function() {
							return form.find('input[name="accept"]').val() == 0;
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.find('.datetimepicker-start').datetimepicker({
				format: 'YYYY-MM-DD'
			}).on("dp.change", function (e) {
				form.find('.datetimepicker-end').data("DateTimePicker").minDate(e.date);
			});
			form.find('.datetimepicker-end').datetimepicker({
				format: 'YYYY-MM-DD',
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