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
									{!! Form::label('paid_until', Lang::get('admin/planchange.paid.until')) !!}
									{!! Form::text('paid_until', null, [ 'class'=>'form-control has-datetimepicker' ]) !!}
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
											<td>{{ @$old_plan->name }}</td>
											<td>{{ @$planchange->plan->name }}</td>
										</tr>
										<tr>
											<td>{{ Lang::get('admin/planchange.payment.interval') }}</td>
											<td>{{ @$planchange->old_data['payment_interval'] ? Lang::get("web/plans.price.{$planchange->old_data['payment_interval']}") : '' }}</td>
											<td>{{ @$planchange->new_data['payment_interval'] ? Lang::get("web/plans.price.{$planchange->new_data['payment_interval']}") : '' }}</td>
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

			form.find('.has-datetimepicker').datetimepicker({
				format: 'YYYY-MM-DD'
			});

		});
	</script>

@endsection