@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		.add-range-area { margin-bottom: 20px; text-align: right; }
		.sortable-handler { border: 1px solid #a0a0a0; padding: 20px; cursor: move; background: #fff; margin-bottom: 5px; }
		.pricerange-item-form {}
			.pricerange-item-form .nav { border: none; }
				.pricerange-item-form .nav>li>a,
				.pricerange-item-form .nav>li>a:hover { display: inline-block; padding: 3px 5px; border: 1px solid #a0a0a0; border-left: none; margin-right: 0px; background: #eee; color: #000; }
				.pricerange-item-form .nav>li:first-child>a,
				.pricerange-item-form .nav>li:first-child>a:hover { border-left: 1px solid #a0a0a0; }
				.pricerange-item-form .nav>li.active>a,
				.pricerange-item-form .nav>li.active>a:hover,
				.pricerange-item-form .nav>li.active>a:focus { background: #fff; border-bottom-color: #fff; color: #000; }
				.pricerange-item-form .form-control { outline: none; box-shadow: none !important; }
			.pricerange-item-form .tab-content { padding: 0px; border: none; }
	</style>

	<div id="site-priceranges">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/priceranges.h1') }}</h1>

		{!! Form::hidden('current_tab', $current_tab, [ 'id'=>'current-tab-input' ]) !!}

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="{{ $current_tab == 'sale' ? 'active' : '' }}">
					<a href="#tab-sale" aria-controls="tab-sale" role="tab" data-toggle="tab" data-tab="sale">
						{{ Lang::get('account/priceranges.sale') }}
					</a>
				</li>
				<li role="presentation" class="{{ $current_tab == 'rent' ? 'active' : '' }}">
					<a href="#tab-rent" aria-controls="tab-rent" role="tab" data-toggle="tab" data-tab="rent">
						{{ Lang::get('account/priceranges.rent') }}
					</a>
				</li>
			</ul>

			<div class="tab-content">

				@foreach (['sale','rent'] as $tab)
					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == $tab ? 'active' : '' }}" id="tab-{{ $tab }}">

						<div class="add-range-area">
							<a href="#pricerange-modal-{{ $tab }}" class="btn btn-primary pricerange-modal-trigger">{{ Lang::get('account/priceranges.button.new') }}</a>
						</div>
						@include('account.site.priceranges.form', [
							'form_action' => 'Account\Site\PriceRangesController@postIndex',
							'form_id' => "pricerange-modal-{$tab}",
							'form_title' => Lang::get('account/priceranges.form.title.new'),
							'form_type' => $tab,
						])

						@if ( $priceranges->$tab->count() < 1 )
							<div class="alert alert-info">{{ Lang::get('account/priceranges.empty') }}</div>
						@else
							{!! Form::open([ 'method'=>'GET', 'action'=>[ 'Account\Site\PriceRangesController@getSort', $tab ], 'class'=>'priceranges-sort-form' ]) !!}
								<ul class="sortable-prices list-unstyled">
									@foreach ($priceranges->$tab as $pricerange)
										<li class="sortable-handler">
											<div class="row">
												<div class="col-xs-12">
													<a href="#pricerange-modal-id-{{ $pricerange->id }}" class="btn btn-primary btn-xs pricerange-modal-trigger pull-right">{{ Lang::get('general.edit') }}</a>
													{!! Form::hidden('items[]', $pricerange->id) !!}
													{{ $pricerange->title }}
												</div>
											</div>
										</li>
									@endforeach
								</ul>
								{!! Form::button('Save changes', [ 'type'=>'submit', 'class'=>'btn btn-primary hide' ]) !!}
							{!! Form::close() !!}
							@foreach ($priceranges->$tab as $pricerange)
								@include('account.site.priceranges.form', [
									'form_action' => 'Account\Site\PriceRangesController@postIndex',
									'form_id' => "pricerange-modal-id-{$pricerange->id}",
									'form_title' => Lang::get('account/priceranges.form.title.edit'),
									'form_item' => $pricerange,
								])
							@endforeach
						@endif

					</div>
				@endforeach

			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#site-priceranges');

			// Sort forms
			cont.find('.priceranges-sort-form').each(function(){
				var form = $(this);
				form.validate({
					submitHandler: function(f) {
						LOADING.show();
						$.ajax({
							type: 'GET',
							dataType: 'json',
							url: form.attr('action'),
							data: form.serialize(),
							success: function(data) {
								LOADING.hide();
								if ( data.success ) {
									alertify.success("{{ print_js_string( Lang::get('general.messages.success.saved') ) }}");
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

			cont.find('.pricerange-modal-trigger').each(function(){
				$(this).magnificPopup({
					type: 'inline',
					modal: true
				});
			});
			

			cont.find('.pricerange-item-form').each(function(){
				var form = $(this);
				form.validate({
					ignore: '',
					errorPlacement: function(error, element) {
						element.closest('.error-container').append(error);
					},
					rules: {
						from: {
							required: function(element){
								return form.find('input[name="till"]').val() ? false : true;
							}

						},
						till: {
							required: function(element){
								return form.find('input[name="from"]').val() ? false : true;
							}

						}
					},
					invalidHandler: function(e, validator){
						if ( validator.errorList.length ) {
							var el = $(validator.errorList[0].element);
							form.find('.main-tabs a[href="#' + el.closest(".tab-main").attr('id') + '"]').tab('show');
							if ( el.closest('.tab-locale').length ) {
								form.find('.locale-tabs a[href="#' + el.closest(".tab-locale").attr('id') + '"]').tab('show');
							}
							if ( el.closest('.mailer-tab-pane').length ) {
								form.find('.mailer-tabs a[href="#' + el.closest('.mailer-tab-pane').attr('id') + '"]').tab('show');
							}
						}
					},
					submitHandler: function(f) {
						form.append('<input type="hidden" name="current_tab" value="' + $('#current-tab-input').val() + '" />');

						LOADING.show();
						f.submit();
					}
				});
			});

			cont.find('.main-tabs > li > a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				$('#current-tab-input').val( $(this).data().tab );
			});

			$('body').on('click', '.trigger-popup-close', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			cont.find('.sortable-prices').each(function(){
				$(this).sortable({
					items: '.sortable-handler',
					update: function( event, ui ) {
						$(this).closest('.priceranges-sort-form').submit();
					}
				});
			});


		});
	</script>

@endsection