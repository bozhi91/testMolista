@if ( @$visits_init )
	<style type="text/css">
		#account-visits-ajax-tab {}
		#account-visits-ajax-tab .pagination-limit-select { display: none; }
	</style>

	<div id="account-visits-ajax-tab">
		<div class="alert">
			<img src="{{ asset('images/loading.gif') }}" alt="" />
		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#account-visits-ajax-tab');

			cont.on('click','.pagination a', function(e){
				e.preventDefault();

				var el = $(this);

				LOADING.show();

				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: el.attr('href'),
					success: function(data) {
						LOADING.hide();
						if ( data.success ) {
							cont.html( data.html );
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

		});
	</script>

@elseif ( empty($visits) || $visits->count() < 1 )
	<div class="alert alert-info">
		{{ Lang::get('account/visits.empty') }}
	</div>

@else
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="column-date">{{ Lang::get('account/visits.date') }}</th>
				<th class="column-customer">{{ Lang::get('account/visits.customer') }}</th>
				<th class="column-agent">{{ Lang::get('account/visits.agent') }}</th>
				<th class="column-property">{{ Lang::get('account/visits.property') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($visits as $visit)
				<tr>
					<td class="column-date">{{ $visit->start_time->format("d/m/Y") }}</td>
					<td class="column-customer">{{ @$visit->customer->full_name }}</td>
					<td class="column-agent">{{ @$visit->users->implode('name',', ') }}</td>
					<td class="column-property">{{ @$visit->properties->implode('title',', ') }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! drawPagination($visits, Input::except('page')) !!}

@endif