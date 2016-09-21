@extends('layouts.corporate')

@section('content')

	<div id="signup-new">

		{!! Form::model(@$data, [ 'action'=>'Corporate\SignupController@postIndex', 'method'=>'post', 'id'=>'signup-form', ]) !!}
			<input type="hidden" name="user[type]" value="{{ old('user.type','new') }}" class="user-type-input" />

			<div class="container">
				<h1>{{ Lang::get('corporate/signup.full.h1') }}</h1>
				<div class="intro">{!! Lang::get('corporate/signup.full.intro') !!}</div>

				@include('common.messages', [ 'dismissible'=>true ])

				<div class="row">
					<div class="col-xs-12 col-sm-8">

						<div class="form-column">

							<div class="signup-block ignore-if-hidden">
								<div class="signup-block-title">
									<span>1</span>
									<h2>{{ Lang::get('corporate/signup.full.data.title') }}</h2>
								</div>
								<div class="signup-block-content">
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="user-tab user-tab-new {{ old('user.type','new') == 'new' ? '' : 'hide' }}">
												<div class="form-group error-container">
													{!! Form::label('user[new][name]', Lang::get('corporate/signup.user.new.name'), [ 'class'=>'' ]) !!}
													{!! Form::text('user[new][name]', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('corporate/signup.user.new.name.placeholder') ]) !!}
												</div>
												<div class="form-group error-container">
													{!! Form::label('user[new][email]', Lang::get('corporate/signup.user.new.email'), [ 'class'=>'' ]) !!}
													{!! Form::text('user[new][email]', null, [ 'class'=>'new-user-email-input form-control required email', 'placeholder'=>Lang::get('corporate/signup.user.new.email.placeholder') ]) !!}
												</div>
												<div class="form-group error-container">
													{!! Form::label('user[new][password]', Lang::get('corporate/signup.user.new.password'), [ 'class'=>'' ]) !!}
													{!! Form::password('user[new][password]', [ 'class'=>'form-control required', 'minlength'=>6, 'maxlength'=>20, 'placeholder'=>Lang::get('corporate/signup.user.new.password.placeholder') ]) !!}
												</div>
												<div class="form-group error-container">
													{!! Form::label('user[new][phone]', Lang::get('corporate/signup.user.new.phone'), [ 'class'=>'' ]) !!}
													{!! Form::text('user[new][phone]', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('corporate/signup.user.new.phone.placeholder') ]) !!}
												</div>
												<div class="switch-area">
													<a href="#" class="user-type-switch" data-rel="old"><strong>{{ Lang::get('corporate/signup.user.new.have.account') }}</strong></a>
												</div>
											</div>
											<div class="user-tab user-tab-old {{ old('user.type') == 'old' ? '' : 'hide' }}">
												<div class="form-group error-container">
													{!! Form::label('user[old][email]', Lang::get('corporate/signup.user.new.email'), [ 'class'=>'' ]) !!}
													{!! Form::text('user[old][email]', null, [ 'class'=>'form-control required email', 'placeholder'=>Lang::get('corporate/signup.user.new.email.placeholder') ]) !!}
												</div>
												<div class="form-group error-container">
													{!! Form::label('user[old][password]', Lang::get('corporate/signup.user.new.password'), [ 'class'=>'' ]) !!}
													{!! Form::password('user[old][password]', [ 'class'=>'form-control required', 'placeholder'=>Lang::get('corporate/signup.user.new.password.placeholder') ]) !!}
												</div>
												<div class="switch-area">
													<div class="error-container">
														{{ Lang::get('corporate/signup.user.old.no.account') }}
														<a href="#" class="user-type-switch" data-rel="new"><strong>{{ Lang::get('corporate/signup.user.old.create.account') }}</strong></a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="signup-block">
								<div class="signup-block-title">
									<span>2</span>
									<h2>{{ Lang::get('corporate/signup.full.plan.title') }}</h2>
								</div>
								<div class="signup-block-content">
									<div class="error-container">
										<div class="row">
											@foreach ($plans as $plan)
												<div class="col-xs-12 col-sm-4">
													<div class="plan-block plan-block-level-{{ $plan->level }} {{ old('pack') == $plan->code ? 'selected' : '' }}">
														<div class="plan-block-title">
															{!! Form::radio('pack', $plan->code, null, [ 'class'=>'plan-input required hidden' ]) !!}
															{{ $plan->name }}
														</div>
														<div class="plan-block-options">
															<select name="payment_interval[{{ $plan->code }}]" class="payment-interval-select form-control">
																@if ( $plan->is_free )
																	<option value="year" data-text="{{ $plan->name }}" data-price="0" data-transfer="{{ floatval($plan->extras['transfer']) }}" data-level="{{ $plan->level }}">{{ Lang::get('web/plans.free') }}</option>
																@else
																	<option value="year" data-text="{{ $plan->name }} {{ Lang::get('web/plans.price.year') }}" data-price="{{ $plan->price_year }}" data-transfer="{{ floatval($plan->extras['transfer']) }}" data-level="{{ $plan->level }}" {{ @$data['payment_interval'][$plan->code] == 'year' ? 'selected="selected"' : '' }}>{{ Lang::get('web/plans.price.year') }} {{ price($plan->price_year, $plan->infocurrency->toArray()) }}</option>
																	<option value="month" data-text="{{ $plan->name }} {{ Lang::get('web/plans.price.month') }}"  data-price="{{ $plan->price_month }}" data-transfer="{{ floatval($plan->extras['transfer']) }}" data-level="{{ $plan->level }}"  {{ @$data['payment_interval'][$plan->code] == 'month' ? 'selected="selected"' : '' }}>{{ Lang::get('web/plans.price.month') }} {{ price($plan->price_month, $plan->infocurrency->toArray()) }}</option>
																@endif
															</select>
														</div>
													</div>
												</div>
											@endforeach
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<br />
											<div class="form-group error-container">
												{!! Form::label('reseller_code', Lang::get('corporate/signup.reseller.code'), [ 'class'=>'' ]) !!}
												{!! Form::text('reseller_code', null, [ 'class'=>'form-control' ]) !!}
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="signup-block ignore-if-hidden">
								<div class="signup-block-title">
									<span>3</span>
									<h2>{{ Lang::get('corporate/signup.site.subdomain') }}</h2>
								</div>
								<div class="signup-block-content">
									<div class="row">
										<div class="col-xs-12 col-sm-9">
											<div class="form-group error-container">
												<div class="input-group">
													<div class="input-group-addon domain-input-group-addon">{{env('APP_PROTOCOL','http')}}://</div>
													{!! Form::text('subdomain', null, [ 'class'=>'form-control required alphanumericHypen' ]) !!}
													<div class="input-group-addon domain-input-group-addon">.{{env('APP_DOMAIN','molista.com')}}</div>
												</div>
											</div>
											<div class="custom-domain-warning">{{ Lang::get('corporate/signup.full.site.warning') }}</div>
											<div class="form-group error-container language-block">
												{!! Form::label('language', Lang::get('corporate/signup.site.language'), [ 'class'=>'' ]) !!}
												{!! Form::select('language', $languages, null, [ 'class'=>'form-control' ]) !!}
											</div>
											<div class="error-container">
												<div class="checkbox ">
													<div class="form-inline transfer-list">
														<div class="form-group optional-text">{{ Lang::get('corporate/signup.full.site.optional') }}:</div>
														<div class="form-group" style="position: relative;">
															<label>
																{!! Form::checkbox('web_transfer_requested', 1, null, [ 'class'=>'transfer-checkbox' ]) !!}
																{{ Lang::get('corporate/signup.full.site.transfer') }}
																	<span class="plan-rel plan-rel-level-0 hide"></span>
																	<span class="plan-rel plan-rel-level-1 hide"></span>
																	<span class="plan-rel plan-rel-level-2 hide"></span>
																	@foreach ($plans as $plan)
																		<span class="plan-rel plan-rel-level-{{ $plan->level }} hide">
																			@if ( @floatval($plan->extras['transfer']) > 0 )
																				@if ( env('PLANS_PROMOTION',0) )
																					<span class="price linethrough">({{ price($plan->extras['transfer'], App\Session\Currency::all()) }})</span>
																					<span class="text-uppercase">{{ Lang::get('web/plans.free') }}*</span>
																				@else
																					<span class="price">({{ price($plan->extras['transfer'], App\Session\Currency::all()) }}*)</span>
																				@endif
																			@else
																				<span class="price text-uppercase">({{ Lang::get('web/plans.included') }}*)</span>
																			@endif
																		</span>
																	@endforeach
															</label>
															<div class="plan-footnote">
																<span class="plan-rel plan-rel-level-0 hide">* {{ Lang::get("web/plans.footnote.text0")  }}</span>
																<span class="plan-rel plan-rel-level-1 hide">* {{ Lang::get("web/plans.footnote.text1")  }}</span>
																<span class="plan-rel plan-rel-level-2 hide">* {{ Lang::get("web/plans.footnote.text1")  }}</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="signup-block ignore-if-hidden">
								<div class="signup-block-title">
									<span>4</span>
									<h2>{{ Lang::get('corporate/signup.full.invoicing.title') }}</h2>
								</div>
								<div class="signup-block-content is-last">
									<div class="row">
										<div class="col-xs-12 col-sm-4">
											<div class="form-group error-container">
												{!! Form::label('invoicing[type]', Lang::get('corporate/signup.full.invoicing.please')) !!}
												{!! Form::select('invoicing[type]', [
													'individual' => Lang::get('corporate/signup.invoicing.type.individual'),
													'company' => Lang::get('corporate/signup.invoicing.type.company'),
												], null, [ 'class'=>'invoicing-type-select form-control required' ]) !!}
											</div>
										</div>
										<div class="col-xs-12 col-sm-8">
											<div class="invoicing-type-rel invoicing-type-company {{ old('invoicing.type','individual') == 'company' ? '' : 'hide' }}">
												<div class="form-group error-container">
													{!! Form::label('invoicing[company]', Lang::get('corporate/signup.invoicing.company'), [ 'class'=>'' ]) !!}
													{!! Form::text('invoicing[company]', null, [ 'class'=>'form-control required' ]) !!}
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-4">
											<div class="form-group error-container">
												{!! Form::label('invoicing[first_name]', Lang::get('corporate/signup.invoicing.first_name'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[first_name]', null, [ 'class'=>'form-control required' ]) !!}
											</div>
										</div>
										<div class="col-xs-12 col-sm-8">
											<div class="form-group error-container">
												{!! Form::label('invoicing[last_name]', Lang::get('corporate/signup.invoicing.last_name'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[last_name]', null, [ 'class'=>'form-control required' ]) !!}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-4">
											<div class="form-group error-container">
												{!! Form::label('invoicing[tax_id]', Lang::get('corporate/signup.invoicing.tax_id'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[tax_id]', null, [ 'class'=>'form-control' ]) !!}
											</div>
										</div>
										<div class="col-xs-12 col-sm-8">
											<div class="form-group error-container">
												{!! Form::label('invoicing[email]', Lang::get('corporate/signup.invoicing.email'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[email]', null, [ 'class'=>'form-control required email' ]) !!}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-8">
											<div class="form-group error-container">
												{!! Form::label('invoicing[street]', Lang::get('corporate/signup.invoicing.street'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[street]', null, [ 'class'=>'form-control required' ]) !!}
											</div>
										</div>
										<div class="col-xs-12 col-sm-4">
											<div class="form-group error-container">
												{!! Form::label('invoicing[zipcode]', Lang::get('corporate/signup.invoicing.zipcode'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[zipcode]', null, [ 'class'=>'form-control required' ]) !!}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="form-group error-container">
												{!! Form::label('invoicing[city]', Lang::get('corporate/signup.invoicing.city'), [ 'class'=>'' ]) !!}
												{!! Form::text('invoicing[city]', null, [ 'class'=>'form-control required' ]) !!}
											</div>
										</div>
										<div class="col-xs-12 col-sm-6">
											<div class="form-group error-container">
												{!! Form::label('invoicing[country_id]', Lang::get('corporate/signup.invoicing.country'), [ 'class'=>'' ]) !!}
												{!! Form::select('invoicing[country_id]', [ ''=>'' ]+$countries, null, [ 'class'=>'form-control required' ]) !!}
											</div>
										</div>
									</div>
								</div>
								<div class="paymethod-area hide">
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="form-group error-container">
												{!! Form::label('payment_method', Lang::get('corporate/signup.payment.choose'), [ 'class'=>'' ]) !!}
												{!! Form::select('payment_method', $paymethods, null, [ 'class'=>'payment-method-select form-control required' ]) !!}
											</div>
											<div class="form-group error-container payment-method-rel payment-method-rel-transfer {{ old('payment_method') == 'transfer' ? '' : 'hide' }}">
												{!! Form::label('iban_account', Lang::get('corporate/signup.payment.iban'), [ 'class'=>'' ]) !!}
												{!! Form::text('iban_account', null, [ 'class'=>'form-control iban required' ]) !!}
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="hidden-xs">
								<div class="row">
									<div class="col-xs-12 col-sm-6 col-sm-offset-6">
										<br />
										{!! Form::button(Lang::get('corporate/signup.full.summary.button'), [ 'type'=>'submit', 'class'=>'btn btn-block btn-submit' ]) !!}
									</div>
								</div>
							</div>

						</div>

						<div class="visible-xs" style="height: 50px;"></div>
					</div>
					<div class="col-xs-12 col-sm-4">

						<div class="summary-column">
							<div class="summary-sticky">
								<div class="summary">
									<h2>{{ Lang::get('corporate/signup.full.summary.title') }}</h2>
									<div class="summary-block items-block hide">
										<table>
											<tbody>
												<tr class="plan-row hide">
													<td class="plan-text"></td>
													<td class="plan-price text-right text-nowrap"></td>
												</tr>
												<tr class="transfer-row hide">
													<td class="transfer-text"></td>
													<td class="transfer-price text-right text-nowrap"></td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="summary-block total-block">
										<table>
											<tbody>
												<tr class="total-row">
													<td class="total-text">{{ Lang::get('corporate/signup.full.summary.total') }}</td>
													<td class="total-price text-right text-nowrap"></td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="button-block">
										{!! Form::button(Lang::get('corporate/signup.full.summary.button'), [ 'type'=>'submit', 'class'=>'btn btn-block btn-submit' ]) !!}
									</div>
									<div class="accept-block">
										<div class="checkbox error-container">
											<label>
												{!! Form::checkbox('accept', 1, null, [ 'class'=>'required' ]) !!}
												<a href="{{ action('Corporate\InfoController@getLegal') }}" target="_blank">{{ Lang::get('corporate/signup.user.new.accept') }}</a>
											</label>
										</div>
									</div>
								</div>
								<div class="help">
									<div class="title">{{ Lang::get('corporate/signup.full.help.title') }}</div>
									<div class="phone-email phone">{{ Config::get('app.phone_support') }}</div>
									<div class="phone-email email"><a href="mailto:{{ Lang::get('corporate/signup.full.help.email') }}" target="_blank">{{ Lang::get('corporate/signup.full.help.email') }}</a></div>
									<div class="time">{{ Lang::get('corporate/signup.full.help.time') }}</div>
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>

		{!! Form::close() !!}

	</div>

	<section class="block-links">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 text-center">
					<ul>
						<li>
							<a href="{{ action('Corporate\DemoController@getIndex') }}" class="btn btnBdrYlw text-uppercase">
								{{ Lang::get('corporate/general.demo') }}
							</a>
							<div class="visible-xs" style="height: 10px;"></div>
						</li>
						<li>
							<a href="{{ action('Corporate\FeaturesController@getIndex') }}" class="btn btnBdrYlw text-uppercase">
								{{ Lang::get('corporate/general.moreinfo') }}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#signup-form');

			form.validate({
				ignore: '.ignore-if-hidden :hidden',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				invalidHandler: function(e, validator){
					$(window).trigger('resize');
				},
				rules: {
					"user[new][email]": {
						remote: {
							url: '{{ action('Corporate\SignupController@getValidate', 'email') }}',
							type: 'get',
							data: {
								email: function(){
									return form.find('.new-user-email-input').val();
								}
							}
						}
					},
					"subdomain": {
						remote: {
							url: '{{action('Ajax\SiteController@getValidate','subdomain')}}',
							data: { 
								subdomain: function() {
									return form.find('input[name="subdomain"]').val();
								}
							}
						}
					}
				},
				messages: {
					"user[new][email]": {
						remote: "{{ print_js_string( Lang::get('corporate/signup.user.new.email.error') ) }}"
					},
					"subdomain": {
						remote: "{{ print_js_string( Lang::get('admin/sites.subdomain.error.taken') ) }}",
						alphanumericHypen: "{{ print_js_string( Lang::get('validation.alphanumericHypen') ) }}"
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			// User type switch
			form.on('click','.user-type-switch',function(e){
				e.preventDefault();

				var t = $(this).data().rel;

				form.find('.user-type-input').val(t);
				form.find('.user-tab').addClass('hide').filter('.user-tab-' + t).removeClass('hide');
			});

			form.on('click change','.payment-interval-select',function(e){
				form.find('.plan-block').removeClass('selected');
				$(this).closest('.plan-block').addClass('selected').find('.plan-input').prop("checked", true).valid();
				updateSummary();
			});

			form.on('change','.invoicing-type-select',function(e){
				form.find('.invoicing-type-rel').addClass('hide').filter('.invoicing-type-' + $(this).val() ).removeClass('hide');
				$(window).trigger('resize');
			});

			form.on('change','.payment-method-select',function(e){
				form.find('.payment-method-rel').addClass('hide').filter('.payment-method-rel-' + $(this).val() ).removeClass('hide');
				$(window).trigger('resize');
			});

			$(window).resize(function() {
				if ( $('#header .navbar-toggle').is(':hidden') ) {
					form.find('.summary-column').height( form.find('.form-column').height() );
					if ( form.find('.summary-sticky').hasClass('is-sticky') ) {
						form.find('.summary-sticky').trigger('sticky_kit:recalc');
					} else {
						form.find('.summary-sticky').addClass('is-sticky').stick_in_parent();
					}
				} else {
					form.find('.summary-column').height('auto');
					if ( form.find('.summary-sticky').hasClass('is-sticky') ) {
						form.find('.summary-sticky').removeClass('is-sticky').trigger('sticky_kit:detach');
					}
				}
			}).trigger('resize');

			function updateSummary() {
				var total = 0;
				var decimals = {{ App\Session\Currency::get('decimals') }};
				var currency = '{{ App\Session\Currency::get('code') }}';

				var plan = form.find('.plan-block.selected');
				if ( plan.length ) {
					// Plan related prices
					var opt = plan.find('.payment-interval-select option:selected');
					var price = parseFloat( opt.data().price );
					total += price;
					form.find('.plan-text').text( opt.data().text );
					form.find('.plan-price').text( SITECOMMON.number_format(opt.data().price,decimals,',','.') + ' ' + currency );
					form.find('.transfer-price').text( SITECOMMON.number_format(opt.data().transfer,decimals,',','.') + ' ' + currency );
					// Languages
					if ( opt.data().price > 0 ) {
						form.find('.language-block').addClass('hide');
					} else {
						form.find('.language-block').removeClass('hide');
					}
					// Transfer price
					if ( false && form.find('.transfer-checkbox').is(':checked') ) {
						total += parseFloat( opt.data().transfer );
						form.find('.transfer-row').removeClass('hide');
					} else {
						form.find('.transfer-row').addClass('hide');
					}
					// Payment options
					if ( price > 0 ) {
						form.find('.paymethod-area').removeClass('hide');
					} else {
						form.find('.paymethod-area').addClass('hide');
					}
					// Show
					form.find('.plan-row').removeClass('hide');
					form.find('.items-block').removeClass('hide');
					// Plan level rel
					form.find('.plan-rel').addClass('hide').filter('.plan-rel-level-'+opt.data().level).removeClass('hide');
				} else {
					form.find('.items-block').addClass('hide');
					form.find('.plan-row').addClass('hide');
					form.find('.transfer-row').addClass('hide');
					form.find('.language-block').addClass('hide');
				}

				form.find('.total-price').text( SITECOMMON.number_format(total,decimals,',','.') + ' ' + currency );

				$(window).trigger('resize');
			}
			updateSummary();

		});
	</script>

@endsection
