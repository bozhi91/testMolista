@if ( empty($item) )
	<div id="associate-table-empty" class="alert alert-info">
		{{ Lang::get('account/properties.employees.empty') }}
	</div>


@else
	<div id="associate-table-empty" class="alert alert-info {{ ( $employees->count() > 0 ) ? 'hide' : '' }}">
		{{ Lang::get('account/properties.employees.empty') }}
	</div>

	@if ( Auth::user()->can('property-edit') && Auth::user()->can('employee-edit'))
		<div class="text-right">
			<a href="#associate-modal" class="btn btn-default btn-sm" id="associate-modal-trigger">{{ Lang::get('account/properties.employees.associate') }}</a>
		</div>
	@endif
	<div id="associate-table" class="{{ ( $employees->count() < 1 ) ? 'hide' : '' }}">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>{{ Lang::get('account/properties.employees.employee') }}</th>
					<th>{{ Lang::get('account/properties.employees.email') }}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@if ( $employees->count() > 0 )
					@include('account.properties.form-employees', [ 'employees'=>$employees, 'property_id'=>$item->id ])
				@endif
			</tbody>
		</table>
	</div>
	<div id="associate-modal" class="mfp-white-popup mfp-hide" data-url="{{ action('Account\PropertiesController@getAssociate', $item->slug) }}">
		<h4 class="page-title">{{ Lang::get('account/properties.employees.associate') }}</h4>
		<div class="form-group">
			<select class="form-control employee-select">
				<option value="">&nbsp;</option>
			</select>
		</div>
		<div class="text-right">
			{!! Form::button( Lang::get('general.continue'), [ 'class'=>'btn btn-warning btn-continue']) !!}
		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var associate_table = $('#associate-table');
			var associate_modal = $('#associate-modal');
			var associate_select = associate_modal.find('.employee-select');

			function updateAssociateSelect() {
				$.ajax({
					dataType: "json",
					url: associate_modal.data().url ,
					success: function(data) {
						if ( data.success && data.items.length > 0 ) {
							associate_select.html('<option value="">&nbsp;</option>');
							$.each(data.items, function(i, item) {
								associate_select.append('<option value="' + item.value + '">' + item.label + '</option>');
							});
						} else {
							associate_select.html('<option value="">{{ print_js_string( Lang::get('account/properties.employees.empty') ) }}</option>');
						}
					},
					error: function() {
						associate_select.html('<option value="">{{ print_js_string( Lang::get('account/properties.employees.empty') ) }}</option>');
					}
				});
			}

			updateAssociateSelect();

			$('#associate-modal-trigger').magnificPopup({
				type: 'inline',
				callbacks: {
					close: function() {
						$('.if-overlay-then-blurred').removeClass('blurred');
					}
				}
			});

			associate_modal.on('click', '.btn-continue', function(e){
				e.preventDefault();
				var user_id = associate_modal.find('.employee-select').val();
				if ( !user_id ) {
					return false;
				}

				$.magnificPopup.close();
				LOADING.show();

				$.ajax({
					dataType: "json",
					url: associate_modal.data().url ,
					data: { id : user_id },
					success: function(data) {
						LOADING.hide();
						if ( data.success ) {
							$('#associate-table-empty').addClass('hide');
							associate_table.removeClass('hide').find('tbody').html( data.html );
							alertify.success("{{ print_js_string( Lang::get('account/properties.employees.associated') ) }}"); 
							updateAssociateSelect();
						} else {
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					},
					error: function() {
						LOADING.hide();
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}"); 
					}
				});
			});

			// Dissociate employee
			associate_table.on('click','.dissociate-trigger',function(e){
				var el = $(this);
				e.preventDefault();
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.employees.dissociate.confirm') ) }}", function (e) {
					if (e) {
						LOADING.show();
						$.ajax({
							dataType: 'json',
							url: el.data().url,
							success: function(data) {
								LOADING.hide();
								if ( data.success ) {
									el.closest('.property-line').remove();
									if ( associate_table.find('.property-line').length < 1 ) {
										associate_table.addClass('hide');
										$('#associate-table-empty').removeClass('hide');
									}
									alertify.success("{{ print_js_string( Lang::get('account/properties.employees.dissociated') ) }}");
									updateAssociateSelect();
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

		});
	</script>


@endif