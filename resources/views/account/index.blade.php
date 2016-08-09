@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#account-container .account-block:first-child { margin-top: 20px; }
		#account-container .account-block:last-child { margin-bottom: 20px; }
		#account-container .account-block .plan-item { font-size: 18px; font-weight: bold; padding-top: 8px; }
		#account-container .account-block .list-inline { margin-right: -5px; }
		#account-container .pay-now-area { max-width: 350px; }
			#account-container .pay-now-area ul { margin-bottom: 10px; }
		#tab-invoices .pagination-limit-select { display: none; }
		#tab-invoices .pagination-limit li { line-height: 34px; }
	</style>

	<div id="user-profile">

		@if (session('status'))
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
				{{ session('status') }}
			</div>
		@else
			@include('common.messages', [ 'dismissible'=>true ])
		@endif

		<h1 class="page-title">{{ Lang::get('account/menu.data') }}</h1>

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="{{ $current_tab == 'data' ? 'active' : '' }}"><a href="#tab-data" aria-controls="tab-data" role="tab" data-toggle="tab">{{ Lang::get('account/payment.data') }}</a></li>
				@role('company')
					<li role="presentation" class="{{ $current_tab == 'plans' ? 'active' : '' }} hidden-xs"><a href="#tab-plans" aria-controls="tab-plans" role="tab" data-toggle="tab">{{ Lang::get('account/payment.plans') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'invoices' ? 'active' : '' }}"><a href="#tab-invoices" aria-controls="tab-invoices" role="tab" data-toggle="tab">{{ Lang::get('account/payment.invoices') }}</a></li>
				@endrole
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'data' ? 'active' : '' }}" id="tab-data">
					{!! Form::model(Auth::user(), [ 'method'=>'POST', 'files'=>true, 'action'=>'AccountController@updateProfile', 'id'=>'user-profile-form' ]) !!}

						@include('account.user-form', [
							'user_image' => empty(Auth::user()->image) ? false : Auth::user()->image_directory . '/' . Auth::user()->image,
							'user_email' => Auth::user()->email,
						])

						<br />

						<div class="text-right">
							{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
						</div>

					{!! Form::close() !!}
				</div>

				@role('company')
					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'plans' ? 'active' : '' }}" id="tab-plans">
						<div class="account-block">
							<div class="row">
								<div class="col-xs-12">
									<h3>{{ Lang::get('account/payment.plan.h1') }}</h3>
									<div>
										<ul class="list-inline">
											<li>
												<div class="plan-item">{{ \App\Session\Site::get('plan.name') }}</div>
												<div class="help-block">
													@if ( @$current_plan )
														{{ Lang::get('account/payment.plan.price') }}: <span class="text-lowercase">{{ Lang::get("web/plans.price.{$current_plan->payment_interval}") }} {{ price($current_plan->plan_price, [ 'decimals'=>0 ]) }}</span><br />
														@if ( $current_plan->payment_method == 'stripe ')
															{{ Lang::get('account/payment.plan.valid.from') }}: {{ $current_site->subscription('main')->updated_at->format("d/m/Y") }} <br />
														@else
															{{ Lang::get('account/payment.plan.valid.from') }}: {{ $current_plan->updated_at->format("d/m/Y") }} <br />
														@endif
														@if ( \App\Session\Site::get('plan.paid_until') )
															{{ Lang::get('account/payment.plan.next.charge') }}: {{ date("d/m/Y", strtotime(\App\Session\Site::get('plan.paid_until'))) }} <br />
														@endif
													@endif
												</div>
											</li>
											@if ( $plan_options < 1 )
											@elseif ( empty($pending_request) )
												<li class="pull-right"><a href="{{ action('Account\PaymentController@getUpgrade') }}" class="btn btn-primary">{{ Lang::get('account/payment.plan.upgrade') }}</a></li>
												<li class="pull-right"><a href="#plans-modal" class="btn btn-link" id="plans-modal-trigger">{{ Lang::get('account/payment.plan.show') }}</a></li>
											@else
												<li class="pull-right pay-now-area">
													@if ( $pending_request->payment_method == 'stripe' )
														{!! Lang::get('account/payment.plans.pending.stripe', [
															'plan' => @$pending_request->summary->plan_name,
															'paymethod' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, $pending_request->plan->infocurrency->toArray()),
														]) !!}
														<div class="text-right text-nowrap">
															<a href="#" class="btn btn-default btn-sm pull-left cancel-pending-request-trigger">{{ Lang::get('account/payment.plans.pending.cancel') }}</a>
															<a href="{{ action('Account\PaymentController@getPay') }}" class="btn btn-primary">{{ Lang::get('account/payment.plans.pending.button') }}</a>
														</div>
													@else
														{!! Lang::get('account/payment.plans.pending.transfer', [
															'plan' => @$pending_request->summary->plan_name,
															'paymethod' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, $pending_request->plan->infocurrency->toArray()),
														]) !!}
														<div class="text-nowrap">
															<a href="#" class="btn btn-default btn-sm cancel-pending-request-trigger">{{ Lang::get('account/payment.plans.pending.cancel') }}</a>
														</div>
													@endif
												</li>
											@endif
										</ul>
									</div>
								</div>
							</div>
						</div>

						@if ( \App\Session\Site::get('plan.payment_method') )
							<div class="account-block">
								<div class="row">
									<div class="col-xs-12">
										<h3>{{ Lang::get('account/payment.method.h1') }}</h3>
										<ul class="list-inline">
											@if ( \App\Session\Site::get('plan.payment_method') == 'stripe' )
												<li>
													<div class="plan-item">{{ Lang::get('account/payment.method.stripe') }}</div>
													<div class="help-block"><span class="text-uppercase">{{ \App\Session\Site::get('plan.card_brand') }}</span> ************{{ \App\Session\Site::get('plan.card_last_four') }}</div>
												</li>
											@elseif ( \App\Session\Site::get('plan.payment_method') == 'transfer' )
												<li>
													<div class="plan-item">{{ Lang::get('account/payment.method.transfer') }}</div>
													<div class="help-block">{{ \App\Session\Site::get('plan.iban_account') }}</div>
												</li>
											@endif
										</ul>
									</div>
								</div>
							</div>
						@endif
					</div>
			
					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'invoices' ? 'active' : '' }}" id="tab-invoices">
						<div class="text-center">
							<img src="/images/loading.gif" alt="" />
						</div>
					</div>

				@endrole

			</div>


		</div>

		<div id="plans-modal" class="mfp-hide app-popup-block-white app-popup-block-large">
			<div style="padding: 30px;">
				@include('corporate.common.plans', [
					'buy_plans' => $plans,
					'buy_plan_url' => action('Account\PaymentController@getUpgrade'),
					'buy_button_text' => Lang::get('account/payment.plan.upgrade.simple'),
					'hide_plan_extras' => true,
				])
			</div>
		</div>

		{!! Form::open([ 'method'=>'POST', 'action'=>'Account\PaymentController@postCancel', 'id'=>'delete-request-form' ]) !!}
		{!! Form::close() !!}
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#user-profile');
			var form = $('#user-profile-form');

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

			$('#plans-modal-trigger').magnificPopup({
				type: 'inline',
				showCloseBtn: false
			});

			var tab_invoices = $('#tab-invoices');
			if ( tab_invoices.length ) {
				tab_invoices.load('{{ action("Account\InvoicesController@getIndex") }}');
				tab_invoices.on('click','ul.pagination a', function(e){
					e.preventDefault();
					LOADING.show();
					tab_invoices.load($(this).attr('href'), function(){
						LOADING.hide();
					});
				});
			}

			cont.on('click', '.cancel-pending-request-trigger', function(e){
				e.preventDefault();
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/payment.plans.cancel.warning') ) }}", function (e) {
					if (e) {
						LOADING.show();
						$('#delete-request-form').submit();
					}
				});
			});

		});
	</script>
@endsection
