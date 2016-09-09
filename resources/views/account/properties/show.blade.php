@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#tab-visits .column-property { display: none; }
	</style>

	<div id="admin-properties">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/properties.edit.view') }}</h1>
		<h3 class="page-title">{{ $property->ref }} | {{ $property->title }}</h3>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="{{ (old('current_tab','tab-general') == 'tab-general') ? 'active' : '' }}"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.general') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-lead') ? 'active' : '' }}"><a href="#tab-lead" aria-controls="tab-lead" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.lead') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-appraisal') ? 'active' : '' }}"><a href="#tab-appraisal" aria-controls="tab-appraisal" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.appraisal') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-transaction') ? 'active' : '' }}"><a href="#tab-transaction" aria-controls="tab-transaction" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.transaction') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-documents') ? 'active' : '' }}"><a href="#tab-documents" aria-controls="tab-documents" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.documents') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-reports') ? 'active' : '' }}"><a href="#tab-reports" aria-controls="tab-reports" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.reports') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-logs') ? 'active' : '' }}"><a href="#tab-logs" aria-controls="tab-logs" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.logs') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-employees') ? 'active' : '' }}"><a href="#tab-employees" aria-controls="tab-employees" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.employees') }}</a></li>
			<li role="presentation" class="{{ (old('current_tab') == 'tab-visits') ? 'active' : '' }}"><a href="#tab-visits" aria-controls="tab-visits" role="tab" data-toggle="tab">{{ Lang::get('account/visits.title') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main presentation {{ (old('current_tab','tab-general') == 'tab-general') ? 'active' : '' }}" id="tab-general">
				@if ( $property->catch_current )
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.catch.employee') ) !!}
								{!! Form::text(null, @$property->catch_current->employee->name, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.catch.date') ) !!}
								{!! Form::text(null, $property->catch_current->catch_date->format('d/m/Y'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
							</div>
						</div>
					</div>
					<hr />
					<h3 class="page-title">{{ Lang::get('account/properties.show.property.seller.title') }}</h3>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.seller.name.first') ) !!}
								{!! Form::text(null, $property->catch_current->seller_first_name, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label('seller_last_name', Lang::get('account/properties.show.property.seller.name.last') ) !!}
								{!! Form::text('seller_last_name', @$property->catch_current->seller_last_name, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.seller.email') ) !!}
								{!! Form::text(null, $property->catch_current->seller_email, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.seller.id') ) !!}
								{!! Form::text(null, $property->catch_current->seller_id_card, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.seller.phone') ) !!}
								{!! Form::text(null, $property->catch_current->seller_phone, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.seller.cell') ) !!}
								{!! Form::text(null, $property->catch_current->seller_cell, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<div class="error-placement">
									{!! Form::label(null, Lang::get('account/properties.show.property.price.min') ) !!}
									<div class="input-group">
										@if ( @$property->infocurrency->position == 'before' )
											<div class="input-group-addon">{{ $property->infocurrency->symbol }}</div>
										@endif
										{!! Form::text(null, $property->catch_current->price_min, [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
										@if ( @$property->infocurrency->position == 'after' )
											<div class="input-group-addon">{{ $property->infocurrency->symbol }}</div>
										@endif
									</div>
								</div>
								<div class="help-block">{{ Lang::get('account/properties.show.property.price.min.help') }}</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-2">
							<div class="form-group error-placement">
								{!! Form::label(null, Lang::get('account/properties.show.property.commission') ) !!}
								{!! Form::text(null, "{$property->catch_current->commission}%", [ 'class'=>'form-control', 'readonly'=>'readonly', ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<label class="hidden-xs">&nbsp;</label>
							<div class="text-right">
								<div class="btn-group dropup" role="group">
									<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										{{ Lang::get('account/properties.show.property.catch.actions') }}
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu" style="left: auto; right: 0px;">
										<li><a href="#" data-href="{{ action('Account\PropertiesController@getCatch', [ $property->id, $property->catch_current->id ])}}" class="popup-catch-trigger">{{ Lang::get('account/properties.show.property.catch.actions.edit') }}</a></li>
										<li><a href="#" data-href="{{ action('Account\PropertiesController@getCatchClose', $property->catch_current->id)}}" class="popup-catch-trigger">{{ Lang::get('account/properties.show.property.catch.actions.close') }}</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				@else
					<p>{{ Lang::get('account/properties.show.property.catch.none') }}</p>
					<div class="text-right">
						<a href="#" data-href="{{ action('Account\PropertiesController@getCatch', $property->id)}}" class="btn btn-primary popup-catch-trigger">{{ Lang::get('account/properties.show.property.catch.actions.create') }}</a>
					</div>
				@endif
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-lead') ? 'active' : '' }}" id="tab-lead">
				@if ( !$property->deleted_at )
					<div class="text-right" style="{{ ($property->customers->count() < 1) ? 'margin-bottom: 20px;' : '' }}">
						<a href="#" class="btn btn-default btn-sm add-lead-trigger">{{ Lang::get('account/properties.show.leads.add') }}</a>
					</div>
				@endif
				<div id="leads-list">
					@include('account.properties.show-leads', [ 'customers'=>$property->customers->sortBy('full_name') ])
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-appraisal') ? 'active' : '' }}" id="tab-appraisal">
				@include('account.properties.show-appraisal', [
					'documents' => $property->documents->where('type','appraisal'),
				])
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-transaction') ? 'active' : '' }}" id="tab-transaction">
				@if ( $property->catch_transactions->count() < 1 )
					<p>{{ Lang::get('account/properties.show.transactions.none') }}</p>
				@else
					<table class="table">
						<thead>
							<th>{{ Lang::get('account/properties.show.transactions.date') }}</th>
							<th>{{ Lang::get('account/properties.show.property.catch.status') }}</th>
							<th>{{ Lang::get('account/properties.show.transactions.seller') }}</th>
							<th class="">{{ Lang::get('account/properties.show.transactions.buyer') }}</th>
							<th class="text-nowrap text-right">{{ Lang::get('account/properties.show.transactions.commission') }}</th>
							<th class="text-right">{{ Lang::get('account/properties.show.transactions.price') }}</th>
						</thead>
						<tbody>
							@foreach ($property->catch_transactions as $catch)
								<tr>
									<td>{{ $catch->transaction_date ? $catch->transaction_date->format('d/m/Y') : $catch->catch_date->format('d/m/Y') }}</td>
									<td>
										@if ( $catch->status == 'other' )
											@if ( $catch->reason )
												<div>{{ nl2br($catch->reason) }}</div>
											@else
												{{ Lang::get("account/properties.show.property.catch.status.cancel") }}
											@endif
										@else
											{{ Lang::get("account/properties.show.property.catch.status.{$catch->status}") }}
										@endif
									</td>
									<td>
										@if ( $catch->seller_full_name )
											{{ $catch->seller_full_name }}
											<sup>
												<a href="#seller-modal-{{$catch->id}}" class="transaction-modal-trigger">
													<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
												</a>
											</sup>
											<div id="seller-modal-{{$catch->id}}" class="mfp-white-popup mfp-hide">
												<h4 class="page-title">{{ $catch->seller_full_name }}</h4>
												<p>
													{{ Lang::get('account/properties.show.property.seller.email') }}: 
													<a href="mailto:{{ $catch->seller_email }}" target="_blank">{{ $catch->seller_email }}</a>
												</p>
												@if ( $catch->seller_phone )
													<p>
														{{ Lang::get('account/properties.show.property.seller.phone') }}: 
														{{ $catch->seller_phone }}
													</p>
												@endif
												@if ( $catch->seller_cell )
													<p>
														{{ Lang::get('account/properties.show.property.seller.cell') }}: 
														{{ $catch->seller_cell }}
													</p>
												@endif
												@if ( $catch->seller_id_card )
													<p>
														{{ Lang::get('account/properties.show.property.seller.id') }}: 
														{{ $catch->seller_id_card }}
													</p>
												@endif
											</div>
										@endif
									</td>
									<td>
										@if ( $catch->buyer )
											{{ $catch->buyer->full_name }}
											<sup>
												<a href="#buyer-modal-{{$catch->id}}" class="transaction-modal-trigger">
													<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
												</a>
											</sup>
											<div id="buyer-modal-{{$catch->id}}" class="mfp-white-popup mfp-hide">
												<h4 class="page-title">{{ $catch->buyer->full_name }}</h4>
												<p>
													{{ Lang::get('account/properties.show.property.seller.email') }}: 
													<a href="mailto:{{ $catch->buyer->email }}" target="_blank">{{ $catch->buyer->email }}</a>
												</p>
												@if ( $catch->buyer->phone )
													<p>
														{{ Lang::get('account/properties.show.property.seller.phone') }}: 
														{{ $catch->buyer->phone }}
													</p>
												@endif
											</div>
										@endif
									</td>
									<td class="text-right">
										@if ( $catch->status == 'sold' || $catch->status == 'rent' || $catch->status == 'transfer' )
											{{ price($catch->commission_earned, $property->infocurrency->toArray()) }}
											({{ number_format($catch->commission, 2, ',', '.') }}%)
										@endif
									</td>
									<td class="text-right">
										@if ( $catch->status == 'sold' || $catch->status == 'rent' || $catch->status == 'transfer' )
											{{ price($catch->price_sold, $property->infocurrency->toArray()) }}
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@endif
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-documents') ? 'active' : '' }}" id="tab-documents">
				@include('account.properties.show-documents', [
					'documents' => $property->documents->filter(function ($value, $key) {
						return ($value->type == 'appraisal') ? false : true;
					})
				])
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-reports') ? 'active' : '' }}" id="tab-reports">
				@if ( $property->catch_kpis )
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title text-center">{{ Lang::get('account/properties.show.kpis.leads') }}</h3>
								</div>
								<div class="panel-body text-center">
									{{ number_format($property->catch_kpis->leads_to_close,0,',','.') }}
									/
									{{ number_format($property->catch_kpis->leads_average,2,',','.') }}
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title text-center">{{ Lang::get('account/properties.show.kpis.discount') }}</h3>
								</div>
								<div class="panel-body text-center">
									{{ number_format($property->catch_kpis->discount_to_close,2,',','.') }}%
									/
									{{ number_format($property->catch_kpis->discount_average,2,',','.') }}%
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title text-center">{{ Lang::get('account/properties.show.kpis.days') }}</h3>
								</div>
								<div class="panel-body text-center">
									{{ number_format($property->catch_kpis->days_to_close,0,',','.') }}
									/
									{{ number_format($property->catch_kpis->days_average,2,',','.') }}
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title text-center">{{ Lang::get('account/properties.show.kpis.closed.by') }}</h3>
								</div>
								<div class="panel-body text-center">
									{{ @$property->catch_kpis->seller->name }}
								</div>
							</div>
						</div>
					</div>
				@else
					<p>{{ Lang::get('account/properties.show.kpis.none') }}</p>
				@endif
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-logs') ? 'active' : '' }}" id="tab-logs">
				<div class="alert logs-empty hide">
					{{ Lang::get('account/properties.logs.empty') }}
				</div>
				<table class="table logs-table hide" data-toggle="table" data-sort-name="date" data-sort-order="desc">
					<thead>
						<th data-field="date" data-sortable="true" data-sort-name="_date_data" data-sorter="logDateSorter">{{ Lang::get('account/properties.logs.date') }}</th>
						<th data-field="responsible" data-sortable="false">{{ Lang::get('account/properties.logs.responsible') }}</th>
						<th data-field="action" data-sortable="false">{{ Lang::get('account/properties.logs.action') }}</th>
					</thead>
					<tbody>
						@if ( count($property->logs) > 0 )
							@include('account.properties.logs', [ 'logs'=>$property->logs, 'locale'=>false, 'property'=>$property ])
						@endif
						@if ( count($property->translations) > 0 )
							@foreach ($property->translations as $translation)
								@if ( count($translation->logs) > 0 )
									@include('account.properties.logs', [ 'logs'=>$translation->logs, 'locale'=>lang_text($translation->locale), 'property'=>$property ])
								@endif
							@endforeach
						@endif
					</tbody>
				</table>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ (old('current_tab') == 'tab-employees') ? 'active' : '' }}" id="tab-employees">
				@include('account.properties.tab-managers', [
					'item' => $property,
					'employees' => $property->users()->withRole('employee')->get(),
				])
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{old('current_tab') == 'tab-visits' ? 'active' : '' }}" id="tab-visits">
				@include('account.visits.ajax-tab', [
					'visits_init' => true,
				])
			</div>

		</div>

	</div>

	<script type="text/javascript">
		function logDateSorter(a, b) {
			if (a.date < b.date) return -1;
			if (a.date > b.date) return 1;
			return 0;
		}

		function reloadLeadsList() {
			LOADING.show();
			$('#leads-list').load('{{ action('Account\PropertiesController@getLeads', $property->slug) }}', function(){
				LOADING.hide();
			});
		}

		ready_callbacks.push(function() {
			var cont = $('#admin-properties');

			if ( cont.find('.logs-row').length > 0 ) {
				cont.find('.logs-table').removeClass('hide');
				cont.find('.popup-log-trigger').magnificPopup({
					type:'inline'
				});
				cont.find('.log-detail').each(function(){
					if ( $(this).find('.log-detail-row').length > 0 ) {
						$(this).find('.log-detail-content').removeClass('hide');
					} else {
						$(this).find('.log-detail-empty').removeClass('hide');
					}
				});
			} else {
				cont.find('.logs-empty').removeClass('hide');
			}

			cont.on('click','.popup-catch-trigger', function(e){
				var el = $(this);
				e.preventDefault();
				$.magnificPopup.open({
					items: {
						src: el.data().href
					},
					type: 'iframe',
					modal: true
				});
			});

			cont.find('.transaction-modal-trigger').magnificPopup({
				type: 'inline'
			});

			cont.on('click','.add-lead-trigger',function(e){
				e.preventDefault();
				$.magnificPopup.open({
					items: {
						src: '{{ action('Account\CustomersController@getAddPropertyCustomer',$property->slug) }}'
					},
					type: 'iframe',
					modal: false
				});
			});

			cont.on('click', '.document-delete-trigger', function(e){
				var el = $(this);
				e.preventDefault();
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.documents.delete.warning') ) }}", function (e) {
					if (e) {
						LOADING.show();
						document.location.href = el.data().href;
					}
				});
			});
			cont.find('.document-modal-trigger').magnificPopup({
				type: 'inline',
				modal: true
			});
			cont.find('.document-modal-close').on('click', function(e){
				e.preventDefault();
				$.magnificPopup.close();
			});

			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: '{{ action('Account\Visits\AjaxController@getTab') }}',
				data: {
					property_id: {{ $property->id }}
				},
				success: function(data) {
					if ( data.success ) {
						$('#account-visits-ajax-tab').html( data.html );
					} else {
						$('#account-visits-ajax-tab').html('<div class="alert alert-danger">{{ print_js_string( Lang::get('general.messages.error') ) }}</div>')
					}
				},
				error: function() {
						$('#account-visits-ajax-tab').html('<div class="alert alert-danger">{{ print_js_string( Lang::get('general.messages.error') ) }}</div>')
				}
			});

		});
	</script>

@endsection