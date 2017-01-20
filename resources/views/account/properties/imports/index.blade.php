@extends('layouts.account')

@section('account_content')

	<div id="properties-imports">

		@include('common.messages', [ 'dismissible'=>true ])

		<a href="{{ action(('Account\Properties\ImportsController@getUpload')) }}" class="btn btn-primary pull-right">{{ Lang::get('account/properties.imports.create') }}</a>

		<h1 class="page-title">{{ Lang::get('account/properties.imports.h1') }}</h1>

		@if ( $logs->count() < 1 )
			<div class="alert alert-info">
				{{ Lang::get('account/logs.none') }}
			</div>
		@else

			<table class="table">
				<thead>
					<tr>
						<th>{{ Lang::get('account/properties.imports.date') }}</th>
						<th>{{ Lang::get('account/properties.imports.version') }}</th>
						<th class="text-center">{{ Lang::get('account/properties.imports.file') }}</th>
						<th>{{ Lang::get('account/properties.imports.status') }}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($logs as $item)
						@include('account.properties.imports.log-row')
					@endforeach
				</tbody>
			</table>
			{!! drawPagination($logs, Input::except('page')) !!}
		@endif

		<div id="modal-messages">
			@foreach ($logs as $item)
				@if ( @$item->result['messages'] )
					<div class="modal fade" id="modal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-{{ $item->id }}Label">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="modal-{{ $item->hash }}Label">{{ Lang::get('account/logs.response') }}</h4>
								</div>
								<div class="modal-body">
									<ul class="warning-list">
										@foreach ($item->result['messages'] as $k => $m)
											<li>
												{!! $m !!}
											</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					</div>
				@endif

			@endforeach
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#properties-imports');

			cont.on('click', '.reload-log', function(e){
				e.preventDefault();
				window.location.reload(true);
			});

		});
	</script>

@endsection
