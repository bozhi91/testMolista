<?php
	$current_tab = session('current_tab', 'general');
?>

<style type="text/css">
	.plan-input-group { max-width: 200px; }
</style>

{!! Form::hidden('current_tab', $current_tab) !!}
{!! Form::password(null, [ 'class'=>'prevent-chrome-password-autofill', 'style'=>'opacity: 0; height: 1px; width: 1px;', ]) !!}

<ul class="nav nav-tabs main-tabs" role="tablist">
	<li role="presentation" class="{{ $current_tab == 'general' ? 'active' : '' }}"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab" data-tab="general">{{ Lang::get('admin/resellers.tab.general') }}</a></li>
	<li role="presentation" class="{{ $current_tab == 'commissions' ? 'active' : '' }}"><a href="#tab-commissions" aria-controls="tab-commissions" role="tab" data-toggle="tab" data-tab="commissions">{{ Lang::get('admin/resellers.tab.commissions') }}</a></li>
</ul>

<div class="tab-content">

	<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'general' ? 'active' : '' }}" id="tab-general">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('ref', Lang::get('admin/resellers.ref')) !!}
					{!! Form::text('ref', null, [ 'class'=>'form-control required alphanumeric', 'minlength'=>6, 'maxlength'=>20, ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('name', Lang::get('admin/resellers.name')) !!}
					{!! Form::text('name', null, [ 'class'=>'form-control required', ]) !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('email', Lang::get('admin/resellers.email')) !!}
					{!! Form::email('email', null, [ 'class'=>'form-control required email' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<div class="error-container">
						{!! Form::label('password', Lang::get('admin/resellers.password')) !!}
						<div class="input-group">
							@if ( @$reseller )
								{!! Form::password('password', [ 'class'=>'form-control', 'minlength'=>6, 'maxlength'=>20, ]) !!}
							@else
								{!! Form::password('password', [ 'class'=>'form-control required', 'minlength'=>6, 'maxlength'=>20, ]) !!}
							@endif
							<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password" style="cursor: pointer;" aria-hidden="true"></span></div>
						</div>
						@if ( @$reseller )
							<div class="help-block">{{ Lang::get('admin/resellers.password.helper') }}</div>
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('type', Lang::get('admin/resellers.type')) !!}
					{!! Form::select('type', [
						'individual' => Lang::get('admin/resellers.type.individual'),
						'company' => Lang::get('admin/resellers.type.company'),
					], null, [ 'class'=>'form-control required' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('locale', Lang::get('admin/resellers.locale')) !!}
					{!! Form::select('locale', [ ''=>'' ]+$locales, null, [ 'class'=>'form-control required' ]) !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('enabled', Lang::get('admin/resellers.enabled')) !!}
					{!! Form::select('enabled', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
			</div>
		</div>
	</div>

	<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'commissions' ? 'active' : '' }}" id="tab-commissions">
		<table class="table">
			<thead>
				<tr>
					<th>Plan</th>
					<th>{{ Lang::get('admin/resellers.commission.variable') }}</th>
					<th>{{ Lang::get('admin/resellers.commission.fixed') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($plans as $plan)
					<tr>
						<td>{{ $plan->name }} ({{ $plan->infocurrency->title }})</td>
						<td class="error-container">
							<div class="input-group plan-input-group">
								{!! Form::number("plans_commissions[{$plan->id}][commission_percentage]", null, [ 'class'=>'form-control number', 'min'=>'0', 'max'=>100, ]) !!}
								<div class="input-group-addon">%</div>
							</div>
						</td>
						<td class="error-container">
							<div class="input-group plan-input-group">
								{!! Form::number("plans_commissions[{$plan->id}][commission_fixed]", null, [ 'class'=>'form-control number', 'min'=>'0', ]) !!}
								<div class="input-group-addon">{{ $plan->currency }}</div>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#reseller-form');

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			rules: {
				ref: {
					remote: {
						url: '{{ action('Admin\ResellersController@getValidate', 'ref') }}',
						type: 'get',
						data: {
							exclude: {{ @intval($reseller->id) }}
						}
					}
				},
				email: {
					remote: {
						url: '{{ action('Admin\ResellersController@getValidate', 'email') }}',
						type: 'get',
						data: {
							exclude: {{ @intval($reseller->id) }}
						}
					}
				}
			},
			messages: {
				ref: {
					remote: "{{ print_js_string( Lang::get('admin/resellers.ref.used') ) }}"
				},
				email: {
					remote: "{{ print_js_string( Lang::get('admin/resellers.email.used') ) }}"
				}
			},
			submitHandler: function(f) {
				f.submit();
			}
		});

		form.find('.main-tabs > li > a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			form.find('input[name="current_tab"]').val( $(this).data().tab );
		});

		form.on('click', '.show-hide-password', function(e){
			e.preventDefault();
			form.find('input[name="password"]').togglePassword();
		});

	});
</script>
